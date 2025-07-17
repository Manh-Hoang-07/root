<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->lexify('role_?????'),
            'display_name' => $this->faker->sentence(2),
            'guard_name' => 'web',
            'parent_id' => null,
        ];
    }
} 