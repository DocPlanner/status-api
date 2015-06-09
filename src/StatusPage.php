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
		$incidentName = $this->_alert->component . ($this->_alert->info ? ' - '. $this->_alert->info : '');

		if(isset($this->_alert->aggregate))
		{
			$incidents = json_decode($this->_httpClient->get(Config::STATUS_PAGE_PAGE_ID .'/incidents/unresolved.json')->getBody()->getContents(), true);

			foreach($incidents as $incident)
			{
				if (stristr($incident['name'], $this->_alert->component) && strtotime($incident['created_at'])+$this->_alert->aggregate > time())
				{
					$this->_log('Incident '. $incidentName .' not created due to aggregation rule.');
					return false;
				}
			}
		}

		$response = $this->_httpClient->post(Config::STATUS_PAGE_PAGE_ID .'/incidents.json', [
			'debug' => self::DEBUG,
			'form_params' => [
				'incident[name]' 			=> $incidentName,
				'incident[status]' 			=> 'investigating',
				'incident[component_ids][]'	=> $this->_alert->component_id,

			]
		]);

		$this->_log('Incident '. $incidentName .' created.');

		return true;
	}

	private function _log($msg)
	{
		$log = new \Monolog\Logger('status_page');
		$log->pushHandler(new \Monolog\Handler\StreamHandler('log/status_page.log'));
		$log->addInfo($msg);

	}

}