<?php

namespace App\Mail;

use App\Models\Course;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrainerApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $trainer,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'تمت الموافقة على حسابك كمحاضر — أكاديمية إيفورك',
        );
    }

    public function content(): Content
    {
        $base = Course::publicBaseUrl();
        $loginUrl = $base . '/login';

        return new Content(
            view: 'emails.trainer-approved',
            with: [
                'userName' => $this->trainer->name,
                'loginUrl' => $loginUrl,
                'logoUrl' => $base . '/assets/images/white-logo.png',
                'categoryName' => $this->trainer->courseCategory?->title('ar'),
            ],
        );
    }
}
