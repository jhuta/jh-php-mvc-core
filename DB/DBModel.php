<?php

namespace jhuta\phpmvc\DB;

use jhuta\phpmvc\Application;
use jhuta\phpmvc\Model;

abstract class DBModel extends Model {

  abstract public function tableName(): string;
  abstract public function attributes(): array;
  abstract public function primaryKey(): string;

  public function save() {
    $tableName = $this->tableName();
    $attributes = $this->attributes();

    $params = array_map(fn ($attr) => ":{$attr}", $attributes);
    $implodeColumns = implode(',', $attributes);
    $implodeParams = implode(',', $params);

    $sql = "INSERT INTO {$tableName} ({$implodeColumns}) VALUES ({$implodeParams});";
    $statement = self::prepare($sql);
    foreach ($attributes as $attribute) {
      $statement->bindValue(":{$attribute}", $this->{$attribute});
    }
    $statement->execute();
    return true;
  }

  public function findOne($where) {
    $tableName = static::tableName();
    $attributes = array_keys($where);
    $attr_map = array_map(fn ($attr) => "{$attr} = :{$attr}", $attributes);
    $where_implode = implode(" AND ", $attr_map);
    $sql = "SELECT * FROM {$tableName} WHERE {$where_implode}";
    $stmt = self::prepare($sql);
    foreach ($where as $key => $item) {
      $stmt->bindValue(":{$key}", $item);
    }
    $stmt->execute();
    return $stmt->fetchObject(static::class);
  }

  public static function prepare($sql) {
    return Application::$app->db->pdo->prepare($sql);
  }
}
