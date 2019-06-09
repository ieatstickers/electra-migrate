<?php

namespace Electra\Migrate\Task\MigrateAll;

use Electra\Core\Task\AbstractResponse;

class MigrateAllResponse extends AbstractResponse
{
  /** @var bool */
  public $success;

  /** @var int */
  public $executedMigrationsCount;
}