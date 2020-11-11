<?php

namespace Electra\Migrate\Event\GetAllFilesByGroup;

use Electra\Core\Event\AbstractPayload;
use Electra\Core\Event\Type\Type;
use Symfony\Component\Console\Output\Output;

/**
 * Class GetAllFilesByGroupPayload
 * @method static $this create($data = [])
 */
class GetAllFilesByGroupPayload extends AbstractPayload
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
      'migrationDirs' => Type::array(),
      'output' => Type::class(Output::class)
    ];
  }
}
