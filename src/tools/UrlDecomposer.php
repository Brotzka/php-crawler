<?php

namespace Brotzka\PhpCrawler\Tools;

use Brotzka\PhpCrawler\Tools\UrlValidator;
use Brotzka\PhpCrawler\Models\Url;
use Brotzka\PhpCrawler\Exceptions\DecomposeException;

class UrlDecomposer {
    private $url_fragments;
    private $url;

    public function __construct($url)
    {
        if(UrlValidator::isValidUrl($url)){
            $this->url = $url;
            $this->url_fragments = parse_url($this->url);
        } else {
            throw new DecomposeException("UrlDecomposer wurde keine valide URL übergeben!", 900);
        }
        
    }

    /**
     * Gibt alle Fragmente als Array zurück
     */
    public function getAllFragments()
    {
        return $this->url_fragments;
    }

    public function getScheme()
    {
        return isset($this->url_fragments['scheme']) ? $this->url_fragments['scheme'] : NULL;
    }

    public function getHost()
    {
        return isset($this->url_fragments['host']) ? $this->url_fragments['host'] : NULL;
    }

    public function getPort()
    {
        return isset($this->url_fragments['port']) ? $this->url_fragments['port'] : NULL;
    }

    public function getUser()
    {
        return isset($this->url_fragments['user']) ? $this->url_fragments['user'] : NULL;
    }

    public function getPass()
    {
        return isset($this->url_fragments['pass']) ? $this->url_fragments['pass'] : NULL;
    }

    public function getPath()
    {
        return isset($this->url_fragments['path']) ? $this->url_fragments['path'] : NULL;
    }

    public function getQuery()
    {
        return isset($this->url_fragments['query']) ? $this->url_fragments['query'] : NULL;
    }

    public function getFragment()
    {
        return isset($this->url_fragments['fragment']) ? $this->url_fragments['fragment'] : NULL;
    }
}
