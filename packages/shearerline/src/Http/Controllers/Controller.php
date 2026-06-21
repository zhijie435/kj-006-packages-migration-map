<?php

namespace Shearerline\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Shearerline\Facades\Shearerline;
use Shearerline\Models\Course;
use Shearerline\Models\Student;
use Shearerline\Models\Teacher;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
