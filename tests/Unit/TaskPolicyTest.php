<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use App\Policies\TaskPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskPolicyTest extends TestCase
{
    use RefreshDatabase;

    private TaskPolicy $policy;
    private User $user;
    private User $otherUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new TaskPolicy();
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
    }

    public function test_user_can_view_any_tasks()
    {
        $this->assertTrue($this->policy->viewAny($this->user));
    }

    public function test_user_can_view_their_own_task()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);
        $this->assertTrue($this->policy->view($this->user, $task));
    }

    public function test_user_cannot_view_others_tasks()
    {
        $task = Task::factory()->create(['user_id' => $this->otherUser->id]);
        $this->assertFalse($this->policy->view($this->user, $task));
    }

    public function test_user_can_create_task()
    {
        $this->assertTrue($this->policy->create($this->user));
    }

    public function test_user_can_update_their_own_task()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);
        $this->assertTrue($this->policy->update($this->user, $task));
    }

    public function test_user_cannot_update_others_tasks()
    {
        $task = Task::factory()->create(['user_id' => $this->otherUser->id]);
        $this->assertFalse($this->policy->update($this->user, $task));
    }

    public function test_user_can_delete_their_own_task()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);
        $this->assertTrue($this->policy->delete($this->user, $task));
    }

    public function test_user_cannot_delete_others_tasks()
    {
        $task = Task::factory()->create(['user_id' => $this->otherUser->id]);
        $this->assertFalse($this->policy->delete($this->user, $task));
    }
}