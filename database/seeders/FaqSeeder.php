<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'What is your return policy?',
                'answer' => 'We offer a 30-day return policy for unused and unworn items with their original tags attached. Please contact our support team to initiate a return.',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'question' => 'How long does shipping take?',
                'answer' => 'Standard shipping typically takes 3-5 business days within the Philippines. Delivery times may vary depending on your exact location.',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'question' => 'Do you offer international shipping?',
                'answer' => 'Currently, we only ship within the Philippines. We are working hard to expand our reach internationally in the near future.',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'question' => 'How can I track my order?',
                'answer' => 'Once your order has been shipped, you will receive an email with a tracking number and a link to monitor your package\'s progress.',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'question' => 'Are your products suitable for extreme weather?',
                'answer' => 'Yes, our gear is designed with the great outdoors in mind. We use durable, high-quality materials built to withstand rugged terrains and changing weather conditions.',
                'sort_order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($faqs as $data) {
            Faq::create($data);
        }
    }
}
