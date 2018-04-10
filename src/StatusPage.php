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

	private $_aggregateIncidentsTime = 3600;

	public function __construct()
	{
		$this->_httpClient = new GuzzleHttp\Client(['base_uri' => self::URL,
										 			'headers' => [
											 			'Authorization' => 'OAuth '. Config::STATUS_PAGE_API_KEY]]);

	}

	private function _getComponentForAlert(Alert $alert)
	{
		$components = json_decode($this->_httpClient->get(Config::STATUS_PAGE_PAGE_ID .'/components.json')->getBody()->getContents(), true);


		foreach($components as $component)
		{
			if($component['id'] == $alert->component_id)
			{
				return $component;
			}
		}

		return false;

	}

	public function update(Alert $alert)
	{
		$this->_alert = $alert;

		$response = $this->_httpClient->patch(Config::STATUS_PAGE_PAGE_ID .'/components/'. $this->_alert->component_id .'.json', [
			'debug' => self::DEBUG,
			'form_params' => [
				'component[status]' => $this->_alert->status,
			]
		]);

		if(Config::STATUS_PAGE_ENABLE_INCIDENTS && $this->_alert->status == 'major_outage')
		{
			$this->_createIncident($alert);
		}

	}

	public function getUnresolvedIncidents()
	{
		$unresolvedIncidents = json_decode($this->_httpClient->get(Config::STATUS_PAGE_PAGE_ID .'/incidents/unresolved.json')->getBody()->getContents(), true);

		foreach($unresolvedIncidents as $unresolvedIncident)
		{
			try {
				$this->_httpClient->delete(Config::STATUS_PAGE_PAGE_ID .'/incidents/'. $unresolvedIncident['id'] .'.json');
			}
			catch(Exception $e) {}

			sleep(1);

		}
	}

	private function _createIncident(Alert $alert)
	{
		$component     = $this->_getComponentForAlert($alert);
		$componentType = trim(explode(':', $component['name'])[0]);

		$incidents = json_decode($this->_httpClient->get(Config::STATUS_PAGE_PAGE_ID .'/incidents/unresolved.json')->getBody()->getContents(), true);

		foreach($incidents as $incident)
		{
			if (stristr($incident['name'], $componentType) && strtotime($incident['created_at']) + $this->_aggregateIncidentsTime > time())
			{
				$this->_log('Incident for component '. $component['name'] .' not created due to aggregation rule.');
				return false;
			}
		}

		$response = $this->_httpClient->post(Config::STATUS_PAGE_PAGE_ID .'/incidents.json', [
			'debug' => self::DEBUG,
			'form_params' => [
				'incident[name]' 			=> $componentType . ' is having trouble',
				'incident[status]' 			=> 'investigating',
				'incident[component_ids][]'	=> $this->_alert->component_id,
			]
		]);

		$this->_log('Incident for component '. $component['name'] .' created.');

		return true;
	}

	private function _log($msg)
	{
		$log = new \Monolog\Logger('status_page');
		$log->pushHandler(new \Monolog\Handler\StreamHandler('log/status_page.log'));
		$log->addInfo($msg);

	}

}