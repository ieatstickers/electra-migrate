<?php

namespace Electra\Migrate\Event\MigrateAll;

use Electra\Core\Event\AbstractResponse;

/**
 * Class MigrateAllResponse
 * @method static $this create($data = [])
 */
class MigrateAllResponse extends AbstractResponse
{
  /** @var bool */
  public $success;

  /** @var int */
  public $executedMigrationsCount;
}