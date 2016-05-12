<?php
/**
 * Author: Grzesiek
 * Date: 27.05.2015 12:39
 */

return [

	'NewRelic'	=> [
		'DocPlanner Servers' => [
			'component_id'	=> 'xf4n5m6cl5fk',
			'up'			=> 'operational',
			'down'			=> 'major_outage',
			'warning'		=> 'partial_outage',
		],
		'DocPlanner Performance' => [
			'component_id'	=> '9bsyqb1wrjfy',
			'up'			=> 'operational',
			'down'			=> 'degraded_performance',
			'warning'		=> 'partial_outage',
		],
		'DocPlanner Website' => [
			'component_id'	=> 'q777tcg6mwg3',
			'up'			=> 'operational',
			'down'			=> 'major_outage',
			'warning'		=> 'partial_outage',
		],
		'DocPlanner Error Rate' => [
			'component_id'	=> 'yc6dhlqtvt5r',
			'up'			=> 'operational',
			'down'			=> 'major_outage',
			'warning'		=> 'partial_outage',
		],
	],

	'Pingdom'	=> [
		'Chile'	=> [
			'component_id' 	=> 'q777tcg6mwg3',
			'component'		=> 'DocPlanner Website',
			'up'			=> 'operational',
			'down'			=> 'major_outage',
			'warning'		=> 'partial_outage',
			'aggregate'		=> '1800',
		],
		'Bulgaria' => [

		],
		'Poland'	=> [
			'component_id' 	=> 'q777tcg6mwg3',
			'component'		=> 'DocPlanner Website',
			'up'			=> 'operational',
			'down'			=> 'major_outage',
			'warning'		=> 'partial_outage',
			'aggregate'		=> '1800',
		],
		'Russia'	=> [
			'component_id' 	=> 'q777tcg6mwg3',
			'component'		=> 'DocPlanner Website',
			'up'			=> 'operational',
			'down'			=> 'major_outage',
			'warning'		=> 'partial_outage',
			'aggregate'		=> '1800',
		],
		'Turkey'	=> [
			'component_id' 	=> 'q777tcg6mwg3',
			'component'		=> 'DocPlanner Website',
			'up'			=> 'operational',
			'down'			=> 'major_outage',
			'warning'		=> 'partial_outage',
			'aggregate'		=> '1800',
		],
		'Czech Republic'	=> [
			'component_id' 	=> 'q777tcg6mwg3',
			'component'		=> 'DocPlanner Website',
			'up'			=> 'operational',
			'down'			=> 'major_outage',
			'warning'		=> 'partial_outage',
			'aggregate'		=> '1800',
		],
		'Hungary'	=> [
			'component_id' 	=> 'q777tcg6mwg3',
			'component'		=> 'DocPlanner Website',
			'up'			=> 'operational',
			'down'			=> 'major_outage',
			'warning'		=> 'partial_outage',
			'aggregate'		=> '1800',
		],
	],
	'Integrations' => [

	],


];
