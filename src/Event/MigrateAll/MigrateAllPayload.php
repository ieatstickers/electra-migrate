<?php

namespace Electra\Migrate\Event\MigrateAll;

use Electra\Core\Event\AbstractPayload;
use Symfony\Component\Console\Output\Output;

class MigrateAllPayload extends AbstractPayload
{
  /** @var array */
  public $migrationDirs;

  /** @var Output */
  public $output;

  /** @return array */
  public function getRequiredProperties(): array
  {
    return [ 'migrationDirs' ];
  }

  /** @return array */
  public function getPropertyTypes(): array
  {
    return [
      'migrationDirs' => 'array',
      'output' => Output::class
    ];
  }

  /** @return MigrateAllPayload */
  public static function create(): MigrateAllPayload
  {
    return new self();
  }
}