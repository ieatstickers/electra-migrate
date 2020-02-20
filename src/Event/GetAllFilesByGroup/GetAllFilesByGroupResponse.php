<?php

namespace Electra\Migrate\Event\GetAllFilesByGroup;

use Electra\Core\Event\AbstractResponse;

/**
 * Class GetAllFilesByGroupResponse
 * @package Electra\Migrate\Event\GetAllFilesByGroup
 * @method static create($data = [])
 */
class GetAllFilesByGroupResponse extends AbstractResponse
{
  /** @var array */
  public $filesByGroup;
}