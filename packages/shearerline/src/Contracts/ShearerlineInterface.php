<?php

namespace Shearerline\Contracts;

interface ShearerlineInterface
{
    public function getCourses(array $filters = []);

    public function getCourse(int $id);

    public function createCourse(array $data);

    public function updateCourse(int $id, array $data);

    public function deleteCourse(int $id);

    public function getLessons(int $courseId, array $filters = []);

    public function getLesson(int $id);

    public function createLesson(array $data);

    public function updateLesson(int $id, array $data);

    public function deleteLesson(int $id);

    public function getStudents(array $filters = []);

    public function getStudent(int $id);

    public function createStudent(array $data);

    public function updateStudent(int $id, array $data);

    public function deleteStudent(int $id);

    public function getTeachers(array $filters = []);

    public function getTeacher(int $id);

    public function createTeacher(array $data);

    public function updateTeacher(int $id, array $data);

    public function deleteTeacher(int $id);

    public function getEnrollments(array $filters = []);

    public function enrollStudent(array $data);

    public function cancelEnrollment(int $id);
}
