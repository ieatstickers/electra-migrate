<?php

namespace Electra\Migrate\Event\MigrateRollback;

use Electra\Core\Event\AbstractResponse;

/**
 * Class MigrateRollbackResponse
 * @method static $this create($data = [])
 */
class MigrateRollbackResponse extends AbstractResponse
{
  /** @var bool */
  public $success;

  /** @var int */
  public $rolledBackMigrationsCount;
}