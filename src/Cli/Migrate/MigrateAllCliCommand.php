<?php

namespace Electra\Cli\Electra\Commands\Migrate;

use Electra\Config\Config;
use Electra\Module\Migration\Task\MigrateAll\MigrateAllPayload;
use Electra\Module\Migration\Task\MigrationTasks;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateAllCliCommand extends AbstractMigrateCommand
{
  protected static $defaultName = 'migrate:all';

  protected function configure()
  {
    $this
      ->setDescription('Run all migrations')
      ->setHelp('Run all migrations that haven\'t yet been run');
  }

  /**
   * @param InputInterface $input
   * @param OutputInterface $output
   * @throws \Exception
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $migrateAllPayload = new MigrateAllPayload();
    $migrateAllPayload->output = $output;
    $migrateAllPayload->migrationDirs = Config::getByPath('electra:migrate:migrationDirs');

    $migrateAllResponse = MigrationTasks::migrateAll($migrateAllPayload);

    // Migrate all failed
    if (!$migrateAllResponse->success)
    {
      $output->writeln("<fg=green>An error occurred while running migrations</>");
      return;
    }

    // Successfully run migrations
    if ($migrateAllResponse->executedMigrationsCount)
    {
      $pluralMigration = $migrateAllResponse->executedMigrationsCount > 1 ? 's' : '';
      $output->writeln(
        "<fg=green>{$migrateAllResponse->executedMigrationsCount} migration{$pluralMigration} executed successfully</>"
      );
      return;
    }

    $output->writeln("<fg=green>Already up to date. No new migrations found</>");
  }
}