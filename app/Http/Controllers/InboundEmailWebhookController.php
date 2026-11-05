<?php

namespace App\Http\Controllers;

use App\Models\ClientContact;
use App\Models\InboundEmail;
use App\Models\Meeting;
use App\Models\User;
use App\Services\AuditService;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InboundEmailWebhookController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function __invoke(Request $request): JsonResponse
    {
        $body = $request->getContent();
        $secret = (string) config('services.inbound_email.signing_secret');
        abort_if($secret === '', 503, 'Inbound email signing secret is not configured.');

        $expectedSignature = base64_encode(hash_hmac('sha256', $body, $secret, true));
        $receivedSignature = (string) $request->header('X-Inbound-Signature');
        abort_unless($receivedSignature !== '' && hash_equals($expectedSignature, $receivedSignature), 403);

        $validated = $request->validate([
            'message_id' => ['required', 'string', 'max:255'],
            'sender_email' => ['required', 'email', 'max:255'],
            'recipient_emails' => ['required', 'array', 'min:1'],
            'recipient_emails.*' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'text' => ['nullable', 'string'],
            'html' => ['nullable', 'string'],
            'received_at' => ['nullable', 'date'],
        ]);

        $existingEmail = InboundEmail::query()
            ->where('provider_message_id', $validated['message_id'])
            ->first();
        if ($existingEmail !== null) {
            return response()->json(['id' => $existingEmail->id, 'status' => $existingEmail->status]);
        }

        $recipientEmails = collect($validated['recipient_emails'])
            ->map(fn (string $email): string => Str::lower(trim($email)))
            ->unique()
            ->values()
            ->all();
        $allAddresses = [...$recipientEmails, Str::lower(trim($validated['sender_email']))];
        $matchingContact = ClientContact::query()
            ->with('client.salesOwner')
            ->whereNotNull('email')
            ->get()
            ->first(fn (ClientContact $contact): bool => in_array(Str::lower(trim((string) $contact->email)), $allAddresses, true));
        $receivedAt = isset($validated['received_at'])
            ? CarbonImmutable::parse($validated['received_at'])
            : CarbonImmutable::now();
        $bodyExcerpt = Str::limit(trim(strip_tags((string) ($validated['text'] ?? $validated['html'] ?? ''))), 2000, '');

        $inboundEmail = DB::transaction(function () use ($validated, $recipientEmails, $matchingContact, $receivedAt, $bodyExcerpt): InboundEmail {
            $inboundEmail = InboundEmail::query()->create([
                'provider_message_id' => $validated['message_id'],
                'sender_email' => Str::lower(trim($validated['sender_email'])),
                'recipient_emails' => $recipientEmails,
                'subject' => $validated['subject'],
                'body_excerpt' => $bodyExcerpt !== '' ? $bodyExcerpt : null,
                'status' => 'pending',
                'received_at' => $receivedAt,
            ]);

            $client = $matchingContact?->client;
            $recorder = $client?->salesOwner;
            if ($recorder === null || ! $recorder->isEmployee() || $recorder->account_status !== 'active') {
                $recorder = User::query()->where('role', 'employee')->where('account_status', 'active')->first();
            }

            if ($client !== null && $recorder !== null) {
                $meeting = Meeting::query()->create([
                    'client_id' => $client->id,
                    'recorded_by' => $recorder->id,
                    'type' => 'sales',
                    'meeting_date' => $receivedAt->toDateString(),
                    'method' => 'email',
                    'participants' => $validated['sender_email'],
                    'content' => trim($validated['subject']."\n".$bodyExcerpt),
                ]);
                $inboundEmail->update([
                    'client_id' => $client->id,
                    'meeting_id' => $meeting->id,
                    'status' => 'matched',
                ]);
            }

            return $inboundEmail;
        });

        $this->auditService->record($inboundEmail, 'inbound_email_received', null, [
            'status' => $inboundEmail->status,
            'client_id' => $inboundEmail->client_id,
        ]);

        return response()->json(['id' => $inboundEmail->id, 'status' => $inboundEmail->status], 201);
    }
}
