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

		$this->_log(json_encode($this->_payload));
		$this->_parsePayload();
		$this->_log(json_encode($this));
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

		if(	$this->_payload['current_state'] == 'open' &&
			$this->_payload['severity'] == 'CRITICAL')
		{
			$this->status = $this->_config[$this->_payload['policy_name']]['down'];
		}

		if(	$this->_payload['current_state'] == 'open' &&
			$this->_payload['severity'] == 'WARN')
		{
			$this->status = $this->_config[$this->_payload['policy_name']]['warning'];
		}

		if(	$this->_payload['current_state'] == 'closed')
		{
			$this->status = $this->_config[$this->_payload['policy_name']]['up'];
		}

	}

	private function _log($msg)
	{
		$log = new \Monolog\Logger('new_relic_alert');
		$log->pushHandler(new \Monolog\Handler\StreamHandler('log/new_relic_alert.log'));
		$log->addInfo($msg);

	}


}