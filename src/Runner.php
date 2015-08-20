#!/usr/bin/env php
<?php

namespace Certi\LegacypsrFour;

require_once __DIR__.'/../vendor/autoload.php';

use Certi\LegacypsrFour\Command\Fixer;
use Certi\LegacypsrFour\Command\Tester;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new Fixer());
$application->add(new Tester());
$application->run();
