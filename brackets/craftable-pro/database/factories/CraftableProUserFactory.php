<?php

namespace Brackets\CraftablePro\Database\Factories;

use Brackets\CraftablePro\Models\CraftableProUser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class CraftableProUserFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = CraftableProUser::class;

    /**
     * @return array|mixed[]
     */
    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->numberBetween(0, 1000).$this->faker->email,
            'password' => Hash::make($this->faker->password),
            'email_verified_at' => $this->faker->dateTime,
            'remember_token' => $this->faker->md5,
            'locale' => $this->faker->randomElement(['en', 'de', 'fr']),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
