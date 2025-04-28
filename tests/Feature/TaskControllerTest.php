<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $otherUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
    }

    public function test_authenticated_user_can_view_tasks()
    {
        Task::factory()->count(3)->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->withHeaders(['Accept' => 'application/json'])
            ->get('/tasks');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_authenticated_user_can_create_task()
    {
        $taskData = [
            'title' => 'New Task',
            'description' => 'Task Description',
            'status' => 'pending'
        ];

        $response = $this->actingAs($this->user)
            ->withHeaders(['Accept' => 'application/json'])
            ->post('/tasks', $taskData);

        $response->assertStatus(201)
            ->assertJsonFragment($taskData);

        $this->assertDatabaseHas('tasks', array_merge($taskData, [
            'user_id' => $this->user->id
        ]));
    }

    public function test_authenticated_user_can_update_task()
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

        $response->assertStatus(200)
            ->assertJsonFragment($updateData);

        $this->assertDatabaseHas('tasks', array_merge($updateData, [
            'id' => $task->id,
            'user_id' => $this->user->id
        ]));
    }

    public function test_authenticated_user_can_delete_task()
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->withHeaders(['Accept' => 'application/json'])
            ->delete("/tasks/{$task->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_user_cannot_update_other_users_task()
    {
        $task = Task::factory()->create([
            'user_id' => $this->otherUser->id
        ]);

        $response = $this->actingAs($this->user)
            ->withHeaders(['Accept' => 'application/json'])
            ->put("/tasks/{$task->id}", [
                'title' => 'Updated Task'
            ]);

        $response->assertStatus(403);
    }

    public function test_user_cannot_delete_other_users_task()
    {
        $task = Task::factory()->create([
            'user_id' => $this->otherUser->id
        ]);

        $response = $this->actingAs($this->user)
            ->withHeaders(['Accept' => 'application/json'])
            ->delete("/tasks/{$task->id}");

        $response->assertStatus(403);
    }

    public function test_task_creation_requires_valid_data()
    {
        $response = $this->actingAs($this->user)
            ->withHeaders(['Accept' => 'application/json'])
            ->post('/tasks', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'description', 'status']);
    }

    public function test_tasks_can_be_filtered_by_status()
    {
        Task::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'status' => 'pending'
        ]);

        Task::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'completed'
        ]);

        $response = $this->actingAs($this->user)
            ->withHeaders(['Accept' => 'application/json'])
            ->get('/tasks?status=pending');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['status' => 'pending']);
    }
}