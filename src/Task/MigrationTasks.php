<?php

namespace Electra\Migrate\Task;

use Electra\Migrate\Task\GetAllFilesByGroup\GetAllFilesByGroupPayload;
use Electra\Migrate\Task\GetAllFilesByGroup\GetAllFilesByGroupResponse;
use Electra\Migrate\Task\GetAllFilesByGroup\GetAllFilesByGroupTask;
use Electra\Migrate\Task\MigrateAll\MigrateAllPayload;
use Electra\Migrate\Task\MigrateAll\MigrateAllResponse;
use Electra\Migrate\Task\MigrateAll\MigrateAllTask;
use Electra\Migrate\Task\MigrateRollback\MigrateRollbackPayload;
use Electra\Migrate\Task\MigrateRollback\MigrateRollbackResponse;
use Electra\Migrate\Task\MigrateRollback\MigrateRollbackTask;

class MigrationTasks
{
  /**
   * @param GetAllFilesByGroupPayload $payload
   * @return GetAllFilesByGroupResponse
   * @throws \Exception
   */
  public static function getAllFilesByGroup(GetAllFilesByGroupPayload $payload): GetAllFilesByGroupResponse
  {
    return (new GetAllFilesByGroupTask())->execute($payload);
  }

  /**
   * @param MigrateAllPayload $payload
   * @return MigrateAllResponse
   * @throws \Exception
   */
  public static function migrateAll(MigrateAllPayload $payload): MigrateAllResponse
  {
    return (new MigrateAllTask())->execute($payload);
  }

  /**
   * @param MigrateRollbackPayload $payload
   * @return MigrateRollbackResponse
   * @throws \Exception
   */
  public static function rollback(MigrateRollbackPayload $payload): MigrateRollbackResponse
  {
    return (new MigrateRollbackTask())->execute($payload);
  }

}