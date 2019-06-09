<?php

namespace Electra\Migrate\Task\MigrateRollback;

use Electra\Config\Config;
use Electra\Core\Task\AbstractTask;
use Electra\Module\Migration\Data\Migration;
use Electra\Module\Migration\Data\MigrationFile;
use Electra\Module\Migration\Task\GetAllFilesByGroup\GetAllFilesByGroupPayload;
use Electra\Module\Migration\Task\MigrationTasks;
use Electra\Utility\Arrays;

class MigrateRollbackTask extends AbstractTask
{
  /** @return string */
  public function getPayloadClass(): string
  {
    return MigrateRollbackPayload::class;
  }

  /**
   * @param MigrateRollbackPayload $payload
   * @return MigrateRollbackResponse
   * @throws \Exception
   */
  protected function process($payload): MigrateRollbackResponse
  {
    $allFilesPayload = new GetAllFilesByGroupPayload();
    $output = $payload->output;
    $allFilesPayload->migrationDirs = Config::getByPath('electra:migrate:migrationDirs');
    $allFilesByGroupResponse = MigrationTasks::getAllFilesByGroup($allFilesPayload);
    $allFilesByGroup = $allFilesByGroupResponse->filesByGroup;

    $lastExecuted = Migration::getLastExecuted();

    if (!$lastExecuted)
    {
      $response = new MigrateRollbackResponse();
      $response->success = true;
      $response->rolledBackMigrationsCount = 0;
      return $response;
    }

    // Order in reverse so we roll them back in the opposite order they were run in
    $lastBatch = Migration::getAllByBatch($lastExecuted->batch)->sort(
      function(Migration $a, Migration $b)
      {
        return $a->name < $b->name;
      }
    );

    /** @var Migration $executedMigration */
    foreach ($lastBatch->all() as $executedMigration)
    {
      if ($output)
      {
        $output->writeln("<fg=blue>Rolling back:</> {$executedMigration->name}");
      }

      /** @var MigrationFile $migrationFile */
      $migrationFile = Arrays::getByKeyPath("{$executedMigration->group}:{$executedMigration->name}", $allFilesByGroup);

      if (!$migrationFile)
      {
        $errorMessage = "Cannot roll back migration: {$executedMigration->name}. Migration file not found.";

        if ($output)
        {
          $output->writeln("<fg=red>$errorMessage</>");
        }
        else
        {
          throw new \Exception($errorMessage);
        }
      }

      include "$migrationFile->filepath";

      /** @var \Electra\Module\Migration\Migration $migrationInstance */
      $migrationInstance = new $migrationFile->fqns();
      $migrationInstance->down();
      $executedMigration->delete();

      if ($output)
      {
        $output->writeln("<fg=green>Rolled back:</>  {$migrationFile->filename}");
      }
    }

    $response = new MigrateRollbackResponse();
    $response->success = true;
    $response->rolledBackMigrationsCount = $lastBatch->count();

    return $response;
  }

}