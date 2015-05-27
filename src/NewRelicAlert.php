<?php
/**
 * Author: Grzesiek
 * Date: 27.05.2015 12:12
 */

class NewRelicAlert extends Alert
{
	private $_payload;

	public function setPayload($payload)
	{
		$this->_payload = json_decode($payload, true);

		if(!is_array($this->_payload) || !isset($this->_payload['condition_name']))
		{
			throw new Exception('Incorrect payload');
		}

		$this->_parsePayload();
	}

	private function _parsePayload()
	{
		if(!isset($this->_config[$this->_payload['policy_name']]))
		{
			throw new Exception('There is no config for Policy');
		}

		$this->component 	= $this->_payload['policy_name'];
		$this->component_id = $this->_config[$this->_payload['policy_name']]['component_id'];
		$this->info 		= $this->_payload['condition_name'];

		if(	$this->_payload['current_state'] == 'OPEN' &&
			$this->_payload['severity'] == 'CRITICAL')
		{
			$this->status = $this->_config[$this->_payload['policy_name']]['down'];
		}

		if(	$this->_payload['current_state'] == 'OPEN' &&
			$this->_payload['severity'] == 'WARN')
		{
			$this->status = $this->_config[$this->_payload['policy_name']]['warning'];
		}

		if(	$this->_payload['current_state'] == 'CLOSE')
		{
			$this->status = $this->_config[$this->_payload['policy_name']]['up'];
		}

	}

}