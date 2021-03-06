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

	$statusPage = new StatusPage();
	$statusPage->update($newRelicAlert);


});

$app->post('/webhook/pingdom', function() use ($app) {

	$configAlerts = require_once 'config.alerts.php';

	$pingdomAlert = new PingdomAlert($configAlerts['Pingdom']);

	$pingdomAlert->setPayload($app->request()->getBody());

	(new StatusPage())->update($pingdomAlert);

	$amqp = new AMQP();
	$amqp->publish($pingdomAlert);

});

$app->post('/webhook/integrations', function() use ($app) {

	$configAlerts = require_once 'config.alerts.php';

	$integrationsAlert = new IntegrationsAlert($configAlerts['Integrations']);
	$integrationsAlert->setPayload($app->request()->getBody());


	(new Cachet(true))->trigger($integrationsAlert);

});

$app->get('/geckoboard/ghostinspector', function() use ($app) {

	(new GhostInspector())->getGeckoView();

});

$app->get('/clear-incidents', function() use ($app) {

	$statusPage = new StatusPage();
	$statusPage->getUnresolvedIncidents();


});

$app->run();