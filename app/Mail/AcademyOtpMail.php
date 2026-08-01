<?php

namespace App\Mail;

use App\Models\Course;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AcademyOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $otp,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'تأكيد الحساب — كود التحقق | أكاديمية إيفورك',
        );
    }

    public function content(): Content
    {
        $base = rtrim(Course::publicBaseUrl(), '/');

        return new Content(
            view: 'emails.academy-otp',
            with: [
                'userName' => $this->user->name,
                'otp' => $this->otp,
                'verifyUrl' => $base.'/verify-otp',
                'logoUrl' => $base.'/assets/images/evorq_academy_logo_white.png',
            ],
        );
    }
}
