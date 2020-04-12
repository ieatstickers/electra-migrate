<?php

namespace Electra\Migrate\Cli\Migrate;

use Electra\Config\Config;
use Electra\Dal\Database\Mysql\Mysql;
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

    $env = getenv('ENVR');

    $projectRoot = __DIR__ . "/../../../../../../";

    Config::addConfigDir($projectRoot);
    Config::addMergeRule("/^electra.yaml$/");

    if ($env)
    {
      Config::addMergeRule("/^electra\.{$env}\.yaml/");
    }

    Config::generate();

    var_dump(Config::getByPath('electra:dal:users:migration:password'));

    $dbUsers = Config::getByPath('electra:dal:users');

    if ($dbUsers)
    {
      Mysql::registerDbUsers($dbUsers);
    }

    $selectedUser = Config::getByPath('electra:migrate:dbUser');

    var_dump(Config::getByPath('$selectedUser')); die;

    if ($selectedUser)
    {
      Mysql::setUser($selectedUser, true);
    }

    $connections = Config::getByPath('electra:dal:connections');
    Mysql::setDbConnections($connections);
  }

}