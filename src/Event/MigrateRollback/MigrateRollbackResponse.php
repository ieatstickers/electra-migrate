<?php

namespace Electra\Migrate\Event\MigrateRollback;

use Electra\Core\Event\AbstractResponse;

class MigrateRollbackResponse extends AbstractResponse
{
  /** @var bool */
  public $success;

  /** @var int */
  public $rolledBackMigrationsCount;
}