<?php

namespace Shearerline\Listeners;

use Shearerline\Events\StudentEnrolled;
use Illuminate\Support\Facades\Log;

class SendEnrollmentNotification
{
    public function handle(StudentEnrolled $event): void
    {
        Log::info('学生报名成功', [
            'student_id' => $event->enrollment->student_id,
            'course_id' => $event->enrollment->course_id,
            'enrollment_id' => $event->enrollment->id,
        ]);
    }
}
