<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use App\Models\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApiHealthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed categories for testing
        Category::create(['name' => 'Garbage Pile', 'icon' => 'delete']);
        Category::create(['name' => 'Blocked Drain', 'icon' => 'water']);
    }

    /**
     * Test User Registration and Login.
     */
    public function test_auth_flow()
    {
        // 1. Register
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['access_token', 'user']);

        // 2. Login
        $loginResponse = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $loginResponse->assertStatus(200)
            ->assertJsonStructure(['access_token']);
    }

    /**
     * Test Categories retrieval.
     */
    public function test_categories_retrieval()
    {
        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
            ->assertJsonCount(2);
    }

    /**
     * Test Report Submission with Images and Spatial Data.
     */
    public function test_report_submission()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $category = Category::first();

        $response = $this->actingAs($user)->postJson('/api/reports', [
            'category_id' => $category->id,
            'description' => 'Test report description',
            'latitude' => 6.4589,
            'longitude' => 3.3515,
            'photos' => [
                UploadedFile::fake()->create('report1.jpg', 100, 'image/jpeg'),
                UploadedFile::fake()->create('report2.jpg', 100, 'image/jpeg'),
            ],

        ]);

        if ($response->status() !== 201) {
            dump($response->json());
        }

        $response->assertStatus(201)
            ->assertJsonPath('description', 'Test report description');


        // Verify storage
        $report = Report::first();
        $this->assertCount(2, $report->images);
        Storage::disk('public')->assertExists($report->images[0]->image_path);
    }

    /**
     * Test Priority Score Calculation.
     */
    public function test_priority_score_calculation()
    {
        $user = User::factory()->create();
        $category = Category::first();

        // 1. Create first report
        $this->actingAs($user)->postJson('/api/reports', [
            'category_id' => $category->id,
            'latitude' => 6.4589,
            'longitude' => 3.3515,
            'photos' => [UploadedFile::fake()->create('p1.jpg', 100, 'image/jpeg')],

        ]);

        // 2. Create second report nearby (within 500m)
        $response = $this->actingAs($user)->postJson('/api/reports', [
            'category_id' => $category->id,
            'latitude' => 6.4590, // Slightly moved
            'longitude' => 3.3516,
            'photos' => [UploadedFile::fake()->create('p2.jpg', 100, 'image/jpeg')],

        ]);

        $report2 = Report::orderBy('id', 'desc')->first();
        
        // Priority score should be 1 (because report1 is nearby)
        // Note: Controller logic is: ($votes * 2) + $nearbyCount
        // Here votes = 0, nearby = 1.
        $this->assertEquals(1, $report2->priority_score);
    }
}
