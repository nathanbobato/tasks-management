<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_task_belongs_to_user()
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id
        ]);

        $this->assertTrue($task->user()->exists());
        $this->assertEquals($this->user->id, $task->user->id);
    }

    public function test_task_has_required_fields()
    {
        $task = Task::factory()->create([
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => 'pending',
            'user_id' => $this->user->id
        ]);

        $this->assertNotNull($task->title);
        $this->assertNotNull($task->description);
        $this->assertNotNull($task->status);
        $this->assertNotNull($task->user_id);
    }

    public function test_task_status_must_be_valid()
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending'
        ]);

        $this->assertTrue(in_array($task->status, ['pending', 'in_progress', 'completed']));
    }

    public function test_task_can_be_created_with_valid_data()
    {
        $taskData = [
            'title' => 'New Task',
            'description' => 'Task Description',
            'status' => 'pending',
            'user_id' => $this->user->id
        ];

        $task = Task::create($taskData);

        $this->assertDatabaseHas('tasks', $taskData);
    }

    public function test_task_can_be_updated()
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Original Title'
        ]);

        $task->update(['title' => 'Updated Title']);

        $this->assertEquals('Updated Title', $task->fresh()->title);
    }

    public function test_task_can_be_deleted()
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id
        ]);

        $taskId = $task->id;
        $task->delete();

        $this->assertDatabaseMissing('tasks', ['id' => $taskId]);
    }
}