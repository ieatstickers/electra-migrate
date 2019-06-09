<?php

namespace Electra\Module\Migration\Task\GetAllFilesByGroup;

use Electra\Core\Task\AbstractResponse;

class GetAllFilesByGroupResponse extends AbstractResponse
{
  /** @var array */
  public $filesByGroup;
}