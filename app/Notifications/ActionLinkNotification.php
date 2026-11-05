<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ActionLinkNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $subject,
        public string $body,
        public ?string $actionUrl = null,
        public string $actionLabel = '確認する',
    ) {}

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
        $message = (new MailMessage)
            ->subject($this->subject)
            ->greeting($notifiable->name.' 様')
            ->line($this->body);

        if ($this->actionUrl !== null) {
            $message->action($this->actionLabel, $this->actionUrl);
        }

        return $message->line('ハイクラス業務管理システム');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'subject' => $this->subject,
            'body' => $this->body,
            'action_url' => $this->actionUrl,
        ];
    }
}
