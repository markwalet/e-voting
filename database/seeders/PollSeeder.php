<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Poll;
use App\Models\PollVote;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Date;

class PollSeeder extends Seeder
{
    use WithFaker;

    /**
     * Run the database seeds.
     * @return void
     */
    public function run()
    {
        // Prep faker
        $this->setUpFaker();

        // Determine poll count
        $count = $this->faker->numberBetween(1, 10);
        for ($i = 0; $i < $count; $i++) {
            $this->createPoll();
        }
    }

    /**
     * Creates a random poll
     * @return void
     */
    public function createPoll(): void
    {
        // make poll
        $poll = Poll::create([
            'title' => "[Test] {$this->faker->sentence}"
        ]);

        // Start poll
        $poll->started_at = Date::now();
        $poll->start_count = $this->faker->numberBetween(30, 150);
        $poll->save();

        // Create a random number of votes
        $votes = $this->faker->numberBetween(10, $poll->start_count);
        $voteOptions = array_keys(PollVote::VALID_VOTES);

        // Create random votes
        for ($i = 0; $i < $votes; $i++) {
            PollVote::create([
                'poll_id' => $poll->id,
                'vote' => $this->faker->randomElement($voteOptions)
            ]);
        }

        // Close poll
        $poll->ended_at = Date::now();
        $poll->end_count = $poll->start_count;
        $poll->save();
    }
}
