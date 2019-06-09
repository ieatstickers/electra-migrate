<?php

namespace Electra\Migrate;


use Electra\Migrate\Cli\Migrate\MigrateAllCliCommand;
use Electra\Migrate\Cli\Migrate\MigrateMakeCliCommand;
use Electra\Migrate\Cli\Migrate\MigrateRefreshCliCommand;
use Electra\Migrate\Cli\Migrate\MigrateRollbackCliCommand;
use Electra\Migrate\Cli\Migrate\MigrateStatusCliCommand;

class ElectraMigrate
{
  /**
   * @return array
   * @throws \Exception
   */
  public static function getCliCommands(): array
  {
    return [
      new MigrateAllCliCommand(),
      new MigrateMakeCliCommand(),
      new MigrateRefreshCliCommand(),
      new MigrateStatusCliCommand(),
      new MigrateRollbackCliCommand()
    ];
  }
}