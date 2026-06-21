<?php

namespace Shearerline\Tests;

use Shearerline\Facades\Shearerline;

class ShearerlineTest extends TestCase
{
    /** @test */
    public function it_can_access_shearerline_via_facade(): void
    {
        $this->assertInstanceOf(\Shearerline\Shearerline::class, Shearerline::getFacadeRoot());
    }

    /** @test */
    public function it_can_access_shearerline_via_helper(): void
    {
        $this->assertInstanceOf(\Shearerline\Shearerline::class, shearerline());
    }

    /** @test */
    public function it_can_create_and_retrieve_courses(): void
    {
        $teacher = \Shearerline\Models\Teacher::create([
            'name' => 'Test Teacher',
            'email' => 'teacher@test.com',
            'status' => 'active',
        ]);

        $course = Shearerline::createCourse([
            'title' => 'Test Course',
            'description' => 'Test Description',
            'teacher_id' => $teacher->id,
            'price' => 99.99,
            'status' => 'published',
        ]);

        $this->assertEquals('Test Course', $course->title);
        $this->assertEquals(99.99, $course->price);

        $found = Shearerline::getCourse($course->id);
        $this->assertEquals($course->id, $found->id);
    }
}
