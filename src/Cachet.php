<?php
/**
 * Author: gellu
 * Date: 10.05.2016 12:34
 */

use Damianopetrungaro\CachetSDK\CachetClient;
use Damianopetrungaro\CachetSDK\Components\ComponentActions;
use Damianopetrungaro\CachetSDK\Components\ComponentFactory;
use Damianopetrungaro\CachetSDK\Groups\GroupActions;
use Damianopetrungaro\CachetSDK\Groups\GroupFactory;

class Cachet
{
	const STATUS_UP 	= 1;
	const STATUS_DOWN 	= 4;

	const URL = 'http://integrations.docplanner.io';
	const DEBUG = false;

	private $_httpClient;
	/** @var CachetClient */
	private $client;
	/** @var ComponentActions  */
	private $componentManager;
	/** @var GroupActions  */
	private $groupManager;

	public function __construct()
	{
		$this->client 			= new CachetClient('http://integrations.docplanner.io/api/v1/', Config::CACHET_API_KEY);
		$this->componentManager = ComponentFactory::build($this->client);
		$this->groupManager 	= GroupFactory::build($this->client);
	}

	/**
	 * @param Alert $alert
	 */
	public function updateComponent(Alert $alert)
	{
		$componentId = $this->getComponentIdByName($alert->name, $alert->group);

		if($componentId)
		{
			$this->componentManager->updateComponent($componentId, ['status'	=> $this->translateStatus($alert),
																	'link' 		=> $alert->url]);
		}
		else
		{
			$this->createComponent($alert);
		}
	}

	/**
	 * @param Alert $alert
	 */
	public function createComponent(Alert $alert)
	{
		$groupId = 0;

		if ($alert->group)
		{
			$groupId = $this->getGroupIdByName($alert->group);
			if (!$groupId)
			{
				$response = $this->groupManager->storeGroup(['name' 		=> $alert->group,
															 'collapsed' 	=> '1',
															 'order' => time()]);

				$groupId = $response['data']['id'];
			}
		}

		$this->componentManager->storeComponent([
				'name'     => $alert->name,
				'group_id' => $groupId,
				'status'   => $this->translateStatus($alert),
				'link'     => $alert->url,
		]);

	}

	private function getComponentIdByName($componentName, $groupName)
	{
		$groupId = null;

		if($groupName)
		{
			return $this->getComponentIdByFromGroup($groupName, $componentName);
		}

		$response = $this->componentManager->indexComponents();
		$components = $response['data'];

		if(count($components) < 1)
		{
			return null;
		}

		foreach($components as $component)
		{
			if($component['name'] == $componentName && $component['group_id'] == (int) $groupId)
			{
				return $component['id'];
			}
		}

		return null;
	}

	private function getComponentIdByFromGroup($groupName, $componentName)
	{
		$response = $this->groupManager->indexGroups(1000);
		foreach($response['data'] as $group)
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

	private function getGroupIdByName($groupName)
	{
		$response = $this->groupManager->indexGroups(1000);
		foreach($response['data'] as $group)
		{
			if($group['name'] == $groupName)
			{
				return $group['id'];
			}
		}

		return null;
	}

	/**
	 * @param Alert $alert
	 *
	 * @return int
	 */
	private function translateStatus(Alert $alert)
	{
		return $alert->status == 'up' || $alert->status == 'operational' ? self::STATUS_UP : self::STATUS_DOWN;
	}

}