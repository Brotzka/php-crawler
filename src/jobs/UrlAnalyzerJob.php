<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Brotzka\PhpCrawler\Models\Site;
use Brotzka\PhpCrawler\Models\Url;

use Brotzka\PhpCrawler\Tools\UrlValidator;
use Brotzka\PhpCrawler\Tools\UrlDecomposer;
use Brotzka\PhpCrawler\Tools\UrlStatus;

class UrlAnalyzerJob implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	private $url;

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
		$url = UrlValidator::sanitizeUrl($this->url->full_url);

		if(UrlValidator::isValidUrl($url) && !UrlValidator::urlAlreadyExists($url)){

			$this->setUrlFragments($url);
			$this->assignUrlToSite();
			$this->setUrlStatus();


			// Abschließend speichern
			$this->url->full_url = $url;
			$this->url->save();
		} else {
			// Url ist nicht valide => löschen
			$this->url->delete();
		}
	}

	/**
	 * Holt sich alle Url-Fragmente und speichert diese im Model ab
	 */
	private function setUrlFragments($url)
	{
		$decomposer = new UrlDecomposer($url);

		$this->url->scheme = $decomposer->getScheme();
		$this->url->host = $decomposer->getHost();
		$this->url->port = $decomposer->getPort();
		$this->url->user = $decomposer->getUser();
		$this->url->pass = $decomposer->getPass();
		$this->url->path = $decomposer->getPath();
		$this->url->query = $decomposer->getQuery();
		$this->url->fragment = $decomposer->getFragment();
	}

	/**
	 * Überprüft anhand des Hosts, ob diese Site schon in der Datenbank existiert.
	 * Wenn ja, wird die aktuelle Url dieser Seite angehängt.
	 * Wenn nein, wird die Seite neu erzeugt.
	 */
	private function assignUrlToSite()
	{
		$site = '';

		if(Site::where('host', $this->url->host)->count() >= 1){
			// Site existiert bereits
			$site = Site::where('host', $this->url->host)->first();
		} else {
			// Site existiert noch nicht
			$site = new Site();
			$site->save();
		}

		$this->url->site_id = $site->id;
	}

	/**
	 * Holt und setzt Url-Status
	 */
	private function setUrlStatus()
	{
		$response = new UrlStatus($this->url->full_url);
		$response->makeUrlCall();
	}
}
