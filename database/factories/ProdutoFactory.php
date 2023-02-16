<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Produto>
 */
class ProdutoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'nome' => fake()->name(),
            'descrição' => fake()->realText(50),
            'tipo_unidade' => false,
            'estoque' => fake()->numberBetween(1,20),
            'preço' => fake()->randomFloat(4,1,20),
            'custo' => fake()->randomFloat(4,1,20),

        ];
    }
}