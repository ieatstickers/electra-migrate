<?php

namespace Electra\Migrate\Cli\Migrate;

use Electra\Migrate\Data\Migration;
use Electra\Migrate\Event\GetAllFilesByGroup\GetAllFilesByGroupPayload;
use Electra\Migrate\Event\MigrationEvents;
use Electra\Utility\Arrays;
use Electra\Utility\Objects;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateStatusCliCommand extends AbstractMigrateCommand
{
  protected static $defaultName = 'migrate:status';

  protected function configure()
  {
    $this
      ->setDescription('Status of all registered migrations')
      ->setHelp(
        'List out all registered migrations and whether or not they have been executed'
      );
  }

  /**
   * @param InputInterface $input
   * @param OutputInterface $output
   * @return int|void|null
   * @throws \Exception
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    // Get all migration files
    $allFilesPayload = GetAllFilesByGroupPayload::create();
    $allFilesPayload->output = $output;
    $migrationDirsConfig = $this->getContext()->getConfig()->getByPath('electra:migrate:migrationDirs');

    if (!$migrationDirsConfig)
    {
      $output->writeln("<fg=red>'migrationsDirs' not found in electra.yaml</>");
      return Command::INVALID;
    }

    $allFilesPayload->migrationDirs = $migrationDirsConfig;
    $allFilesResponse = MigrationEvents::getAllFilesByGroup($allFilesPayload);
    $migrationFilesByGroup = $allFilesResponse->filesByGroup;

    // Get all migrations from the database
    $migrationDbRows = Migration::getAllByGroupIndexedByName();

    // Read yaml in project root.
    $configFilepath = realpath(__DIR__ . "/../../../../../../electra.yaml");

    if (!file_exists($configFilepath))
    {
      $output->writeln("<fg=red>electra.yaml not found in project root: $configFilepath</>");
      return Command::INVALID;
    }

    $groupDisplayNamesByKey = [];

    foreach ($migrationDirsConfig as $group => $migrationDirConfig)
    {
      $groupDisplayNamesByKey[$group] = Arrays::getByKey('name', $migrationDirConfig);
    }

    foreach ($migrationFilesByGroup as $group => $migrationFiles)
    {
      $groupDisplayName = Arrays::getByKey($group, $groupDisplayNamesByKey, $group);
      $output->writeln("<fg=yellow>" . $groupDisplayName . ":</>");

      if (!$migrationFiles)
      {
        $output->writeln("  <fg=red>* No migrations found *</>");
      }

      foreach ($migrationFiles as $migration)
      {
        $outputColor = 'red';

        // Get migration entity from db
        $groupDbMigrations = Arrays::getByKey($group, $migrationDbRows);

        if ($groupDbMigrations)
        {
          $migrationDbName = Objects::getProperty('name', $migration);
          $dbMigration = Arrays::getByKey($migrationDbName, $groupDbMigrations);

          if ($dbMigration && $dbMigration->executed)
          {
            $outputColor = 'green';
          };
        }

        $output->writeln(
          "<fg=$outputColor>  " . Objects::getProperty('name', $migration) . "</>"
        );
      }
    }

    return Command::SUCCESS;
  }
}
