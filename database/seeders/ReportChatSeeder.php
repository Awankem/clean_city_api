<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Report;
use App\Models\ChatMessage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ReportChatSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create an admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // 2. Create a normal user
        $user = User::firstOrCreate(
            ['email' => 'citizen@example.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('password'),
                'role' => 'citizen',
            ]
        );

        // 3. Ensure we have at least one category
        $category = Category::firstOrCreate(
            ['name' => 'Garbage Collection'],
            [
                'description' => 'Uncollected trash or illegal dumping',
                'icon' => 'delete_outline',
                'color' => '#E53935'
            ]
        );

        // 4. Create a sample report for the user
        $report = Report::firstOrCreate(
            ['title' => 'Overflowing Bin on Main Street'],
            [
                'user_id' => $user->id,
                'category_id' => $category->id,
                'description' => 'The public trash bin near the central park entrance has been overflowing for days. People are throwing trash on the ground.',
                'location' => \Illuminate\Support\Facades\DB::raw("ST_GeomFromText('POINT(-74.0060 40.7128)', 4326)"),
                'location_name' => 'Central Park',
                'latitude' => 40.7128,
                'longitude' => -74.0060,
                'status' => 'in_progress',
                'priority_score' => 10,
            ]
        );

        // 5. Seed some chat messages for this report
        ChatMessage::truncate(); // Clear existing chat messages

        $messages = [
            [
                'report_id' => $report->id,
                'sender_id' => $user->id,
                'message' => 'Hello, is there any update on when this will be cleaned up? It is starting to smell.',
                'is_read' => true,
                'created_at' => Carbon::now()->subDays(2),
            ],
            [
                'report_id' => $report->id,
                'sender_id' => $admin->id,
                'message' => 'Hi John, thank you for reporting this. We have dispatched a cleaning crew. They should arrive tomorrow morning.',
                'is_read' => true,
                'created_at' => Carbon::now()->subDays(1)->subHours(10),
            ],
            [
                'report_id' => $report->id,
                'sender_id' => $user->id,
                'message' => 'Great! Thank you for the quick response. I will let you know if I see them.',
                'is_read' => true,
                'created_at' => Carbon::now()->subDays(1)->subHours(8),
            ],
            [
                'report_id' => $report->id,
                'sender_id' => $admin->id,
                'message' => 'The crew reported that they completed the cleanup. Can you confirm if everything looks good on your end?',
                'is_read' => false,
                'created_at' => Carbon::now()->subHours(2),
            ],
        ];

        foreach ($messages as $msg) {
            ChatMessage::create($msg);
        }
        
        $this->command->info('Seeded users, a sample report, and 4 chat messages successfully!');
    }
}
