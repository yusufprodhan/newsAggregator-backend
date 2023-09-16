<?php

namespace App\Helpers;

use App\Models\News;

class Helper
{
    #intialize all categories
    public static array $categories = array('business', 'entertainment', 'general', 'health', 'science', 'sports', 'technology');

    /**
     * @get all author
     * @return array|object
     * @author yusuf
     */
    public static function getAuthor(): array|object
    {
        return News::whereNotNull('author')->groupBy('author')->get('author')->toArray();
    }

    /**
     * @get all source
     * @return array|object
     * @author yusuf
     */
    public static function getSource(): array|object
    {
        return News::whereNotNull('source_name')->groupBy('source_name')->get('source_name')->toArray();
    }
}