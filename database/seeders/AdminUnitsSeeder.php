<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class AdminUnitsSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/vietnam_admins.json');

        if (!File::exists($path)) {
            $this->command->error("❌ File not found: {$path}");
            return;
        }

        $data = json_decode(File::get($path), true);
        if (!is_array($data)) {
            $this->command->error("❌ Invalid JSON structure");
            return;
        }

        $this->command->info('🚀 Start seeding provinces and wards...');
        $now = Carbon::now()->toDateTimeString();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('wards')->truncate();
        DB::table('provinces')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $provinces = [];
        $wards = [];

        foreach ($data as $item) {
            $provinces[$item['province_name']] = [
                'code' => $item['province_code'],
                'name' => $item['province_name'],
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $wards[] = [
                'code' => $item['ward_code'],
                'name' => $item['ward_name'],
                'province_name' => $item['province_name'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::transaction(function () use ($provinces, $wards, $now) {
            DB::table('provinces')->insert(array_values($provinces));
            $provinceMap = DB::table('provinces')->pluck('id', 'name')->toArray();

            $insertWards = [];
            foreach ($wards as $w) {
                $province_id = $provinceMap[$w['province_name']] ?? null;
                if (!$province_id) continue;

                $insertWards[] = [
                    'code' => $w['code'],
                    'name' => $w['name'],
                    'province_id' => $province_id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('wards')->insert($insertWards);
        });

        $this->command->info('✅ Seeding completed successfully!');
    }
}
