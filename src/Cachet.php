<?php
/**
 * Author: gellu
 * Date: 10.05.2016 12:34
 */

class Cachet
{
	const URL = 'http://integrations.docplanner.io';
	const DEBUG = false;

	private $_httpClient;

	public function __construct()
	{
		$this->_httpClient = new GuzzleHttp\Client(['base_uri' => self::URL,
													'headers' => [
														'Content-Type'	=> 'application/json',
														'X-Cachet-Token' => Config::CACHET_API_KEY]]);

	}

	public function update(Alert $alert)
	{
		$componentId = $this->getComponentIdByName($alert->name);

		if($componentId)
		{
			$response = $this->_httpClient->put('/api/v1/components/'.$componentId , [
				'debug'       => self::DEBUG,
				'form_params' => [
					'id'   		=> $componentId,
					'status' 	=> ($alert->status == 'up' ? 1 : 4),
				]
			]);
		}
		else
		{
			$response = $this->_httpClient->post('/api/v1/components', [
				'debug'       => self::DEBUG,
				'form_params' => [
					'name'		=> $alert->name,
					'status' 	=> ($alert->status == 'up' ? 1 : 4),
				]
			]);
		}
	}

	private function getComponentIdByName($name)
	{
		$response = $this->_httpClient->get('/api/v1/components', ['debug' => self::DEBUG]);
		foreach(json_decode($response->getBody()->getContents(), true)['data'] as $component)
		{
			if($component['name'] == $name)
			{
				return $component['id'];
			}
		}

		return null;
	}


}