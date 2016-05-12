<?php
/**
 * Author: gellu
 * Date: 11.05.2016 17:47
 */

class IntegrationsAlert extends Alert
{
	private $_payload;

	public function setPayload($payload)
	{
		$this->_payload = json_decode($payload, true);

		if(!is_array($this->_payload) || !isset($this->_payload['integration']) || !isset($this->_payload['check']))
		{
			throw new Exception('Incorrect payload');
		}

		$this->_log(json_encode($this->_payload));
		$this->_parsePayload();
		$this->_log(json_encode($this));
	}

	private function _parsePayload()
	{
		$this->name		= $this->_payload['check'];
		$this->status	= $this->_payload['status'] == 1 ? 'up' : 'down';
		$this->group	= $this->_payload['integration'];
	}

	private function _log($msg)
	{
		$log = new \Monolog\Logger('integrations_alert');
		$log->pushHandler(new \Monolog\Handler\StreamHandler('log/integrations_alert.log'));
		$log->addInfo($msg);

	}
}