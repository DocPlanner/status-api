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
	'Pingdom' =>
		[
			//Marketplace
			'909994' => 'hgclrq4cs8yb', // Czech
			'621533' => '88y74jjrr8k1', // Poland
			'2450199' => 'zg0hf0tsnbmz', // Websites
			'1961451' => '7k8n7zpczybb', // Italy
			'4511358' => 'b1x3rsn03bmk', // Mexico
			'2149609' => 'mxt2xrnw83pg', // Spain
			'910776' => 'ttx2q4h8j4r3', // Turkey
			'4511340' => 'cyhj0x9lplzg', // Brazil
			// SaaS
			'4511433' => 'n1gyg65s2qp0', // Brazil
			'4511403' => 'zlmv45mc4dpz', // Czech
			'4511406' => 'h9swrpmxd7vx', // Italy
			'4511436' => 'pq3g02t8yyzg', // Mexico
			'4511376' => 'fgr54d20p575', // Poland
			'4511445' => 'xb4cnzzml4f0', // Spain
			'4511394' => 'bb004fjbmtg7', // Turkey

		],
	'Integrations' => [

	],


];
