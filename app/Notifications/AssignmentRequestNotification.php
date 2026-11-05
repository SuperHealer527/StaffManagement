<?php

namespace App\Notifications;

use App\Models\Assignment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssignmentRequestNotification extends Notification
{
    use Queueable;

    public function __construct(public Assignment $assignment, public string $actionUrl) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $event = $this->assignment->event;

        return (new MailMessage)
            ->subject('イベント勤務のご依頼:'.($event->name ?: $event->business_name))
            ->greeting($notifiable->name.' 様')
            ->line($event->event_date->format('Y年n月j日').'のイベント勤務をご依頼します。')
            ->line('集合 '.substr($event->meeting_time, 0, 5).' / 解散 '.substr($event->dismissal_time, 0, 5))
            ->line('場所:'.($event->venue ?: $event->meeting_place ?: '未定'))
            ->line('謝金 '.number_format($this->assignment->fee).'円 / 交通費 '.number_format($this->assignment->transportation).'円')
            ->when(filled($this->assignment->special_notes), fn (MailMessage $message) => $message->line('備考:'.$this->assignment->special_notes))
            ->action('依頼に回答する', $this->actionUrl)
            ->line('リンクから承諾・未定・辞退を回答してください。');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return ['assignment_id' => $this->assignment->id, 'action_url' => $this->actionUrl];
    }
}
