<?php

namespace Electra\Migrate\Cli\Migrate;

use Electra\Core\Context\ContextAware;
use Electra\Migrate\Context\MigrateContext;
use Symfony\Component\Console\Command\Command;

abstract class AbstractMigrateCommand extends Command
{
  use ContextAware;

  /**
   * AbstractMigrateCommand constructor.
   * @throws \Exception
   */
  public function __construct()
  {
    parent::__construct();
    $this->setContext(MigrateContext::create());
  }
}
