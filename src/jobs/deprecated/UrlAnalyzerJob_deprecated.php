<?php

namespace Brotzka\PhpCrawler\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Brotzka\PhpCrawler\Models\Url;
use Brotzka\PhpCrawler\Models\Site;
use GuzzleHttp\Client;

use Brotzka\PhpCrawler\Jobs\UrlCheckerJob;
use Brotzka\PhpCrawler\Models\UncheckedUrl;

class UrlAnalyzerJob_deprecated implements ShouldQueue {

    use Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels;

    private $url,
            $client,
            $status,
            $response = [
                'status_code' => false,
                'reason_phrase' => false,
                'location' => false,
                'content_type' => false,
                'content_length' => false
    ];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Url $url) {
        $this->url = $url;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $this->client = new Client();
        $this->callStatus();
        $this->getResponse();
        $this->updateUrl();
		$this->setRobots();
        
        if($this->response['status_code'] == '301' || $this->response['status_code'] == '302'){

            if(isset($this->response['location'])){
                $uncheckedUrl = new UncheckedUrl();
                $uncheckedUrl->url = $this->response['location'];
                $uncheckedUrl->save();
                
                UrlCheckerJob::dispatch($uncheckedUrl);
            }
        }
    }

    /**
     * Holt sich den (HTTP) Status einer URL
     */
    private function callStatus() {
        try {
            $this->status = $this->client->request('GET', $this->url->full_url, [
                'allow_redirects' => false
            ]);
        } catch (\Exception $ex) {
            $this->status = $ex->getResponse();
        }
    }

    /**
     * Holt und verarbeitet die Antwort des Requests
     */
    private function getResponse() {
        $this->response['status_code'] = $this->status->getStatusCode();
        $this->response['reason_phrase'] = $this->status->getReasonPhrase();
        
        $this->readHeaders();
    }

    /**
     * Reads the response header and extracts the needed information
     */
    private function readHeaders() {
        $headers = $this->status->getHeaders();

        foreach ($headers as $key => $value) {
            switch ($key) {
                case "Location":
                    $this->response['location'] = $value[0];
                    break;

                case "Content-Type":
                    $this->response['content_type'] = $value[0];
                    break;

                case "Content-Length":
                    $this->response['content_length'] = $value[0];
                    break;

                default:
                    break;
            }
        }
    }
    
    /**
     * Holt sich die meta-robots (wenn verfügbar) und speichert diese ab.
     * Wird nur durchgeführt, wenn HTTP-Statuscode 200 (evtl. erweitern)
     */
    private function setRobots(){
    	if($this->response['status_code'] == '200') {
		    $metas = get_meta_tags( $this->url->full_url );
		    if ( array_key_exists( "robots", $metas ) ) {
			    $robots = str_replace( " ", "", $metas["robots"] );
			    $robots = explode( ",", $robots );
			    foreach ( $robots as $robot ) {
				    switch ( strtolower( $robot ) ) {
					    case "noindex":
					    case "index":
						    $this->url->robots_index = ( strtolower( $robot ) == "index" ) ? true : false;
						    break;
					    case "nofollow":
					    case "follow":
						    $this->url->robots_follow = ( strtolower( $robot ) == "follow" ) ? true : false;
						    break;
				    }
			    }

		    } else {
			    $this->url->robots_index  = true;
			    $this->url->robots_follow = true;
		    }
		    $this->url->save();
	    }
    }
    
    /**
     * Updatet den URL-Eintrag in der Datenbank
     */
    private function updateUrl(){
        $this->url->status_code = $this->response['status_code'];
        $this->url->reason_phrase = $this->response['reason_phrase'];
        $this->url->location = $this->response['location'];
        $this->url->content_type = $this->response['content_type'];
        $this->url->content_length = $this->response['content_length'];
        $this->url->save();
    }

}
