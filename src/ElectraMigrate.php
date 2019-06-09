<?php

namespace Electra\Migrate;

use Electra\Cli\Electra\Commands\Migrate\MigrateAllCliCommand;
use Electra\Cli\Electra\Commands\Migrate\MigrateMakeCliCommand;
use Electra\Cli\Electra\Commands\Migrate\MigrateRefreshCliCommand;
use Electra\Cli\Electra\Commands\Migrate\MigrateRollbackCliCommand;
use Electra\Cli\Electra\Commands\Migrate\MigrateStatusCliCommand;

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