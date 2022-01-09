<?php

namespace Electra\Migrate\Context;

use Electra\Config\Config;
use Electra\Core\Context\Context;
use Electra\Dal\Database\Mysql\Mysql;

class MigrateContext extends Context
{
  /** @var Config */
  private $config;

  protected function __construct()
  {
    parent::__construct();
  }

  public static function create(): MigrateContext
  {
    $ctx = new static();
    $configFilePath = realpath("{$ctx->getProjectRoot()}/electra.yaml");

    if (!file_exists($configFilePath))
    {
      throw new \Exception("electra.yaml not found in project root: $configFilePath");
    }

    $dbUsers = $ctx->getConfig()->getByPath('electra:dal:users');

    if ($dbUsers)
    {
      Mysql::registerDbUsers($dbUsers);
    }

    $selectedUser = $ctx->getConfig()->getByPath('electra:migrate:dbUser');

    if ($selectedUser)
    {
      Mysql::setUser($selectedUser, false);
    }

    $connections = $ctx->getConfig()->getByPath('electra:dal:connections');
    Mysql::setDbConnections($connections);

    return $ctx;
  }

  public function getConfig(): Config
  {
    if (!$this->config)
    {
      $mergeRules = [ "/^electra\.yaml$/" ];

      $env = getenv('ENVR');

      if ($env)
      {
        $mergeRules[] = "/^electra\.{$env}\.yaml/";
      }

      $this->config = Config::create(
        [ $this->getProjectRoot() ],
        $mergeRules
      );
    }

    return $this->config;
  }
}
