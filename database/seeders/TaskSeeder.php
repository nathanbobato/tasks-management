<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create 5 pending tasks
        Task::factory()
            ->count(5)
            ->pending()
            ->create(['user_id' => $user->id]);

        // Create 3 in progress tasks
        Task::factory()
            ->count(3)
            ->inProgress()
            ->create(['user_id' => $user->id]);

        // Create 2 completed tasks
        Task::factory()
            ->count(2)
            ->completed()
            ->create(['user_id' => $user->id]);
    }
}
