<?php
/**
 * Created by PhpStorm.
 * User: fabs
 * Date: 24.11.17
 * Time: 04:50
 */

namespace Brotzka\PhpCrawler\Tools;

use GuzzleHttp\Client;
use Brotzka\PhpCrawler\Models\Url;

use Brotzka\PhpCrawler\Tools\UrlExceptionHandler;

class UrlGrabber {
	protected $url;
	protected $status;
	protected $response = [
		'status_code'    => false,
		'reason_phrase'  => false,
		'location'       => false,
		'content_type'   => false,
		'content_length' => false
	];

	/**
	 * UrlGrabber constructor.
	 *
	 * @param Url $url
	 *
	 * @throws \NoValidUrlException
	 */
	public function __construct( Url $url ) {
		if(!empty($url->getUrl() && UrlValidator::isValidUrl($url->getUrl()))){
			$this->url = $url;
		} else {
			throw new \NoValidUrlException("Ungültige URL übergeben!", 666);
		}
	}

	/**
	 * @return array
	 */
	public function getStatusCode() {
		$this->makeRequest();
		return $this->response;
	}

	private function makeRequest() {
		//Todo: Überprüfen, ob gültige URL vorliegt
		$client = new Client();
		try {
			$this->status  = $client->request( 'GET', $this->url->getUrl(), [
				'allow_redirects' => false
			] );

		} catch ( \Exception $e ) {
			$this->status = $e->getResponse();
		}
		$this->createResponse();
	}

	/**
	 * Fills the response object
	 */
	private function createResponse() {
		$this->response['status_code']   = $this->status->getStatusCode();
		$this->response['reason_phrase'] = $this->status->getReasonPhrase();

		$this->readHeaders();
	}

	/**
	 * Reads the response header and extracts the needed information
	 */
	private function readHeaders() {
		$headers = $this->status->getHeaders();

		foreach ( $headers as $key => $value ) {
			switch ( $key ) {
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

}