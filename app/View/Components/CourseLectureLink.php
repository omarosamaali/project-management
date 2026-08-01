<?php

namespace App\View\Components;

use App\Models\Course;
use App\Support\MeetingLink;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CourseLectureLink extends Component
{
    public string $href;
    public bool $inApp;
    public ?string $platformLabel;
    public string $classes;
    public string $label;
    public bool $newTab;

    public function __construct(
        public Course $course,
        public $payment = null,
        string $label = 'دخول المحاضرة',
        string $classes = '',
    ) {
        $this->label = $label;
        $this->classes = $classes;
        $info = MeetingLink::analyze($course->online_link);
        $this->platformLabel = $info['platform_label'];

        // Always open the lecture chat page; YouTube embeds there, external opens a new tab from that page.
        if ($payment) {
            $this->href = route('dashboard.my_courses.lecture', $payment->id);
            $this->inApp = true;
            $this->newTab = false;
        } elseif ($course->canModerateChat(auth()->user())) {
            $this->href = route('dashboard.courses.lecture', $course);
            $this->inApp = true;
            $this->newTab = false;
        } else {
            $this->href = $course->online_link ?? '#';
            $this->newTab = true;
            $this->inApp = false;
        }
    }

    public function render(): View|Closure|string
    {
        return view('components.course-lecture-link');
    }
}
