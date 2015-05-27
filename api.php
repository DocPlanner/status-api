<?php
/**
 * Author: Grzesiek
 * Date: 27.05.2015 12:11
 */

require 'config.php';

require 'vendor/autoload.php';

$app = new \Slim\Slim();

$app->post('/api', function() use ($app) {

	$configAlerts = require_once 'config-alerts.php';

	$newRelicAlert = new NewRelicAlert($configAlerts['NewRelic']);
	$newRelicAlert->setPayload($app->request()->getBody());

	$statusPage = new StatusPage($newRelicAlert);
	$statusPage->update();


});

$app->run();