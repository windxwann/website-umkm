<?php

namespace Database\Seeders;

use App\Models\Table;
use Illuminate\Database\Seeder;

class TableSeeder extends Seeder
{
    public function run(): void
    {
        $tables = [
            ['table_number' => 'MEJA 01', 'location' => 'indoor'],
            ['table_number' => 'MEJA 02', 'location' => 'indoor'],
            ['table_number' => 'MEJA 03', 'location' => 'indoor'],
            ['table_number' => 'MEJA 04', 'location' => 'indoor'],
            ['table_number' => 'MEJA 05', 'location' => 'indoor'],
            ['table_number' => 'MEJA 06', 'location' => 'indoor'],
            ['table_number' => 'MEJA 07', 'location' => 'indoor'],
            ['table_number' => 'MEJA 08', 'location' => 'indoor'],
            ['table_number' => 'MEJA 09', 'location' => 'outdoor'],
            ['table_number' => 'MEJA 10', 'location' => 'outdoor'],
            ['table_number' => 'MEJA 11', 'location' => 'outdoor'],
            ['table_number' => 'MEJA 12', 'location' => 'outdoor'],
            ['table_number' => 'MEJA 13', 'location' => 'outdoor'],
            ['table_number' => 'MEJA 14', 'location' => 'outdoor'],
            ['table_number' => 'MEJA 15', 'location' => 'outdoor'],
        ];

        foreach ($tables as $table) {
            Table::create($table);
        }
    }
}