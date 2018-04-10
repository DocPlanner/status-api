<?php
/**
 * Author: Grzesiek
 * Date: 03.06.2015 10:19
 */


class PingdomAlert extends Alert
{
	private $_payload;

	public function setPayload($payload)
	{
		$this->_payload = json_decode(str_replace(array("(u'", "',)"), '', $payload), true);

		if(!is_array($this->_payload) || !isset($this->_payload['check_name']))
		{
			throw new Exception('Incorrect payload');
		}

		$this->_log(json_encode($this->_payload));
		$this->_parsePayload();
		$this->_log(json_encode($this));
	}

	private function _parsePayload()
	{
		if(!isset($this->_config[$this->_payload['check_id']]))
		{
			throw new Exception('There is no config for Checkname');
		}

		$this->name			= $this->_payload['check_name'];
		$this->component_id = $this->_config[$this->_payload['check_id']];

		if(	$this->_payload['current_state'] == 'DOWN')
		{
			$this->status = 'major_outage';
		}

		if(	$this->_payload['current_state'] == 'UP')
		{
			$this->status = 'operational';
		}

		$this->created_at = time();

	}

	private function _log($msg)
	{
		$log = new \Monolog\Logger('pingdom_alert');
		$log->pushHandler(new \Monolog\Handler\StreamHandler('log/pingdom_alert.log'));
		$log->addInfo($msg);

	}

}