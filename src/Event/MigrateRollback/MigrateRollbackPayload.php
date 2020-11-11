<?php

namespace Electra\Migrate\Event\MigrateRollback;

use Electra\Core\Event\AbstractPayload;
use Electra\Core\Event\Type\Type;
use Symfony\Component\Console\Output\Output;

/**
 * Class MigrateRollbackPayload
 * @method static $this create($data = [])
 */
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
      'migrationDirs' => Type::array(Type::string()),
      'output' => Type::class(Output::class)
    ];
  }
}
