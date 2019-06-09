<?php

namespace Electra\Migrate\Task\MigrateRefresh;

use Electra\Core\Task\AbstractTask;

class MigrateRefreshTask extends AbstractTask
{
  /** @return string */
  public function getPayloadClass(): string
  {
    return MigrateRefreshPayload::class;
  }

  /**
   * @param MigrateRefreshPayload $payload
   * @return MigrateRefreshResponse
   */
  protected function process($payload): MigrateRefreshResponse
  {
    return new MigrateRefreshResponse();
  }

}