<?php

if (php_sapi_name() !== 'cli') {
    die('Must be run from CLI.');
}

require_once 'vendor/autoload.php';

use ERTNoteMaker\Database;

$database = Database::create();
return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($database->getEntityManager());
