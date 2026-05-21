<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = Tag::firstOrCreate(['name' => 'Marketing']);
        $tags = Tag::firstOrCreate(['name' => 'Sport']);
        $tags = Tag::firstOrCreate(['name' => 'Health']);
        $tags = Tag::firstOrCreate(['name' => 'War']);
        $tags = Tag::firstOrCreate(['name' => 'Finance']);
        $tags = Tag::firstOrCreate(['name' => 'Economy']);

    }
}
