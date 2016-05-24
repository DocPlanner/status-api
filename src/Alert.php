<?php
/**
 * Author: Grzesiek
 * Date: 27.05.2015 12:55
 */

class Alert
{
	protected $_config = [];

	public $name;
	public $group;
	public $group_id = 0;
	public $url;
	public $component;
	public $component_id;
	public $status;
	public $info;
	public $aggregate;

	public function __construct($config)
	{
		$this->_config = $config;
	}

	public function __get($property) {

		if (property_exists($this, $property))
		{
			return $this->$property;
		}
	}

	public function __set($property, $value) {

		if (property_exists($this, $property))
		{
			$this->$property = $value;
		}

		return $this;
	}
}
