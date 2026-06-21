<?php

namespace Shearerline\Events;

use Illuminate\Queue\SerializesModels;
use Shearerline\Models\Enrollment;

class StudentEnrolled
{
    use SerializesModels;

    public $enrollment;

    public function __construct(Enrollment $enrollment)
    {
        $this->enrollment = $enrollment;
    }
}
