<?php

namespace App\Notifications;

use App\Models\Interview;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InterviewInvitationNotification extends Notification
{
    use Queueable;

    public function __construct(public Interview $interview, public string $actionUrl) {}

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
        return (new MailMessage)
            ->subject('面談候補日時のご確認')
            ->greeting($notifiable->name.' 様')
            ->line($this->interview->manager->name.'さんとの面談候補日時をお送りします。')
            ->line('次のリンクから都合のよい日時を1つ選択してください。')
            ->action('面談日時を選ぶ', $this->actionUrl);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return ['interview_id' => $this->interview->id, 'action_url' => $this->actionUrl];
    }
}
