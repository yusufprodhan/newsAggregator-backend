<?php

namespace App\Helpers;

use App\Models\News;

class Helper
{
    #intialize all categories
    public static array $categories = array(
                                                array(
                                                    'name' => 'Business',
                                                    'code'=>'business'
                                                ),
                                                array(
                                                    'name' => 'Entertainment',
                                                    'code'=>'entertainment'
                                                ),
                                                array(
                                                    'name' => 'General',
                                                    'code'=>'general'
                                                ),
                                                array(
                                                    'name' => 'Health',
                                                    'code'=>'health'
                                                ),
                                                array(
                                                    'name' => 'Science',
                                                    'code'=>'science'
                                                ),
                                                array(
                                                    'name' => 'Sports',
                                                    'code'=>'sports'
                                                ),
                                                array(
                                                    'name' => 'Technology',
                                                    'code'=>'technology'
                                                ),
                                            );

    /**
     * @get all author
     * @return array|object
     * @author yusuf
     */
    public static function getAuthor(): array|object
    {
        $author = [];
        $data =  News::whereNotNull('author')->groupBy('author')->get('author')->toArray();
        foreach ($data as $d){
            $array = array(
                'name' => $d["author"],
                'code'=>$d["author"]
            );
            $author[] = $array;
        }
        return $author;
    }

    /**
     * @get all source
     * @return array|object
     * @author yusuf
     */
    public static function getSource(): array|object
    {
        $source = [];
        $data =  News::whereNotNull('source_name')->groupBy('source_name')->get('source_name')->toArray();
        foreach ($data as $d){
            $array = array(
                'name' => $d["source_name"],
                'code'=>$d["source_name"]
            );
            $source[] = $array;
        }
        return $source;
    }
}