<?php

namespace Electra\Module\Migration;

use Illuminate\Database\Migrations\Migration as IlluminateMigration;

abstract class Migration extends IlluminateMigration
{
  /** @return mixed */
  abstract public function up();

  /** @return mixed */
  abstract public function down();
}