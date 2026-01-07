<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        $organizations = [
            [
                'name' => 'Student Council',
                'description' => 'Official student government organization',
                'is_active' => true,
            ],
            [
                'name' => 'Computer Science Department',
                'description' => 'Department of Computer Science and Technology',
                'is_active' => true,
            ],
            [
                'name' => 'Engineering Society',
                'description' => 'Organization for all engineering students',
                'is_active' => true,
            ],
            [
                'name' => 'Business Club',
                'description' => 'Business and entrepreneurship organization',
                'is_active' => true,
            ],
            [
                'name' => 'Arts & Culture Organization',
                'description' => 'Promoting arts and cultural activities',
                'is_active' => true,
            ],
            [
                'name' => 'Sports Committee',
                'description' => 'Managing sports and athletic events',
                'is_active' => true,
            ],
        ];

        foreach ($organizations as $org) {
            Organization::create($org);
        }
    }
}
