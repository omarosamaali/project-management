<?php

namespace App\Mail;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class CourseAnnouncementMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Course $course,
        public string $recipientName,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'دورة تدريبية جديدة — ' . $this->course->name_ar,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.course-announcement',
            with: [
                'userName' => $this->recipientName,
                'courseName' => $this->course->name_ar,
                'courseDescription' => Str::limit(strip_tags((string) $this->course->description_ar), 220),
                'courseUrl' => $this->course->publicUrl(),
                'imageUrl' => $this->course->mainImageUrl(),
                'logoUrl' => Course::publicBaseUrl() . '/assets/images/white-logo.png',
            ],
        );
    }
}
