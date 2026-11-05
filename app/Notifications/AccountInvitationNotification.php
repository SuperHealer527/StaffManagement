<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountInvitationNotification extends Notification
{
    use Queueable;

    public function __construct(public string $actionUrl) {}

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
            ->subject('ハイクラス業務管理システムへのご招待')
            ->greeting($notifiable->name.' 様')
            ->line('ハイクラス業務管理システムのアカウントが作成されました。')
            ->line('次のボタンから初回パスワードを設定してください。リンクは7日間有効です。')
            ->action('パスワードを設定する', $this->actionUrl)
            ->line('このメールに心当たりがない場合は、操作せず破棄してください。');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return ['action_url' => $this->actionUrl];
    }
}
