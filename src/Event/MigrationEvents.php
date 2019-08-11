<?php

namespace Electra\Migrate\Event;

use Electra\Migrate\Event\GetAllFilesByGroup\GetAllFilesByGroupPayload;
use Electra\Migrate\Event\GetAllFilesByGroup\GetAllFilesByGroupResponse;
use Electra\Migrate\Event\GetAllFilesByGroup\GetAllFilesByGroupEvent;
use Electra\Migrate\Event\MigrateAll\MigrateAllPayload;
use Electra\Migrate\Event\MigrateAll\MigrateAllResponse;
use Electra\Migrate\Event\MigrateAll\MigrateAllEvent;
use Electra\Migrate\Event\MigrateRollback\MigrateRollbackPayload;
use Electra\Migrate\Event\MigrateRollback\MigrateRollbackResponse;
use Electra\Migrate\Event\MigrateRollback\MigrateRollbackEvent;

class MigrationEvents
{
  /**
   * @param GetAllFilesByGroupPayload $payload
   * @return GetAllFilesByGroupResponse
   * @throws \Exception
   */
  public static function getAllFilesByGroup(GetAllFilesByGroupPayload $payload): GetAllFilesByGroupResponse
  {
    return (new GetAllFilesByGroupEvent())->execute($payload);
  }

  /**
   * @param MigrateAllPayload $payload
   * @return MigrateAllResponse
   * @throws \Exception
   */
  public static function migrateAll(MigrateAllPayload $payload): MigrateAllResponse
  {
    return (new MigrateAllEvent())->execute($payload);
  }

  /**
   * @param MigrateRollbackPayload $payload
   * @return MigrateRollbackResponse
   * @throws \Exception
   */
  public static function rollback(MigrateRollbackPayload $payload): MigrateRollbackResponse
  {
    return (new MigrateRollbackEvent())->execute($payload);
  }

}