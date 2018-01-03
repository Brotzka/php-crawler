<?php

namespace Brotzka\Crawler\Tools;

use Brotzka\PhpCrawler\Models\Url;

class UrlValidator {

    /**
     * Überprüft, ob eine URL valide ist
     * z.B. https://www.google.com
     */
    public static function isValidUrl($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED);
    }

    /**
     * Bringt eine URL in eine valide Form
     */
    public static function sanitizeUrl($url)
    {
        // TODO: Funktionalität ausbauen
        return filter_var($url, FILTER_SANITIZE_URL);
    }

    /**
     * Überprüft, ob diese Url bereits in der Datenbank vorhanden ist
     */
    public static function urlAlreadyExists($url)
    {
        return Url::where('full_url', $url)->count() != 0;
    }
}