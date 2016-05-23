<?php
/**
 * Author: gellu
 * Date: 10.05.2016 12:34
 */

class Cachet
{
	const STATUS_UP 	= 1;
	const STATUS_DOWN 	= 4;

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
		$status = $alert->status == 'up' || $alert->status == 'operational' ? self::STATUS_UP : self::STATUS_DOWN;

		$componentId = $this->getComponentIdByName($alert->name, $alert->group);

		if($componentId)
		{
			$response = $this->_httpClient->put('/api/v1/components/'.$componentId , [
				'debug'       => self::DEBUG,
				'form_params' => [
					'id'   		=> $componentId,
					'status' 	=> $status,
					'link'		=> $alert->url,
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
					'status' 	=> $status,
					'link'		=> $alert->url,
				]
			]);
		}
	}

	private function getComponentIdByName($name, $group)
	{
		$groupId = null;
		if($group)
		{
			return $this->getComponentIdByFromGroup($group, $name);
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

	private function getComponentIdByFromGroup($groupName, $componentName)
	{
		$response = $this->_httpClient->get('/api/v1/components/groups?per_page=1000', ['debug' => self::DEBUG]);
		foreach(json_decode($response->getBody()->getContents(), true)['data'] as $group)
		{
			if($group['name'] == $groupName)
			{
				if(count($group['enabled_components']) > 0)
				{
					foreach ($group['enabled_components'] as $component)
					{
						if($component['name'] == $componentName)
						{
							return $component['id'];
						}
					}
				}
				return null;
			}
		}

		return null;
	}

	private function getGroupIdByName($name)
	{
		$response = $this->_httpClient->get('/api/v1/components/groups?per_page=1000', ['debug' => self::DEBUG]);
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