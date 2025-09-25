<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CounselingCategory;

class CounselingCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Academic Concerns',
                'description' => 'Issues related to academic performance, study habits, and learning difficulties.',
                'counselor_id' => 1,
                'status' => 'active',
                'admin_feedback' => 'Approved by admin',
            ],
            [
                'name' => 'Career Guidance',
                'description' => 'Exploration of career choices, job preparation, and future planning.',
                'counselor_id' => 1,
                'status' => 'active',
                'admin_feedback' => 'Approved by admin',
            ],
            [
                'name' => 'Personal Issues',
                'description' => 'Counseling for stress, self-esteem, and personal development.',
                'counselor_id' => 1,
                'status' => 'active',
                'admin_feedback' => 'Approved by admin',
            ],
            [
                'name' => 'Family Concerns',
                'description' => 'Support for family-related challenges and relationship concerns.',
                'counselor_id' => 1,
                'status' => 'active',
                'admin_feedback' => 'Approved by admin',
            ],
            [
                'name' => 'Peer Relationships',
                'description' => 'Guidance on managing friendships, conflicts, and social challenges.',
                'counselor_id' => 1,
                'status' => 'active',
                'admin_feedback' => 'Approved by admin',
            ],
        ];

        foreach ($categories as $category) {
            CounselingCategory::create($category);
        }
    }
}
