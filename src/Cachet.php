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
		$componentId = $this->getComponentIdByName($alert->name, $alert->group);

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
			$groupId = 0;
			if($alert->group)
			{
				$groupId = $this->getGroupIdByName($alert->group);
				if(!$groupId)
				{
					$response = $this->_httpClient->post('/api/v1/components/groups', [
						'debug'       => self::DEBUG,
						'form_params' => [
							'name'		=> $alert->group,
							'collapsed'	=> '1',
							'order'		=> time(),
						]
					]);

					$groupId = json_decode($response->getBody()->getContents(), true)['data']['id'];
				}
			}

			$response = $this->_httpClient->post('/api/v1/components', [
				'debug'       => self::DEBUG,
				'form_params' => [
					'name'		=> $alert->name,
					'group_id'	=> $groupId,
					'status' 	=> ($alert->status == 'up' ? 1 : 4),
				]
			]);
		}
	}

	private function getComponentIdByName($name, $group)
	{
		$groupId = null;
		if($group)
		{
			$groupId = $this->getGroupIdByName($group);
		}

		$response = $this->_httpClient->get('/api/v1/components', ['debug' => self::DEBUG]);
		$components = json_decode($response->getBody()->getContents(), true)['data'];
		if(count($components) < 1)
		{
			return null;
		}

		foreach($components as $component)
		{
			if($component['name'] == $name && $component['group_id'] == (int) $groupId)
			{
				return $component['id'];
			}
		}

		return null;
	}

	private function getGroupIdByName($name)
	{
		$response = $this->_httpClient->get('/api/v1/components/groups', ['debug' => self::DEBUG]);
		foreach(json_decode($response->getBody()->getContents(), true)['data'] as $group)
		{
			if($group['name'] == $name)
			{
				return $group['id'];
			}
		}

		return null;
	}


}