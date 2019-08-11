<?php

namespace Electra\Migrate\Event\GetAllFilesByGroup;

use Electra\Core\Event\AbstractPayload;
use Symfony\Component\Console\Output\Output;

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
      'migrationDirs' => 'array',
      'output' => Output::class
    ];
  }

  /** @return GetAllFilesByGroupPayload */
  public static function create(): GetAllFilesByGroupPayload
  {
    return new self();
  }
}