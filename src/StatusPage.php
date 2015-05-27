<?php
/**
 * Author: Grzesiek
 * Date: 27.05.2015 12:13
 */

use GuzzleHttp\Client,
	GuzzleHttp\Exception\RequestException;;

class StatusPage
{
	const URL = 'https://api.statuspage.io/v1/pages/';
	const DEBUG = false;

	private $_httpClient;
	private $_alert;

	public function __construct(Alert $alert)
	{
		$this->_alert = $alert;
		$this->_httpClient = new GuzzleHttp\Client(['base_uri' => self::URL,
										 			'headers' => [
											 			'Authorization' => 'OAuth '. Config::STATUS_PAGE_API_KEY]]);

	}

	public function update()
	{
		$response = $this->_httpClient->patch(Config::STATUS_PAGE_PAGE_ID .'/components/'. $this->_alert->component_id .'.json', [
			'debug' => self::DEBUG,
			'form_params' => [
				'component[status]' => $this->_alert->status,
			]
		]);

		if($this->_alert->status == 'major_outage')
		{
			$this->_createIncident();
		}

	}

	private function _createIncident()
	{
		$response = $this->_httpClient->post(Config::STATUS_PAGE_PAGE_ID .'/incidents.json', [
			'debug' => self::DEBUG,
			'form_params' => [
				'incident[name]' 			=> $this->_alert->component . ' -  '. $this->_alert->info  .' ['. $this->_alert->status .']',
				'incident[status]' 			=> 'investigating',
				'incident[component_ids][]'	=> $this->_alert->component_id,

			]
		]);
	}

}