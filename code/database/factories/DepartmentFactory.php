<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Department>
 */
class DepartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $departments = [
            'Recursos Humanos',
            'Finanzas',
            'Compras',
            'Ventas',
            'Marketing',
            'Tecnología',
            'Operaciones',
            'Logística',
            'Legal',
            'Administración',
        ];

        $name = fake()->unique()->randomElement($departments);
        $code = strtoupper(substr($name, 0, 3)) . fake()->numberBetween(10, 99);

        return [
            'name' => $name,
            'code' => $code,
            'description' => fake()->sentence(),
            'manager_id' => null, // Se asignará en el seeder
            'budget_limit' => fake()->randomFloat(2, 100000, 5000000), // Valores en ARS (más realistas para Argentina)
            'is_active' => fake()->boolean(90), // 90% activos
            'erp_department_id' => null,
        ];
    }

    /**
     * Indicate that the department is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the department is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
