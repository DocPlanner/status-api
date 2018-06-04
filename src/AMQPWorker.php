<?php
/**
 * Author: gellu
 * Date: 10.04.2018 15:16
 */

class AMQPWorker
{
	const TIME_TO_INCIDENT = 420;

	/** @var AMQP */
	private $amqp;

	public function dispatch()
	{
		$this->amqp = new AMQP();

		$rePublish = [];

		while(true)
		{
			$message = $this->getMessage();

			if(false === $message)
			{
				break;
			}

			if($message->created_at + self::TIME_TO_INCIDENT < time())
			{
				(new StatusPage())->createIncident($message);
			}
			else {
				$rePublish[] = $message;
			}
		}

		foreach($rePublish as $message)
		{
			$this->amqp->publish($message);
		}
	}

	private function getMessage()
	{
		return $this->amqp->get();
	}
}