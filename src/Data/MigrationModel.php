<?php

namespace Electra\Migrate\Data;

use Carbon\Carbon;
use Electra\Dal\Database\Mysql\IndependentModel;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class User
 * @property int $id;
 * @property string $group;
 * @property string $name;
 * @property Carbon $executed;
 */
class MigrationModel extends IndependentModel
{
  protected string $connection = 'electra';
  protected string $table = 'migration';
  protected $dates = [ 'executed' ];

  /**
   * @param Blueprint $table
   * @return mixed|void
   */
  public function createTable(Blueprint $table)
  {
    $table->increments('id');
    $table->string('group');
    $table->string('name');
    $table->dateTime('executed')->nullable();
    $table->integer('batch');
    $table->dateTime('created');
    $table->dateTime('updated');
  }

}
