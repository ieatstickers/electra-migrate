<?php

namespace Electra\Module\Migration\Task\MigrateRollback;

use Electra\Core\Task\AbstractPayload;
use Symfony\Component\Console\Output\Output;

class MigrateRollbackPayload extends AbstractPayload
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
}