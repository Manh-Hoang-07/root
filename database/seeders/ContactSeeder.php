<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contact;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contacts = [
            [
                'name' => 'Nguyễn Văn A',
                'email' => 'nguyenvana@example.com',
                'phone' => '0123456789',
                'subject' => 'Hỏi về sản phẩm',
                'content' => 'Tôi muốn tìm hiểu thêm về sản phẩm của công ty.',
                'status' => 'pending',
                'admin_notes' => null,
                'admin_id' => null,
                'responded_at' => null,
                'created_user_id' => 1,
                'updated_user_id' => 1
            ],
            [
                'name' => 'Trần Thị B',
                'email' => 'tranthib@example.com',
                'phone' => '0987654321',
                'subject' => 'Khiếu nại dịch vụ',
                'content' => 'Tôi không hài lòng với dịch vụ khách hàng.',
                'status' => 'in_progress',
                'admin_notes' => 'Đã liên hệ lại khách hàng',
                'admin_id' => 1,
                'responded_at' => now(),
                'created_user_id' => 1,
                'updated_user_id' => 1
            ],
            [
                'name' => 'Lê Văn C',
                'email' => 'levanc@example.com',
                'phone' => '0369258147',
                'subject' => 'Đề xuất cải tiến',
                'content' => 'Tôi có một số đề xuất để cải thiện website.',
                'status' => 'completed',
                'admin_notes' => 'Đã xử lý và cảm ơn khách hàng',
                'admin_id' => 1,
                'responded_at' => now(),
                'created_user_id' => 1,
                'updated_user_id' => 1
            ]
        ];

        foreach ($contacts as $contactData) {
            Contact::firstOrCreate([
                'email' => $contactData['email'],
                'subject' => $contactData['subject']
            ], $contactData);
        }

        $this->command->info('Contacts seeded successfully!');
    }
}
