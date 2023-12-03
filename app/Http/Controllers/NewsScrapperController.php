<?php

namespace App\Http\Controllers;

use App\Console\Commands\NewsScrapper;
use App\Models\Articles;
use Illuminate\Http\Request;

class NewsScrapperController extends Controller
{

    public function handleAPIData($data, $apiType){
        if($apiType == NewsScrapper::NEWS_API_URL){
            return $this->createNewsAPIData($data);
        }else if($apiType == NewsScrapper::NEW_YORK_TIMES_URL){
            return $this->createNewYorkTimesAPIData($data);
        }
    }

    public function createNewsAPIData($data) {
        if($data['status'] == 'ok'){
            foreach ($data['articles'] ?? [] as $key => $article){
                $data = [
                    'api_source_id' =>  '1',
                    'source' => $article['source']['name'] ?? null,
                    'author' => $article['author'],
                    'title' => $article['title'],
                    'description' => $article['description'],
                    'url' => $article['url'],
                    'published_at' => $article['publishedAt'],
                    'article_json' => json_encode($article)
                ];
                Articles::create($data);
            }
        }
        return ;
    }

    public function createNewYorkTimesAPIData($data){
        if($data['status'] == 'OK'){
            foreach ($data['response']['docs'] ?? [] as $key => $article){
                $data = [
                    'api_source_id' =>  '2',
                    'source' => $article['source'] ?? null,
                    'author' => $article['byline']['original'],
                    'categories' => json_encode(collect($article['keywords'])->where('name', 'subject')->pluck('value')->toArray()),
                    'title' => $article['abstract'],
                    'description' => $article['snippet'],
                    'url' => $article['web_url'],
                    'published_at' => $article['pub_date'],
                    'article_json' => json_encode($article)
                ];
                Articles::create($data);
            }
        }
        return ;
    }

    public function filterData(Request $request){
        $articles = Articles::getFilteredData($request);
        return response()->json(['status' => "OK", 'meta' => ['count' => count($articles)], 'data' => $articles]);
    }
}
