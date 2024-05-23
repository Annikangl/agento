<?php

namespace Database\Factories;

use App\Models\ScrapperTask;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScrapperTaskFactory extends Factory
{
    protected $model = ScrapperTask::class;

    public function definition(): array
    {
        return [
            'task_start' => $this->faker->randomNumber(),
            'task_last_update' => $this->faker->randomNumber(),
            'task_status' => $this->faker->randomNumber(),
            'task_type' => $this->faker->word(),
            'task_progress' => $this->faker->randomFloat(),
            'task_last_msg' => $this->faker->word(),
            'task_log_path' => $this->faker->word(),
        ];
    }
}
