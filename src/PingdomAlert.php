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

		if(!is_array($this->_payload) || !isset($this->_payload['checkname']))
		{
			throw new Exception('Incorrect payload');
		}

		$this->_log(json_encode($this->_payload));
		$this->_parsePayload();
		$this->_log(json_encode($this));
	}

	private function _parsePayload()
	{
		if(!isset($this->_config[$this->_payload['checkname']]))
		{
			throw new Exception('There is no config for Checkname');
		}

		$this->name			= $this->_payload['checkname'];
		$this->group		= 'Web';
		$this->status		= $this->_payload['description'];
		$this->component 	= $this->_config[$this->_payload['checkname']]['component'];
		$this->component_id = $this->_config[$this->_payload['checkname']]['component_id'];
		$this->info 		= 'down! (first noticed in: '. $this->_payload['checkname'] .')';
		$this->aggregate	= $this->_config[$this->_payload['checkname']]['aggregate'];

		if(	$this->_payload['description'] == 'down')
		{
			$this->status = $this->_config[$this->_payload['checkname']]['down'];
		}

		if(	$this->_payload['description'] == 'up')
		{
			$this->status = $this->_config[$this->_payload['checkname']]['up'];
		}

	}

	private function _log($msg)
	{
		$log = new \Monolog\Logger('pingdom_alert');
		$log->pushHandler(new \Monolog\Handler\StreamHandler('log/pingdom_alert.log'));
		$log->addInfo($msg);

	}

}