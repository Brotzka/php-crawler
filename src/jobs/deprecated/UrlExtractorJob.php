<?php

namespace Brotzka\PhpCrawler\Jobs;

use Brotzka\PhpCrawler\Models\UncheckedUrl;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Brotzka\PhpCrawler\Models\Url;

/**
 * Extrahiert alle URLs aus einer Website und gibt diese als Auftrag weiter.
 * 
 * Eventuell Unterscheidung nach URLs aus Links innerhalb von a-Tags und allen anderen
 */
class UrlExtractorJob implements ShouldQueue
{

    protected $url;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Url $url)
    {
        $this->url = $url;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->isValidUrl() && $this->isHtml()){
            $links = $this->getUrlsFromHtml();

            foreach($links as $link){
            	$url = new UncheckedUrl();
            	$url->url = $link;
            	$url->save();

            	// Deaktiviert: führt nach kurzer Zeit dazu, dass nur noch URLCheckerJobs durchgeführt werden
            	//UrlCheckerJob::dispatch($url);
            }
        }
    }

    /**
     * Prüft, ob eine URL einen für die Weiterverarbeitung gültigen Statuscode hat
     */
    private function isValidUrl()
    {
        return $this->url->status_code == '200';
    }

	/**
	 * Prüft, ob der Inhalt der Website HTML ist
	 * @return bool
	 */
    private function isHtml()
    {
    	return str_contains($this->url->content_type, 'text/html');
    }

    private function getUrlsFromHtml($all = true, $types = [])
    {
    	$html = file_get_contents($this->url->full_url);
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $dom->preserveWhiteSpace = false;
        $tags = $dom->getElementsByTagName('a');
        $links = [];
        $i = 1;
        foreach($tags as $tag){
        	$links[$i] = $tag->getAttribute('href');
        	$i++;
        }
        $links = $this->extendUrlsWithoutBase($links);
	    return $links;
    }

    private function extendUrlsWithoutBase($links)
    {
		foreach($links as $index => $link){
			if(!str_contains($link, 'http') || !str_contains($link, 'https')){
				$links[$index] = $this->url->scheme . "://" . $this->url->host . $link;
			}
		}
		return $links;
    }
}
