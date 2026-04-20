<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Garbage Pile', 
                'icon' => 'delete_outline', 
                'color' => '#E57373' // Red-ish
            ],
            [
                'name' => 'Blocked Drain', 
                'icon' => 'water_damage', 
                'color' => '#64B5F6' // Blue-ish
            ],
            [
                'name' => 'Illegal Dumping', 
                'icon' => 'warning_amber', 
                'color' => '#FFB74D' // Amber/Orange
            ],
            [
                'name' => 'Overflowing Bin', 
                'icon' => 'delete_sweep', 
                'color' => '#81C784' // Green-ish
            ],
            [
                'name' => 'Other', 
                'icon' => 'more_horiz', 
                'color' => '#90A4AE' // Grey-ish
            ],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::create($category);
        }
    }
}
