<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Brotzka\PhpCrawler\Tools;


use Brotzka\PhpCrawler\Models\Url;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Client;
/**
 * Description of UrlHtmlMetaGrabberTool
 *
 * @author WN00111510
 */
class UrlHtmlMetaGrabberTool {
    private $url;
    
    public function __construct(Url $url) {
        $this->url = $url;
    }
    
    public function getHtmlMeta(){
        $client = new Client();
        $response = $client->get($this->url->full_url);
        
        return $response->getBody();
    }
}
