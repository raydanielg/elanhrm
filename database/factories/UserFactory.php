<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $email = fake()->unique()->safeEmail;
        $email_parts = explode('@', $email);
        $random_letter = chr(mt_rand(97, 122)) . rand(0, 100);

        $new_email = $email_parts[0] . $random_letter.'@' . $email_parts[1];

        return [
            'name' => fake()->name,
            'gender' => 'male',
            'email' => $new_email, /* @phpstan-ignore-line */
        ];
    }

}
