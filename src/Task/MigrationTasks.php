<?php

namespace Electra\Module\Migration\Task;

use Electra\Module\Migration\Task\GetAllFilesByGroup\GetAllFilesByGroupPayload;
use Electra\Module\Migration\Task\GetAllFilesByGroup\GetAllFilesByGroupResponse;
use Electra\Module\Migration\Task\GetAllFilesByGroup\GetAllFilesByGroupTask;
use Electra\Module\Migration\Task\MigrateAll\MigrateAllPayload;
use Electra\Module\Migration\Task\MigrateAll\MigrateAllResponse;
use Electra\Module\Migration\Task\MigrateAll\MigrateAllTask;
use Electra\Module\Migration\Task\MigrateRollback\MigrateRollbackPayload;
use Electra\Module\Migration\Task\MigrateRollback\MigrateRollbackResponse;
use Electra\Module\Migration\Task\MigrateRollback\MigrateRollbackTask;

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