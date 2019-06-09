<?php

namespace Electra\Module\Migration\Task\MigrateAll;

use Electra\Core\Task\AbstractResponse;

class MigrateAllResponse extends AbstractResponse
{
  /** @var bool */
  public $success;

  /** @var int */
  public $executedMigrationsCount;
}