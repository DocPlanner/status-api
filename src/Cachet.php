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
use Damianopetrungaro\CachetSDK\Incidents\IncidentActions;
use Damianopetrungaro\CachetSDK\Incidents\IncidentFactory;

class Cachet
{
	const URL 			= 'http://integrations.docplanner.io/api/v1/';

	const STATUS_UP 	= 1;
	const STATUS_DOWN 	= 4;

	/** @var CachetClient */
	private $client;
	/** @var ComponentActions  */
	private $componentManager;
	/** @var GroupActions  */
	private $groupManager;
	/** @var IncidentActions  */
	private $incidentManager;

	private $trackIncidents;

	public function __construct($trackIncidents = false)
	{
		$this->client 			= new CachetClient(self::URL, Config::CACHET_API_KEY);
		$this->componentManager = ComponentFactory::build($this->client);
		$this->groupManager 	= GroupFactory::build($this->client);
		$this->incidentManager	= IncidentFactory::build($this->client);
		$this->trackIncidents	= $trackIncidents;
	}

	/**
	 * @param Alert $alert
	 */
	public function trigger(Alert $alert)
	{
		$alert->component = $this->getComponentByName($alert->name, $alert->group);

		if($alert->component && $this->translateStatus($alert) != $alert->component['status'])
		{
			$this->updateComponent($alert);
		}
		else {
			$this->createComponent($alert);
		}

		if($this->trackIncidents && $this->translateStatus($alert) != $alert->component['status'])
		{
			$this->triggerIncident($alert);
		}
	}

	private function triggerIncident(Alert $alert)
	{
		if(!$alert->component)
		{
			throw new Exception('Component undefined');
		}

		if($this->translateStatus($alert) == self::STATUS_DOWN)
		{
			$this->incidentManager->storeIncident([
					'name'             => ' integration',
					'message'          => 'Integration has failed to report',
					'status'           => 1,
					'visible'          => true,
					'component_id'     => $alert->component['id'],
					'component_status' => $this->translateStatus($alert)
			]);
		}
	}

	/**
	 * @param Alert $alert
	 */
	private function updateComponent(Alert $alert)
	{
		$this->componentManager->updateComponent($alert->component['id'], [	'status'	=> $this->translateStatus($alert),
																			'link' 		=> $alert->url]);
	}

	/**
	 * @param Alert $alert
	 */
	private function createComponent(Alert $alert)
	{
		if ($alert->group)
		{
			$alert->group_id = $this->getGroupIdByName($alert->group);

			if (!$alert->group_id)
			{
				$response = $this->groupManager->storeGroup(['name' 		=> $alert->group,
															 'collapsed' 	=> '1',
															 'order' 		=> time()]);

				$alert->group_id = $response['data']['id'];
			}
		}

		$response = $this->componentManager->storeComponent([
				'name'     => $alert->name,
				'group_id' => $alert->group_id,
				'status'   => $this->translateStatus($alert),
				'link'     => $alert->url,
		]);

		$alert->component['id'] = $response['data']['id'];

	}

	private function getComponentByName($componentName, $groupName)
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
				return $component;
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
							return $component;
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