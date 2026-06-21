<?php

namespace Shearerline\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Shearerline\Models\Enrollment;

class EnrollmentConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $enrollment;

    public function __construct(Enrollment $enrollment)
    {
        $this->enrollment = $enrollment;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '报名成功确认',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'shearerline::emails.enrollment-confirmation',
        );
    }
}
