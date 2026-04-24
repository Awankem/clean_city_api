<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Report;
use App\Models\StatusHistory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Categories
        $categories = [
            ['name' => 'Illegal Dumping', 'description' => 'Large piles of trash dumped in non-designated areas.'],
            ['name' => 'Overflowing Bin', 'description' => 'Public trash receptacles that are completely full.'],
            ['name' => 'Hazardous Waste', 'description' => 'Chemicals, batteries, or medical waste found in public space.'],
            ['name' => 'Dead Animals', 'description' => 'Animal remains requiring immediate removal.'],
            ['name' => 'Littering', 'description' => 'Accumulation of small trash in parks or streets.'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['name' => $cat['name']], $cat);
        }

        $allCategories = Category::all();

        // 2. Create Staff Users
        $staff = [
            ['name' => 'Agent Ahmadou', 'email' => 'ahmadou@municipality.gov', 'role' => 'collector'],
            ['name' => 'Officer Brenda', 'email' => 'brenda@municipality.gov', 'role' => 'staff'],
            ['name' => 'Director Samuel', 'email' => 'samuel@clean-city.api', 'role' => 'admin'],
        ];

        foreach ($staff as $s) {
            User::firstOrCreate(
                ['email' => $s['email']],
                array_merge($s, ['password' => Hash::make('password'), 'phone_number' => '+237600000000'])
            );
        }

        $admin = User::where('role', 'admin')->first();
        $citizen = User::factory()->create(['name' => 'Citizen John', 'role' => 'citizen']);

        // 3. Create Reports
        // Center: Douala (4.05, 9.70)
        $statuses = ['pending', 'in_progress', 'resolved'];
        
        for ($i = 0; $i < 30; $i++) {
            $status = $statuses[array_rand($statuses)];
            $lat = 4.05 + (mt_rand(-100, 100) / 5000);
            $lon = 9.70 + (mt_rand(-100, 100) / 5000);

            $report = Report::create([
                'user_id' => $citizen->id,
                'category_id' => $allCategories->random()->id,
                'title' => 'Waste Issue #' . ($i + 101),
                'description' => 'This is a sample waste report generated for dashboard testing. It requires municipal attention.',
                'latitude' => $lat,
                'longitude' => $lon,
                'location' => \DB::raw("ST_GeomFromText('POINT($lon $lat)')"), // For spatial indexing
                'status' => $status,
                'priority_score' => mt_rand(1, 10),
            ]);

            // 4. Create History
            if ($status !== 'pending') {
                StatusHistory::create([
                    'report_id' => $report->id,
                    'changed_by' => $admin->id,
                    'old_status' => 'pending',
                    'new_status' => $status,
                    'note' => 'Action initiated by municipal team.',
                ]);
            }
        }
    }
}
