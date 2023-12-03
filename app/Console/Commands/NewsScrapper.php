<?php

namespace App\Console\Commands;

use App\Http\Controllers\NewsScrapperController;
use App\Models\Articles;
use Carbon\Carbon;
use http\Client;
use Illuminate\Console\Command;
use Symfony\Component\VarDumper\VarDumper;
use Weidner\Goutte\GoutteFacade;
use Illuminate\Support\Facades\Http;


class NewsScrapper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:news_scrapper';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command Will get Data from two API sources, after each 10 minutes command will be executed and get data in chunks and save it in mysql';
    const NEWS_API_URL = 'handleNewsApiData';
    const NEW_YORK_TIMES_URL = 'handleNewYorkTimeApiData';
    protected array $api_urls = [
        self::NEWS_API_URL => 'https://newsapi.org/v2/everything?q=all&from=2023-12-01&sortBy=publishedAt&apiKey=22ac936f07dd4fd0bdffc1b1c50c8da3',
        self::NEW_YORK_TIMES_URL => 'https://api.nytimes.com/svc/search/v2/articlesearch.json?q=all&from=2023-12-02&api-key=ytswv1X906H5g9KrbeHB62FpCTFYsGk7'
    ];
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $guzzleClient = new \GuzzleHttp\Client(['verify' => false]);
        $newsAPIPagesCount = Articles::where('api_source_id', 1)->count();
        $newYorkAPIPagesCount = Articles::where('api_source_id', 2)->count();
        $urlExtension = null;
        try {
            foreach ($this->api_urls as $key => $url){
                if($key == self::NEWS_API_URL){
                    $urlExtension = '&page_number=' . $newsAPIPagesCount/100;
                }else if($key == self::NEW_YORK_TIMES_URL){
                    $urlExtension = '&page=' . $newYorkAPIPagesCount/10;
                }
                $this->output->title("This Scraper will fetch data from " . $key );
                $response   = $guzzleClient->get($url . $urlExtension)->getBody()->getContents();
                $response = json_decode($response, true);
                (new NewsScrapperController())->handleAPIData($response, $key);
                $this->output->title("This Processed for " . $key );

            }

//            $this->output->progressStart($pagesCount);
//            $this->output->progressFinish();
        }
        catch (\Exception $exception){
            VarDumper::dump($exception->getMessage());
        }
    }
}
