<?php

namespace Electra\Migrate\Cli\Migrate;

use Electra\Config\Config;
use Electra\Migrate\Event\MigrateAll\MigrateAllPayload;
use Electra\Migrate\Event\MigrationEvents;
use Symfony\Component\Console\Command\Command;
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
    $migrateAllPayload = MigrateAllPayload::create();
    $migrateAllPayload->output = $output;
    $migrateAllPayload->migrationDirs = Config::getByPath('electra:migrate:migrationDirs');

    $migrateAllResponse = MigrationEvents::migrateAll($migrateAllPayload);

    // Migrate all failed
    if (!$migrateAllResponse->success)
    {
      $output->writeln("<fg=red>An error occurred while running migrations</>");
      return Command::FAILURE;
    }

    // Successfully run migrations
    if ($migrateAllResponse->executedMigrationsCount)
    {
      $pluralMigration = $migrateAllResponse->executedMigrationsCount > 1 ? 's' : '';
      $output->writeln(
        "<fg=green>{$migrateAllResponse->executedMigrationsCount} migration{$pluralMigration} executed successfully</>"
      );

      return Command::SUCCESS;
    }

    $output->writeln("<fg=green>Already up to date. No new migrations found</>");
    return Command::SUCCESS;
  }
}
