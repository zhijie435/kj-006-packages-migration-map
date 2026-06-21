<?php

namespace Shearerline\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Shearerline\Models\Enrollment;

class EnrollmentCompleted extends Notification
{
    use Queueable;

    public $enrollment;

    public function __construct(Enrollment $enrollment)
    {
        $this->enrollment = $enrollment;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('您的课程已完成。')
            ->action('查看课程', url('/shearerline/courses/' . $this->enrollment->course_id))
            ->line('感谢您的学习！');
    }
}
