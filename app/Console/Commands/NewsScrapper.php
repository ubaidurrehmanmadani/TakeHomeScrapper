<?php

namespace App\Console\Commands;

use http\Client;
use Illuminate\Console\Command;
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
    protected $description = 'Command description';
    const URL = "https://bankruptcy.gov.sa/eservices/api/recordsearchapi/";

    public function fetchRecord() {
        return Http::get(self::URL);
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $url          = 'https://newsapi.org/v2/everything?q=tesla&from=2023-10-30&sortBy=publishedAt&apiKey=22ac936f07dd4fd0bdffc1b1c50c8da3';
        $guzzleClient = new \GuzzleHttp\Client(['verify' => false]);
        try {
            $response   = $guzzleClient->get($url)->getBody()->getContents();
            dd($response);
            $pages      = $this->_filterSelector('.PagerInfoCell', $response);
            $pagesCount = (explode(' ', $pages[0]))[3];
            $scrappedData      = [];
            $scrappedCompanies = [];
            $count             = 0;
            $this->output->title("This Scraper will fetch data for " . $pagesCount . " pages.");
            $this->output->progressStart($pagesCount);
            for ($i = 1; $i <= $pagesCount; $i++) {
                $url = 'https://eservices.chi.gov.sa/Pages/ClientSystem/Provider/SearchSPs.aspx?IsSearch=1&PageIndex=' . $i;
                $guzzleClient = new \GuzzleHttp\Client(['verify' => false]);
                $response = $guzzleClient->get($url)->getBody()->getContents();
                $crawler = new Crawler($response);
                $crawler->filter('#HCPtbl')->each(function (Crawler $crawler) use (&$scrappedData, &$count, &$i) {
                    $crawler->filter('tr')->each(function (Crawler $row) use (&$scrappedData, &$count, &$i) {
                        $rowData = [];
                        $row->filter('td')->each(function (Crawler $cell) use (&$rowData, &$count) {
                            $rowData[] = $cell->text();
                        });
                        if (count($rowData) == 4 || count($rowData) == 2) {
                            $arrangedData = $this->_rearrangeArray($rowData);
                            if (isset($arrangedData)) {
                                $scrappedData[$count] = $arrangedData;
                                $count++;
                            }
                        }
                    });
                });
                $scrappedData = array_filter($scrappedData);
                foreach (array_chunk($scrappedData, 5) as $key => $company) {
                    $scrappedCompanies[$key] = call_user_func_array('array_merge', $company);
                    $existingCompanies = DahmanCompany::where('reg_no', $scrappedCompanies[$key]['reg_no'])->exists();
                    if (!$existingCompanies)
                        continue;
                    else
                        unset($scrappedCompanies[$key]);
                }
                $this->output->progressAdvance();
            }
            $this->output->progressFinish();
            if (isset($scrappedCompanies)) {
                foreach (array_chunk($scrappedCompanies, 100) as $scrappedCompany)
                    DahmanCompany::insert($scrappedCompany);
                VarDumper::dump("Total " . count($scrappedCompanies) . " has been scrapped from web.");
            } else
                VarDumper::dump("Sorry! No new data has been scrapped");
        }
        catch (\Exception $exception){
            VarDumper::dump($exception->getMessage());
        }
    }
}
