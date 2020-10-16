<?php

namespace MichaelDzjap\TwoFactorAuth\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MichaelDzjap\TwoFactorAuth\Models\TwoFactorAuth;

class TwoFactorAuthFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TwoFactorAuth::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'user_id' => config('twofactor-auth.model')::factory(),
        ];
    }
}
