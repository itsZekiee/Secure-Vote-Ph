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
                'status' => 'active',
            ],
            [
                'name' => 'Computer Science Department',
                'description' => 'Department of Computer Science and Technology',
                'status' => 'active',
            ],
            [
                'name' => 'Engineering Society',
                'description' => 'Organization for all engineering students',
                'status' => 'active',
            ],
            [
                'name' => 'Business Club',
                'description' => 'Business and entrepreneurship organization',
                'status' => 'active',
            ],
            [
                'name' => 'Arts & Culture Organization',
                'description' => 'Promoting arts and cultural activities',
                'status' => 'active',
            ],
            [
                'name' => 'Sports Committee',
                'description' => 'Managing sports and athletic events',
                'status' => 'active',
            ],
        ];

        foreach ($organizations as $org) {
            Organization::create($org);
        }
    }
}
