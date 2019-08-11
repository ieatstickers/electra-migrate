<?php

namespace Electra\Migrate\Event\GetAllFilesByGroup;


use Electra\Core\Event\AbstractResponse;

class GetAllFilesByGroupResponse extends AbstractResponse
{
  /** @var array */
  public $filesByGroup;
}