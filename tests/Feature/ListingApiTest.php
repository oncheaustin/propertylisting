<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListingApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_listing_can_be_created_and_returned(): void
    {
        $agent = User::factory()->create();

        $response = $this->postJson('/api/listings', [
            'title' => 'Modern two-bedroom apartment',
            'price' => 250000,
            'type' => 'rent',
            'bedrooms' => 2,
            'location' => 'Victoria Island, Lagos',
            'latitude' => 6.4281,
            'longitude' => 3.4219,
            'agent_id' => $agent->id,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.title', 'Modern two-bedroom apartment')
            ->assertJsonPath('data.location.latitude', 6.4281);
        $this->assertDatabaseHas('listings', ['title' => 'Modern two-bedroom apartment']);
    }

    public function test_invalid_listing_data_returns_validation_errors(): void
    {
        $response = $this->postJson('/api/listings', [
            'title' => '',
            'price' => -1,
            'type' => 'lease',
            'bedrooms' => -2,
            'latitude' => 100,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['title', 'price', 'type', 'bedrooms', 'location', 'latitude', 'longitude', 'agent_id']);
    }

    public function test_search_filters_and_paginates_by_type_price_bedrooms_and_radius(): void
    {
        $agent = User::factory()->create();
        Listing::factory()->create([
            'agent_id' => $agent->id,
            'title' => 'Nearby rental',
            'type' => 'rent',
            'price' => 200000,
            'bedrooms' => 2,
            'latitude' => 6.4281,
            'longitude' => 3.4219,
        ]);
        Listing::factory()->create([
            'agent_id' => $agent->id,
            'title' => 'Too far away',
            'type' => 'rent',
            'price' => 200000,
            'bedrooms' => 2,
            'latitude' => 7.5,
            'longitude' => 3.4,
        ]);
        Listing::factory()->create([
            'agent_id' => $agent->id,
            'title' => 'Wrong type',
            'type' => 'sale',
            'price' => 200000,
            'bedrooms' => 2,
            'latitude' => 6.4281,
            'longitude' => 3.4219,
        ]);

        $response = $this->getJson('/api/listings/search?type=rent&min_price=100000&max_price=300000&bedrooms=2&latitude=6.4281&longitude=3.4219&radius_km=10&per_page=1');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Nearby rental')
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonPath('meta.per_page', 1);
    }

    public function test_listing_can_be_updated_and_deleted(): void
    {
        $listing = Listing::factory()->create();

        $this->patchJson("/api/listings/{$listing->id}", ['title' => 'Updated title'])
            ->assertOk()
            ->assertJsonPath('data.title', 'Updated title');

        $this->deleteJson("/api/listings/{$listing->id}")->assertNoContent();
        $this->assertDatabaseMissing('listings', ['id' => $listing->id]);
    }
}
