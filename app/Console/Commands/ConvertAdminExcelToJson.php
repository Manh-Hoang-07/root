<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ConvertAdminExcelToJson extends Command
{
    protected $signature = 'convert:admin-excel {path : Path to Excel file}';
    protected $description = 'Convert new administrative Excel (no districts) to JSON for seeder';

    public function handle()
    {
        $path = $this->argument('path');

        if (!file_exists($path)) {
            $this->error("❌ File not found: $path");
            return 1;
        }

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $data = [];

        foreach ($rows as $index => $row) {
            // Bỏ dòng tiêu đề
            if ($index < 3) continue;

            $provinceCode = trim((string)($row[2] ?? ''));
            $provinceName = trim((string)($row[3] ?? ''));
            $wardCode = trim((string)($row[8] ?? ''));
            $wardName = trim((string)($row[9] ?? ''));

            if (!$provinceName || !$wardName) continue;

            $data[] = [
                'province_code' => $provinceCode ?: null,
                'province_name' => $provinceName,
                'ward_code' => $wardCode ?: null,
                'ward_name' => $wardName,
            ];
        }

        $outputPath = database_path('seeders/data/vietnam_admins.json');
        File::ensureDirectoryExists(dirname($outputPath));
        File::put($outputPath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->info('✅ JSON file created at: ' . $outputPath);
    }
}
