<?php

namespace Electra\Module\Migration\Task\MigrateMake;

use Electra\Core\Task\AbstractTask;

class MigrateMakeTask extends AbstractTask
{
  /** @return string */
  public function getPayloadClass(): string
  {
    return MigrateMakePayload::class;
  }

  /**
   * @param MigrateMakePayload $payload
   * @return MigrateMakeResponse
   */
  protected function process($payload): MigrateMakeResponse
  {
    return new MigrateMakeResponse();
  }

}