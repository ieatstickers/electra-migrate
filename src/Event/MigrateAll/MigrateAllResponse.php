<?php

namespace Electra\Migrate\Event\MigrateAll;

use Electra\Core\Event\AbstractResponse;

/**
 * Class MigrateAllResponse
 * @package Electra\Migrate\Event\MigrateAll
 * @method static create($data = [])
 */
class MigrateAllResponse extends AbstractResponse
{
  /** @var bool */
  public $success;

  /** @var int */
  public $executedMigrationsCount;
}