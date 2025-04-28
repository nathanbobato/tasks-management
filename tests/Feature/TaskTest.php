<?php

namespace Tests\Feature;

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

    public function test_user_can_create_a_task()
    {
        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => 'pending',
        ];

        $response = $this->actingAs($this->user)
            ->withHeaders(['Accept' => 'application/json'])
            ->post('/tasks', $taskData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('tasks', array_merge($taskData, [
            'user_id' => $this->user->id
        ]));
    }

    public function test_user_can_update_their_own_task()
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id
        ]);

        $updateData = [
            'title' => 'Updated Task',
            'description' => 'Updated Description',
            'status' => 'completed'
        ];

        $response = $this->actingAs($this->user)
            ->withHeaders(['Accept' => 'application/json'])
            ->put("/tasks/{$task->id}", $updateData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', array_merge($updateData, [
            'id' => $task->id,
            'user_id' => $this->user->id
        ]));
    }

    public function test_user_cannot_update_other_users_tasks()
    {
        $otherUser = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $otherUser->id
        ]);

        $response = $this->actingAs($this->user)
            ->withHeaders(['Accept' => 'application/json'])
            ->put("/tasks/{$task->id}", [
                'title' => 'Updated Task',
                'description' => 'Updated Description',
                'status' => 'completed'
            ]);

        $response->assertStatus(403);
    }
}