<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Console\Commands\NewsScrapper;
use App\Models\APISource;
use App\Models\Articles;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $data = [
            [
                'source_name' => 'New API',
                'url' => 'https://newsapi.org/v2/everything?q=tesla&from=2023-11-30&sortBy=publishedAt&apiKey=22ac936f07dd4fd0bdffc1b1c50c8da3'
            ],
            [
                'source_name' => 'New York Times',
                'url' => 'https://api.nytimes.com/svc/search/v2/articlesearch.json?q=election&api-key=ytswv1X906H5g9KrbeHB62FpCTFYsGk7'
            ]
        ];
        foreach ($data as $apiUrl){
             APISource::create($apiUrl);
        }
    }
}
