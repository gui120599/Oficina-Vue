<?php

namespace Database\Factories;

use App\Models\Client; // Importar o modelo Client
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str; // Para gerar UUIDs ou strings aleatórias se precisar

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * The name of the corresponding model.
     *
     * @var string
     */
    protected $model = Client::class; // Definir o modelo associado à factory

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'cpf_cnpj' => $this->faker->unique()->numberBetween(10000000000, 99999999999999), // Exemplo simples, pode melhorar a formatação
            'address' => $this->faker->address(),
            'profile_photo_path' => null, // Por enquanto, deixamos nulo. Poderíamos adicionar lógica para fotos de perfil dummy.
            // 'profile_photo_path' => $this->faker->imageUrl(640, 480, 'people', true, 'Faker', true), // Se quiser gerar URLs de imagem dummy
        ];
    }
}