<?php
require_once __DIR__.'/../vendor/autoload.php';

use App\Shared\Infrastructure\Bootstrap\EnvironmentLoader;
use App\Shared\Infrastructure\Di\ContainerConfig;

EnvironmentLoader::load();
$container = ContainerConfig::create();