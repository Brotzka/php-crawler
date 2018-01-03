<?php

namespace Brotzka\PhpCrawler\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Carbon\Carbon;
use Brotzka\PhpCrawler\Models\UncheckedUrl;
use Brotzka\PhpCrawler\Models\Site;
use Brotzka\PhpCrawler\Models\Url;
use Brotzka\PhpCrawler\Jobs\UrlAnalyzerJob;

/**
 * Class UrlCheckerJob
 * Holt sich eine ungeprüfte URL und verarbeitet diese weiter
 * @package Brotzka\PhpCrawler\Jobs
 */
class UrlCheckerJob implements ShouldQueue {

    use Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels;

    private $url = [
        'full' => '',
        'fragments' => []
    ];
    private $site;
    private $uncheckedUrl;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(UncheckedUrl $uncheckedUrl) {
        $this->uncheckedUrl = $uncheckedUrl;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        // Url zerlegen
        $this->fragmentUrl($this->uncheckedUrl);

        // Prüfen, ob URL schon existiert
        if ($this->isNewUrl()) {
            // Prüfen, ob Site schon existiert
            if ($this->isNewSite()) {
                $this->createSite();
            } else {
                $this->getSite();
            }

            // URL anlegen
            $urlData = $this->url['fragments'];
            $urlData['full_url'] = $this->url['full'];
            $urlData['site_id'] = $this->site->id;
            $url = Url::create($urlData);
            $this->updateSite();

            // und an Job übergeben
            UrlAnalyzerJob::dispatch($url);
        }

        $this->uncheckedUrl->delete();
    }

    /**
     * Überprüft, ob die aktuelle Url schon vorhanden ist.
     * @return type
     */
    private function isNewUrl() {
        $url = Url::where('full_url', $this->url['full'])->first();
        return ($url === NULL) ? true : false;
    }

    /**
     * Zerlegt eine URL in ihre Bestandteile und speichert diese in $url
     * 
     * @param UncheckedUrl $uncheckedUrl
     */
    private function fragmentUrl(UncheckedUrl $uncheckedUrl) {
        $this->url['full'] = $uncheckedUrl->url;
        $this->url['fragments'] = parse_url($uncheckedUrl->url);
    }

    /**
     * Überprüft, ob schon eine Site zur aktuellen URL existiert
     * @return type
     */
    private function isNewSite() {
        $site = Site::where('host', $this->url['fragments']['host'])->first();
        return ($site === NULL) ? true : false;
    }

    /**
     * Holt die zugehörige Site aus der Datenbank
     */
    private function getSite() {
        $this->site = Site::where('host', $this->url['fragments']['host'])->first();
    }

    /**
     * Updates the Site
     * 
     * @param Site $site
     */
    private function updateSite() {
        // TODO: Verbessern! So ist das nix!
        $site = Site::find($this->site->id);
        $site->touch();
        $site->save();
    }

    /**
     * Erstellt eine neue Site
     */
    private function createSite() {
        $site = new Site();
        $site->host = $this->url['fragments']['host'];
        $site->scheme = $this->url['fragments']['scheme'];
        $site->save();
        $this->site = $site;
    }

}
