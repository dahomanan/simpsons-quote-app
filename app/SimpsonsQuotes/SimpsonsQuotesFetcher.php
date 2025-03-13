<?php

namespace App\SimpsonsQuotes;

use Exception;

class SimpsonsQuotesFetcher
{

    protected static $apiDomain = 'https://thesimpsonsquoteapi.glitch.me';
    protected static $path = '/quotes';

    public static function fetch(int $countOfQuotesToFetch): string
    {
        $query = '';
        if ($countOfQuotesToFetch > 0) {
            $query = '?count=' . $countOfQuotesToFetch;
        }
        $url = self::$apiDomain . self::$path . $query;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['User-Agent: curl']); // Without user agent the API does not work!
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($result === false || $httpStatusCode != 200) {
            throw new Exception('Fetching quotes from Simpsons API failed');
        }

        return $result;
    }
}
