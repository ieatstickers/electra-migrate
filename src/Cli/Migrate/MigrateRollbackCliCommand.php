<?php

namespace Electra\Migrate\Cli\Migrate;

use Electra\Migrate\Event\MigrateRollback\MigrateRollbackPayload;
use Electra\Migrate\Event\MigrationEvents;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateRollbackCliCommand extends AbstractMigrateCommand
{
  protected static $defaultName = 'migrate:rollback';

  protected function configure()
  {
    $this
      ->setDescription('Rollback migrations')
      ->setHelp('Rollback all migrations that were run in the last batch');
  }

  /**
   * @param InputInterface $input
   * @param OutputInterface $output
   * @return int|void|null
   * @throws \Exception
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $migrateRollbackPayload = MigrateRollbackPayload::create();
    $migrateRollbackPayload->output = $output;
    $migrateRollbackPayload->migrationDirs = $this->getContext()->getConfig()->getByPath('electra:migrate:migrationDirs');

    $migrateAllResponse = MigrationEvents::rollback($migrateRollbackPayload);

    // Migrate all failed
    if (!$migrateAllResponse->success)
    {
      $output->writeln("<fg=red>An error occurred while rolling back migrations</>");
      return Command::FAILURE;
    }

    // Successfully run migrations
    if ($migrateAllResponse->rolledBackMigrationsCount)
    {
      $pluralMigration = $migrateAllResponse->rolledBackMigrationsCount > 1 ? 's' : '';
      $output->writeln(
        "<fg=green>{$migrateAllResponse->rolledBackMigrationsCount} migration{$pluralMigration} rolled back successfully</>"
      );
      return Command::SUCCESS;
    }

    $output->writeln("<fg=green>No migrations to rollback</>");
    return Command::SUCCESS;
  }
}
