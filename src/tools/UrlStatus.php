<?php


namespace Brotzka\UrlCrawler\Tools;

use GuzzleHttp\Client;

use Brotzka\PhpCrawler\Exceptions\UrlStatusException;

/**
 * Überprüft den Status einer Url
 * - Status Code
 * - Reason Phrase
 * - Redirects
 */
class UrlStatus {

    private $url;
    private $status = NULL;
    private $options = [
    	"allow_redirects" => false
    ];

	/**
	 * UrlStatus constructor.
	 *
	 * @param $url
	 */
    public function __construct($url)
    {
        $this->url = $url;
    }

	/**
	 * Ermöglicht das zusätzliche Setzen von Abfrage-Optionen
	 * @param $options
	 *
	 * @throws UrlStatusException
	 */
    public function setOptions($options)
    {
    	if(is_array($options)){
    		foreach($options as $key => $value){
    			$this->options[$key] = $value;
		    }
	    } else {
    		throw new UrlStatusException("Als Optionen wurde kein Array übergeben!", 702);
	    }
    }

	/**
	 * Ruft die im Konstruktor übergebene Url auf und holt Status Code und Reason Phrase
	 *
	 * @param string $method
	 */
	public function makeUrlCall($method = "GET")
	{
		$client = new Client();
		try {
			$this->status = $client->request($method, $this->url, $this->options);
		} catch(\Exception $ex){
			$this->status = $ex->getResponse();
		}
	}

	/**
	 * Liefert den Status Code zurück
	 * @return mixed
	 * @throws UrlStatusException
	 */
    public function getStatusCode()
    {
    	if($this->status !== NULL){
		    return $this->status['status_code'];
	    } else {
    		throw new UrlStatusException("Url wurde noch nicht abgerufen!", 701);
	    }
    }

	/**
	 * Liefert die Reason Phrase zurück
	 * @return mixed
	 * @throws UrlStatusException
	 */
    public function getResonPhrase()
    {
    	if($this->status !== NULL){
		    return $this->status['reason_phrase'];
	    } else {
		    throw new UrlStatusException( "Url wurde noch nicht abgerufen", 701 );
	    }
    }
}