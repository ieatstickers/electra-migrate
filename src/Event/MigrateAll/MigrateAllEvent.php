<?php

namespace Electra\Migrate\Event\MigrateAll;

use Carbon\Carbon;
use Electra\Config\Config;
use Electra\Core\Event\AbstractEvent;
use Electra\Migrate\Data\Migration;
use Electra\Migrate\Data\MigrationFile;
use Electra\Migrate\Event\GetAllFilesByGroup\GetAllFilesByGroupPayload;
use Electra\Migrate\Event\MigrationEvents;
use Electra\Utility\Arrays;
use Electra\Migrate\Migration as ElectraMigration;

class MigrateAllEvent extends AbstractEvent
{
  /** @return string */
  public function getPayloadClass(): string
  {
    return MigrateAllPayload::class;
  }

  /**
   * @param MigrateAllPayload $payload
   * @return MigrateAllResponse
   * @throws \Exception
   */
  protected function process($payload): MigrateAllResponse
  {
    $allFilesPayload = new GetAllFilesByGroupPayload();
    $output = $payload->output;
    $allFilesPayload->migrationDirs = Config::getByPath('electra:migrate:migrationDirs');
    $allFilesByGroupResponse = MigrationEvents::getAllFilesByGroup($allFilesPayload);
    $executedMigrationsByGroupAndName = Migration::getAllExecutedIndexedByGroupAndName();
    $lastExecuted = Migration::getLastExecuted();
    $batch = $lastExecuted ? $lastExecuted->batch + 1 : 1;

    $migrationsToRun = [];

    // For each group
    foreach ($allFilesByGroupResponse->filesByGroup as $group => $migrations)
    {
      // For each file
      /** @var MigrationFile $migrationFile */
      foreach ($migrations as $migrationFile)
      {
        $key = $group . "_" . $migrationFile->name;
        /** @var Migration $executedMigration */
        $executedMigration = Arrays::getByKey($key, $executedMigrationsByGroupAndName);

        // If it hasn't been executed
        if (!$executedMigration || ($executedMigration && !$executedMigration->executed))
        {
          // Add to $migrationsToRun
          if (!isset($migrationsToRun[$group]))
          {
            $migrationsToRun[$group] = [];
          }

          $migrationsToRun[$group][] = $migrationFile;
        }
      }
    }

    $migrationsRun = 0;

    // For each $migrationsToRun as $group => $migrations
    foreach ($migrationsToRun as $group => $migrations)
    {
      // For each migration
      /** @var MigrationFile $migrationFile */
      foreach ($migrations as $migrationFile)
      {
        if ($output)
        {
          $output->writeln("<fg=blue>Migrating:</> {$migrationFile->filename}");
        }

        include "$migrationFile->filepath";

        /** @var ElectraMigration $migrationInstance */
        $migrationInstance = new $migrationFile->fqns();
        $migrationInstance->up();

        // Add row to DB
        $migrationEntity = Migration::create();

        $migrationEntity->name = $migrationFile->name;
        $migrationEntity->group = $group;
        $migrationEntity->batch = $batch;
        $migrationEntity->executed = new Carbon();
        $migrationEntity->save();

        if ($output)
        {
          $output->writeln("<fg=green>Migrated:</>  {$migrationFile->filename}");
        }

        $migrationsRun++;
      }
    }

    $response = new MigrateAllResponse();
    $response->success = true;
    $response->executedMigrationsCount = $migrationsRun;

    return $response;
  }

}