<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('corporation');
            $table->string('department')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('postal_code', 12)->nullable();
            $table->text('address')->nullable();
            $table->text('notes')->nullable();
            $table->string('proposal_status')->default('uncontacted');
            $table->date('next_action_date')->nullable();
            $table->text('proposal_notes')->nullable();
            $table->foreignId('sales_owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('proposal_status_changed_at')->nullable();
            $table->timestamps();
            $table->index(['proposal_status', 'next_action_date']);
        });

        Schema::create('client_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('position')->nullable();
            $table->string('name');
            $table->string('mobile', 30)->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_retired')->default(false);
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->restrictOnDelete();
            $table->string('business_name');
            $table->string('name')->nullable();
            $table->longText('details')->nullable();
            $table->text('work_content')->nullable();
            $table->date('event_date');
            $table->time('meeting_time');
            $table->time('start_time');
            $table->time('end_time');
            $table->time('dismissal_time');
            $table->unsignedSmallInteger('required_staff');
            $table->string('meeting_place')->nullable();
            $table->string('venue')->nullable();
            $table->json('items')->nullable();
            $table->json('emergency_contacts')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('draft');
            $table->boolean('report_required')->default(false);
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->index(['status', 'event_date']);
        });

        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('status')->default('requested');
            $table->text('special_notes')->nullable();
            $table->unsignedInteger('fee')->default(0);
            $table->unsignedInteger('transportation')->default(0);
            $table->unsignedInteger('reimbursement')->default(0);
            $table->unsignedInteger('withholding_tax')->default(0);
            $table->string('payment_status')->default('waiting');
            $table->string('payment_source')->nullable();
            $table->date('paid_at')->nullable();
            $table->string('seven_day_confirmation')->default('not_sent');
            $table->string('same_day_confirmation')->default('not_sent');
            $table->boolean('is_day_leader')->default(false);
            $table->boolean('is_report_representative')->default(false);
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
            $table->unique(['event_id', 'user_id']);
            $table->index(['status', 'payment_status']);
        });

        Schema::create('report_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->text('question');
            $table->json('options')->nullable();
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->timestamps();
        });

        Schema::create('report_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->json('answers');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->unique(['event_id', 'user_id']);
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('memo')->nullable();
            $table->unsignedInteger('amount');
            $table->timestamps();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('memo')->nullable();
            $table->unsignedInteger('amount');
            $table->string('receipt_path')->nullable();
            $table->timestamps();
        });

        Schema::create('qualifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->date('expires_at')->nullable();
            $table->string('certificate_path')->nullable();
            $table->timestamps();
            $table->index(['expires_at', 'user_id']);
        });

        Schema::create('availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('weekday');
            $table->string('time_slot');
            $table->string('availability')->default('unknown');
            $table->timestamps();
            $table->unique(['user_id', 'weekday', 'time_slot']);
        });

        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->nullableMorphs('notifiable_context', 'notification_context_idx');
            $table->string('type');
            $table->string('channel');
            $table->string('destination')->nullable();
            $table->string('status')->default('pending');
            $table->text('subject')->nullable();
            $table->longText('body')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at']);
        });

        Schema::create('secure_action_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->nullableMorphs('actionable', 'secure_actionable_idx');
            $table->string('purpose');
            $table->string('token_hash', 64)->unique();
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
        });

        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recorded_by')->constrained('users')->restrictOnDelete();
            $table->string('type');
            $table->date('meeting_date');
            $table->string('method');
            $table->string('participants')->nullable();
            $table->longText('content');
            $table->timestamps();
        });

        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('manager_id')->constrained('users')->restrictOnDelete();
            $table->string('type');
            $table->string('method');
            $table->string('location')->nullable();
            $table->json('candidate_times')->nullable();
            $table->dateTime('scheduled_at')->nullable();
            $table->longText('content')->nullable();
            $table->text('next_tasks')->nullable();
            $table->string('next_timing')->nullable();
            $table->timestamps();
        });

        Schema::create('phone_logs', function (Blueprint $table) {
            $table->id();
            $table->dateTime('called_at');
            $table->string('caller_name');
            $table->string('company')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('purpose');
            $table->longText('content');
            $table->foreignId('recipient_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('received_by')->constrained('users')->restrictOnDelete();
            $table->string('status')->default('callback_required');
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->nullableMorphs('auditable', 'audit_auditable_idx');
            $table->string('action');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $tables = ['audit_logs', 'phone_logs', 'interviews', 'meetings', 'secure_action_tokens', 'notification_logs', 'availabilities', 'qualifications', 'expenses', 'invoice_items', 'report_submissions', 'report_questions', 'assignments', 'events', 'client_contacts', 'clients'];

        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
    }
};
