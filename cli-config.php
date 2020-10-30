<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

define('BASE_PATH', dirname(__FILE__));

require_once "vendor/autoload.php";

$isDevMode = true;//generate proxy manually if entity not found
$paths = array(
    BASE_PATH . "/resources/Infrastructure/Persistence/Doctrine/Mapping",
    BASE_PATH . "/src/Query/Infrastructure/Persistence/Doctrine/Mapping",
    BASE_PATH . "/src/SharedContext/Infrastructure/Persistence/Doctrine/Mapping/ValueObject",
);

$doctrineConfig = Setup::createXMLMetadataConfiguration($paths, $isDevMode);

 $conn = array(
    'driver' => 'pdo_mysql',
    'user' => 'root',
    'password' => '',
    'dbname' => 'bara_mcp'
 );
//$conn = array(
//    'driver' => 'pdo_sqlite',
//    'path' => BASE_PATH . "/tests/database.sqlite",
//);

$entityManager = EntityManager::create($conn, $doctrineConfig);
return ConsoleRunner::createHelperSet($entityManager);
