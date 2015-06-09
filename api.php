<?php
/**
 * Author: Grzesiek
 * Date: 27.05.2015 12:11
 */

require 'config.php';

require 'vendor/autoload.php';

$app = new \Slim\Slim();

$app->post('/webhook/newrelic', function() use ($app) {

	$configAlerts = require_once 'config.alerts.php';

	$newRelicAlert = new NewRelicAlert($configAlerts['NewRelic']);
	$newRelicAlert->setPayload($app->request()->getBody());

	$statusPage = new StatusPage($newRelicAlert);
	$statusPage->update();


});

$app->get('/webhook/pingdom', function() use ($app) {

	$configAlerts = require_once 'config.alerts.php';

	$pingdomAlert = new PingdomAlert($configAlerts['Pingdom']);
	$pingdomAlert->setPayload($app->request()->get('message'));

	$statusPage = new StatusPage($pingdomAlert);
	$statusPage->update();

});

$app->run();