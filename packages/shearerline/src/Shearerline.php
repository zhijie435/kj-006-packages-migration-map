<?php

namespace Shearerline;

use Shearerline\Contracts\ShearerlineInterface;
use Shearerline\Models\Course;
use Shearerline\Models\Lesson;
use Shearerline\Models\Student;
use Shearerline\Models\Teacher;
use Shearerline\Models\Enrollment;
use Illuminate\Support\Facades\Config;
use Illuminate\Pagination\LengthAwarePaginator;

class Shearerline implements ShearerlineInterface
{
    protected $perPage;

    public function __construct()
    {
        $this->perPage = Config::get('shearerline.pagination.per_page', 15);
    }

    public function getCourses(array $filters = []): LengthAwarePaginator
    {
        $query = Course::query();

        if (!empty($filters['keyword'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['keyword']}%")
                  ->orWhere('description', 'like', "%{$filters['keyword']}%");
            });
        }

        if (!empty($filters['teacher_id'])) {
            $query->where('teacher_id', $filters['teacher_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->with(['teacher', 'lessons'])
            ->latest()
            ->paginate($filters['per_page'] ?? $this->perPage);
    }

    public function getCourse(int $id)
    {
        return Course::with(['teacher', 'lessons', 'enrollments', 'enrollments.student'])
            ->findOrFail($id);
    }

    public function createCourse(array $data)
    {
        return Course::create($data);
    }

    public function updateCourse(int $id, array $data)
    {
        $course = Course::findOrFail($id);
        $course->update($data);
        return $course->fresh();
    }

    public function deleteCourse(int $id): bool
    {
        return Course::findOrFail($id)->delete();
    }

    public function getLessons(int $courseId, array $filters = []): LengthAwarePaginator
    {
        $query = Lesson::where('course_id', $courseId);

        if (!empty($filters['keyword'])) {
            $query->where('title', 'like', "%{$filters['keyword']}%");
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('sort_order')
            ->orderBy('id')
            ->paginate($filters['per_page'] ?? $this->perPage);
    }

    public function getLesson(int $id)
    {
        return Lesson::with('course')->findOrFail($id);
    }

    public function createLesson(array $data)
    {
        return Lesson::create($data);
    }

    public function updateLesson(int $id, array $data)
    {
        $lesson = Lesson::findOrFail($id);
        $lesson->update($data);
        return $lesson->fresh();
    }

    public function deleteLesson(int $id): bool
    {
        return Lesson::findOrFail($id)->delete();
    }

    public function getStudents(array $filters = []): LengthAwarePaginator
    {
        $query = Student::query();

        if (!empty($filters['keyword'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['keyword']}%")
                  ->orWhere('email', 'like', "%{$filters['keyword']}%")
                  ->orWhere('phone', 'like', "%{$filters['keyword']}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->with(['enrollments', 'enrollments.course'])
            ->latest()
            ->paginate($filters['per_page'] ?? $this->perPage);
    }

    public function getStudent(int $id)
    {
        return Student::with(['enrollments', 'enrollments.course'])->findOrFail($id);
    }

    public function createStudent(array $data)
    {
        return Student::create($data);
    }

    public function updateStudent(int $id, array $data)
    {
        $student = Student::findOrFail($id);
        $student->update($data);
        return $student->fresh();
    }

    public function deleteStudent(int $id): bool
    {
        return Student::findOrFail($id)->delete();
    }

    public function getTeachers(array $filters = []): LengthAwarePaginator
    {
        $query = Teacher::query();

        if (!empty($filters['keyword'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['keyword']}%")
                  ->orWhere('email', 'like', "%{$filters['keyword']}%")
                  ->orWhere('phone', 'like', "%{$filters['keyword']}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->with('courses')
            ->latest()
            ->paginate($filters['per_page'] ?? $this->perPage);
    }

    public function getTeacher(int $id)
    {
        return Teacher::with(['courses', 'courses.lessons'])->findOrFail($id);
    }

    public function createTeacher(array $data)
    {
        return Teacher::create($data);
    }

    public function updateTeacher(int $id, array $data)
    {
        $teacher = Teacher::findOrFail($id);
        $teacher->update($data);
        return $teacher->fresh();
    }

    public function deleteTeacher(int $id): bool
    {
        return Teacher::findOrFail($id)->delete();
    }

    public function getEnrollments(array $filters = []): LengthAwarePaginator
    {
        $query = Enrollment::query();

        if (!empty($filters['course_id'])) {
            $query->where('course_id', $filters['course_id']);
        }

        if (!empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->with(['course', 'student'])
            ->latest()
            ->paginate($filters['per_page'] ?? $this->perPage);
    }

    public function enrollStudent(array $data)
    {
        return Enrollment::create($data);
    }

    public function cancelEnrollment(int $id): bool
    {
        return Enrollment::findOrFail($id)->delete();
    }

    public function getCourseStatistics(int $courseId): array
    {
        $course = Course::withCount(['lessons', 'enrollments'])->findOrFail($courseId);

        return [
            'lessons_count' => $course->lessons_count,
            'enrollments_count' => $course->enrollments_count,
            'completed_enrollments_count' => $course->enrollments()->where('status', 'completed')->count(),
            'average_progress' => $course->enrollments()->avg('progress') ?? 0,
        ];
    }
}
