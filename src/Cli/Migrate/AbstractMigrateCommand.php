<?php

namespace Electra\Migrate\Cli\Migrate;

use Electra\Config\Config;
use Electra\Dal\Database\Mysql\Mysql;
use Electra\Utility\Arrays;
use Symfony\Component\Console\Command\Command;

abstract class AbstractMigrateCommand extends Command
{
  /**
   * AbstractMigrateCommand constructor.
   * @throws \Exception
   */
  public function __construct()
  {
    parent::__construct();

    $configFilePath = realpath(__DIR__ . "/../../../../../../electra.yaml");

    if (!file_exists($configFilePath))
    {
      throw new \Exception("electra.yaml not found in project root: $configFilePath");
    }

    $env = Arrays::getByKey('ENV', $_ENV);

    $projectRoot = __DIR__ . "/../../../../../../";

    Config::addConfigDir($projectRoot);
    Config::addMergeRule("/^electra.yaml$/");

    if ($env)
    {
      Config::addMergeRule("/^$env-electra.yaml$/");
    }

    Config::generate();

    $connections = Config::getByPath('electra:dal:connections');
    Mysql::setDbConnections($connections);
  }

}