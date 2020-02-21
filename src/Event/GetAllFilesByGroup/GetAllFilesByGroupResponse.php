<?php

namespace Electra\Migrate\Event\GetAllFilesByGroup;

use Electra\Core\Event\AbstractResponse;

/**
 * Class GetAllFilesByGroupResponse
 * @method static $this create($data = [])
 */
class GetAllFilesByGroupResponse extends AbstractResponse
{
  /** @var array */
  public $filesByGroup;
}