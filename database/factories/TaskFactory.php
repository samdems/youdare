<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(["truth", "dare"]);

        // Generate appropriate descriptions based on type
        $descriptions =
            $type === "truth"
                ? $this->getTruthDescriptions()
                : $this->getDareDescriptions();

        return [
            "type" => $type,
            "spice_rating" => $this->faker->numberBetween(1, 5),
            "description" => $this->faker->randomElement($descriptions),
            "draft" => $this->faker->boolean(20), // 20% chance of being draft
        ];
    }

    /**
     * Indicate that the task is a draft.
     */
    public function draft(): static
    {
        return $this->state(
            fn(array $attributes) => [
                "draft" => true,
            ],
        );
    }

    /**
     * Indicate that the task is published.
     */
    public function published(): static
    {
        return $this->state(
            fn(array $attributes) => [
                "draft" => false,
            ],
        );
    }

    /**
     * Indicate that the task is a truth.
     */
    public function truth(): static
    {
        return $this->state(
            fn(array $attributes) => [
                "type" => "truth",
                "description" => $this->faker->randomElement(
                    $this->getTruthDescriptions(),
                ),
            ],
        );
    }

    /**
     * Indicate that the task is a dare.
     */
    public function dare(): static
    {
        return $this->state(
            fn(array $attributes) => [
                "type" => "dare",
                "description" => $this->faker->randomElement(
                    $this->getDareDescriptions(),
                ),
            ],
        );
    }

    /**
     * Set a specific spice rating.
     */
    public function withSpiceRating(int $rating): static
    {
        return $this->state(
            fn(array $attributes) => [
                "spice_rating" => $rating,
            ],
        );
    }

    /**
     * Get sample truth descriptions.
     */
    private function getTruthDescriptions(): array
    {
        return [
            "What is your most embarrassing moment?",
            "Have you ever lied to get out of a bad date?",
            "What is your biggest fear?",
            "What is the most childish thing you still do?",
            "Have you ever cheated on a test?",
            "What is your guilty pleasure?",
            "What is the most trouble you got into as a kid?",
            "What secret are you keeping from your parents?",
            "Have you ever stolen anything?",
            "What is your biggest regret?",
            "What is the weirdest dream you have ever had?",
            'Have you ever been caught doing something you shouldn\'t?',
            "What is your worst habit?",
            "What is the most embarrassing thing in your room?",
            "Have you ever spread a rumor?",
        ];
    }

    /**
     * Get sample dare descriptions.
     */
    private function getDareDescriptions(): array
    {
        return [
            "Do 10 pushups right now",
            "Sing the chorus of your favorite song",
            "Dance with no music for 30 seconds",
            "Do your best impression of someone in the group",
            "Speak in an accent for the next 3 rounds",
            "Let someone draw on your face with a pen",
            "Post an embarrassing photo on social media",
            "Call a random contact and sing happy birthday",
            "Do a cartwheel or attempt one",
            "Let the group choose your profile picture for a week",
            "Eat a spoonful of hot sauce",
            "Talk without closing your mouth for the next round",
            "Let someone style your hair",
            "Wear your clothes inside out for the next hour",
            "Do your best animal impression",
        ];
    }
}
