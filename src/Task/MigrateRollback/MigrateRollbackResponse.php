<?php

namespace Electra\Module\Migration\Task\MigrateRollback;

use Electra\Core\Task\AbstractResponse;

class MigrateRollbackResponse extends AbstractResponse
{
  /** @var bool */
  public $success;

  /** @var int */
  public $rolledBackMigrationsCount;
}