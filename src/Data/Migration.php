<?php

namespace Electra\Migrate\Data;

use Carbon\Carbon;
use Electra\Dal\Data\Collection;
use Electra\Utility\Objects;

class Migration
{
  /** @var MigrationModel */
  private static $model;

  /** @var int */
  public $id;
  /** @var string */
  public $group;
  /** @var string */
  public $name;
  /** @var Carbon */
  public $executed;
  /** @var integer */
  public $batch;
  /** @var Carbon */
  public $created;
  /** @var Carbon */
  public $updated;

  /**
   * @return MigrationModel
   * @throws \Exception
   */
  private static function getModel(): MigrationModel
  {
    if (!self::$model)
    {
      self::$model = new MigrationModel();
    }

    return clone self::$model;
  }

  /**
   * @param \stdClass | array | object $data
   * @return Migration
   * @throws \Exception
   */
  public static function create($data = []): ?self
  {
    if (is_null($data))
    {
      return null;
    }

    return Objects::hydrate(new self(), (object)$data);
  }

  /**
   * @return Migration|null
   * @throws \Exception
   */
  public function save()
  {
    if (!$this->id)
    {
      $this->created = new Carbon();
    }
    $this->updated = new Carbon();
    $model = Objects::copyAllProperties($this, self::getModel());
    $model->save();

    if (!$this->id)
    {
      $this->id = $model->id;
    }

    return $this;
  }

  /**
   * @throws \Exception
   */
  public function delete()
  {
    return self::getModel()->destroy($this->id);
  }

  /**
   * @param $id
   * @return Migration
   * @throws \Exception
   */
  public static function getById($id)
  {
    $model = self::getModel()
      ->query()
      ->where('id', '=', $id)
      ->get()
      ->first();

    return self::create($model);
  }

  /**
   * @return Migration
   * @throws \Exception
   */
  public static function getLastExecuted()
  {
    $model = self::getModel()
      ->query()
      ->where('executed', '<>', null)
      ->orderBy('executed', 'desc')
      ->take(1)
      ->get()
      ->first();

    return self::create($model);
  }

  /**
   * @return Collection
   * @throws \Exception
   */
  public static function getAll()
  {
    $modelCollection = self::getModel()->all();

    $entityCollection = new Collection();

    foreach ($modelCollection->all() as $model)
    {
      $entityCollection->add(self::create($model));
    }

    return $entityCollection;
  }

  /**
   * @return Collection
   * @throws \Exception
   */
  public static function getAllExecuted()
  {
    $modelCollection = self::getModel()
      ->query()
      ->where('executed', '<>', null)
      ->orderBy('name', 'ASC')
      ->get();

    $entityCollection = new Collection();

    foreach ($modelCollection->all() as $model)
    {
      $entityCollection->add(self::create($model));
    }

    return $entityCollection;
  }

  /**
   * @param int $batch
   * @return Collection
   * @throws \Exception
   */
  public static function getAllByBatch(int $batch)
  {
    $modelCollection = self::getModel()
      ->query()
      ->where('batch', '=', $batch)
      ->orderBy('name', 'ASC')
      ->get();

    $entityCollection = new Collection();

    foreach ($modelCollection->all() as $model)
    {
      $entityCollection->add(self::create($model));
    }

    return $entityCollection;
  }

  /**
   * @return Migration[]
   * @throws \Exception
   */
  public static function getAllExecutedIndexedByGroupAndName()
  {
    $entityCollection = self::getAllExecuted();

    $entityArray = [];

    /** @var Migration $entity */
    foreach ($entityCollection->all() as $entity)
    {
      $entityArray[$entity->group . '_' . $entity->name] = $entity;
    }

    return $entityArray;
  }

  /**
   * @return array
   * @throws \Exception
   */
  public static function getAllByGroupIndexedByName(): array
  {
    $entityCollection = self::getAll();
    $entityArray = [];

    /** @var Migration $entity */
    foreach ($entityCollection->all() as $entity)
    {
      if (!isset($entityArray[$entity->group]))
      {
        $entityArray[$entity->group] = [];
      }

      $entityArray[$entity->group][$entity->name] = $entity;
    }

    return $entityArray;
  }

}