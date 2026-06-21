<?php

namespace Shearerline\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Pagination\LengthAwarePaginator getCourses(array $filters = [])
 * @method static mixed getCourse(int $id)
 * @method static mixed createCourse(array $data)
 * @method static mixed updateCourse(int $id, array $data)
 * @method static bool deleteCourse(int $id)
 * @method static \Illuminate\Pagination\LengthAwarePaginator getLessons(int $courseId, array $filters = [])
 * @method static mixed getLesson(int $id)
 * @method static mixed createLesson(array $data)
 * @method static mixed updateLesson(int $id, array $data)
 * @method static bool deleteLesson(int $id)
 * @method static \Illuminate\Pagination\LengthAwarePaginator getStudents(array $filters = [])
 * @method static mixed getStudent(int $id)
 * @method static mixed createStudent(array $data)
 * @method static mixed updateStudent(int $id, array $data)
 * @method static bool deleteStudent(int $id)
 * @method static \Illuminate\Pagination\LengthAwarePaginator getTeachers(array $filters = [])
 * @method static mixed getTeacher(int $id)
 * @method static mixed createTeacher(array $data)
 * @method static mixed updateTeacher(int $id, array $data)
 * @method static bool deleteTeacher(int $id)
 * @method static \Illuminate\Pagination\LengthAwarePaginator getEnrollments(array $filters = [])
 * @method static mixed enrollStudent(array $data)
 * @method static bool cancelEnrollment(int $id)
 * @method static array getCourseStatistics(int $courseId)
 *
 * @see \Shearerline\Shearerline
 */
class Shearerline extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'shearerline';
    }
}
