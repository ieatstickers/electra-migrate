<?php

namespace Electra\Migrate\Data;

use Carbon\Carbon;

class MigrationFile
{
  /** @var string */
  public $classname;
  /** @var string */
  public $name;
  /** @var string */
  public $filename;
  /** @var string */
  public $filepath;
  /** @var string */
  public $fqns;
  /** @var string */
  public $directoryKey;
  /** @var string */
  public $directoryDisplayName;
  /** @var Carbon */
  public $timestamp;
}