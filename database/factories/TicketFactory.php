<?php

namespace Database\Factories;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Ticket::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $createdAt = $this->faker->dateTimeBetween('-1 year', 'now');

        return [
            'subject'     => $this->faker->text(),
            'content'     => $this->faker->paragraphs(3, true),
            'priority_id' => $this->faker->numberBetween(0, 4),
            'created_at'  => $createdAt,
            'updated_at'  => $this->faker->dateTimeBetween($createdAt, 'now'),
        ];
    }
}
