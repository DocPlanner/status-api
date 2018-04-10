<?php
/**
 * Author: gellu
 * Date: 10.04.2018 15:47
 */

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class AMQP
{
	const QUEUE = 'tasks';
	const EXCHANGE = '';

	/** @var AMQPChannel  */
	private $channel;
	/** @var AMQPStreamConnection  */
	private $connection;

	public function __construct()
	{
		$this->connection = new AMQPStreamConnection(Config::AQMP_HOST, Config::AMQP_PORT, Config::AMQP_USER, Config::AMQP_PASS, Config::AQMP_VHOST);
		$this->channel = $this->connection->channel();
		$this->channel->queue_declare(self::QUEUE, false, true, false, false);
	}

	public function publish($messageBody)
	{
		$messageBody = serialize($messageBody);
		$message = new AMQPMessage($messageBody, array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
		$this->channel->basic_publish($message, self::EXCHANGE, self::QUEUE);
	}

	public function get()
	{
		$message = $this->channel->basic_get(self::QUEUE);

		if(!$message)
		{
			return false;
		}

		$this->channel->basic_ack($message->delivery_info['delivery_tag']);

		return unserialize($message->body);
	}

	public function __destruct()
	{
		$this->channel->close();
		$this->connection->close();
	}


}