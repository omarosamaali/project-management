<?php

namespace App\Mail;

use App\Models\Course;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrainerPendingApprovalMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $trainer,
        public string $reviewUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'طلب محاضر جديد بانتظار الموافقة — أكاديمية إيفورك',
        );
    }

    public function content(): Content
    {
        $base = Course::publicBaseUrl();

        return new Content(
            view: 'emails.trainer-pending-approval',
            with: [
                'trainerName' => $this->trainer->name,
                'trainerEmail' => $this->trainer->email,
                'trainerPhone' => $this->trainer->phone,
                'categoryName' => $this->trainer->courseCategory?->title('ar'),
                'reviewUrl' => $this->reviewUrl,
                'logoUrl' => $base.'/assets/images/white-logo.png',
            ],
        );
    }
}
