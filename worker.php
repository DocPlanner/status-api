<?php
/**
 * Author: gellu
 * Date: 10.04.2018 15:12
 */

require 'config.php';

require 'vendor/autoload.php';

$worker = new AMQPWorker();
$worker->dispatch();