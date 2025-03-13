<?php

namespace Tests\Feature;

use App\Models\SimpsonsQuote as SimpsonsQuoteModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class SimpsonsQuotesControllerTest extends TestCase
{
    use RefreshDatabase;

    protected string $token;
    protected int $maxAllowedQuoteCount = 5;
    protected array $expectedFields = ['quote', 'character', 'image', 'characterDirection', 'created_at', 'updated_at', 'id'];

    public function test_quotes_are_correctly_returned_and_stored_in_database(): void
    {
        // Get token
        $user = User::factory()->create();
        $user->setAttribute('password', 'password');
        $user->save();

        $response = $this->post('/api/simpsons-quotes/login', ['email' => $user->email, 'password' => 'password']);

        $result = json_decode($response->getContent(), true);
        $this->token = $result['token'];


        // Test initial show of quotes
        $response = $this->get('/api/simpsons-quotes/show', ['Authorization' => 'Bearer ' . $this->token]);

        $response->assertStatus(200);
        $quotes = json_decode($response->getContent(), true);

        $this->assertCount($this->maxAllowedQuoteCount, $quotes);

        $ids = [];
        foreach ($quotes as $quote) {
            // Compare field names with expected field names
            $this->assertTrue(array_diff($this->expectedFields, array_keys($quote)) == []);
            // Add one to each ID for comparison with IDs after next show quotes
            $ids[] = ++$quote['id'];
        }

        // Test second show of quotes
        $response = $this->get('/api/simpsons-quotes/show', ['Authorization' => 'Bearer ' . $this->token]);

        $response->assertStatus(200);
        $quotes = json_decode($response->getContent(), true);

        $newIds = [];
        foreach ($quotes as $quote) {
            // Compare field names with expected field names
            $this->assertTrue(array_diff($this->expectedFields, array_keys($quote)) == []);
            $newIds[] = $quote['id'];
        }

        // IDs from first show incremented by one must match IDs from second show.
        // One new quote has been added.
        // The oldest one has been removed.
        $this->assertTrue(array_diff($ids, $newIds) == []);

        $models = SimpsonsQuoteModel::all();

        $this->assertCount($this->maxAllowedQuoteCount, $models);

        $modelIds = [];
        foreach ($models as $model) {
            $modelIds[] = $model->id;
        }

        // IDs from first show incremented by one must match the IDs of the models in database.
        $this->assertTrue(array_diff($ids, $modelIds) == []);
    }
}
