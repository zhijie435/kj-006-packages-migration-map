<?php

namespace Shearerline\Listeners;

use Shearerline\Events\CourseCompleted;
use Illuminate\Support\Facades\Log;

class SendCourseCompletedNotification
{
    public function handle(CourseCompleted $event): void
    {
        Log::info('课程完成', [
            'student_id' => $event->enrollment->student_id,
            'course_id' => $event->enrollment->course_id,
            'enrollment_id' => $event->enrollment->id,
        ]);
    }
}
