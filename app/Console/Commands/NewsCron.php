<?php

namespace App\Console\Commands;

use App\Helpers\Helper;
use App\Models\News;
use Illuminate\Console\Command;

class NewsCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for get latest news';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        \Log::info("Cron is working fine!");

        #categories
        $categories = Helper::$categories;

        foreach ($categories as $category){

            $url = "https://newsapi.org/v2/top-headlines?apiKey=ebf8e79400204ed1bbce63a94522fcc0&category=$category&language=en";
            //  Initiate curl
            $ch = curl_init();
            // Will return the response, if false it print the response
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
            // Set the url
            curl_setopt($ch, CURLOPT_URL,$url);
            // Execute
            $result=curl_exec($ch);
            // Closing
            curl_close($ch);

            // Will dump a beauty json :3
            $data = json_decode($result, true);
            $insert_data = [];
            foreach ($data['articles'] as $d){
                $data_array['title'] = $d['title'];
                $data_array['source_id'] = $d['source']['id'];
                $data_array['source_name'] = $d['source']['name'] ? preg_replace("/[^a-zA-Z,\"{}:]/", '', $d['source']['name'])  : null;
                $data_array['author'] = $d['author'] ? preg_replace("/[^a-zA-Z,\"{}:]/", '', $d['author']) :null;
                $data_array['category'] = $category;
                $data_array['content'] = $d['content'];
                $data_array['description'] = $d['description'];
                $data_array['url'] = $d['url'];
                $data_array['urlToImage'] = $d['urlToImage'];
                $data_array['publishedAt'] = date('Y-m-d h:m:s', strtotime($d['publishedAt']));
                array_push($insert_data, $data_array);
            }
            News::insert($insert_data);
        }
        return 0;
    }
}
