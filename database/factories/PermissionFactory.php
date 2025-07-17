<?php

namespace Database\Factories;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;

class PermissionFactory extends Factory
{
    protected $model = Permission::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->lexify('permission_?????'),
            'display_name' => $this->faker->sentence(2),
            'guard_name' => 'web',
            'parent_id' => null,
        ];
    }
} 