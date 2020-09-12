<?php

declare(strict_types=1);

use Cake\Core\Configure;

require dirname(__DIR__) . '/vendor/autoload.php';

$_SERVER['PHP_SELF'] = '/';

define('TEST_ROOT', getcwd() . DS);

Configure::write('App.fullBaseUrl', 'http://localhost');

session_id('cli');
