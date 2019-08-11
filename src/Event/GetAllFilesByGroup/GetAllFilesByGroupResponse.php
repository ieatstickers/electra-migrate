<?php

namespace Electra\Migrate\Event\GetAllFilesByGroup;


use Electra\Core\Event\AbstractResponse;

class GetAllFilesByGroupResponse extends AbstractResponse
{
  /** @var array */
  public $filesByGroup;

  /** @return GetAllFilesByGroupResponse */
  public static function create(): GetAllFilesByGroupResponse
  {
    return new self();
  }
}