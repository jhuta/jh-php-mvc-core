<?php

namespace jhuta\phpmvc\DB;

use jhuta\phpmvc\Application;

class Database {

  public \PDO $pdo;

  public function __construct(array $config) {
    $dsn = $config['dsn'] ?? '';
    $user = $config['user'] ?? '';
    $pass = $config['pass'] ?? '';
    $this->pdo = new \PDO($dsn, $user, $pass);
    $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
  }

  public function applyMigrations() {
    $this->createMigrationTable();
    $appliedMigration = $this->getAppliedMigrations();

    $newMigrations = [];
    $files = scandir(Application::$ROOT_DIR . '/migrations');
    // usuń już dodane
    $toApplyMigration = array_diff($files, $appliedMigration);
    // var_dump($toApplyMigration);
    foreach ($toApplyMigration as $migration) {
      if (
        $migration === '.'
        || $migration === '..'
        || $migration === '_m.php'
      ) {
        continue;
      }
      require_once Application::$ROOT_DIR . '/migrations/' . $migration;
      $className = pathinfo($migration, PATHINFO_FILENAME);
      $instance = new $className;
      $this->log("Applying migration {$migration}");
      $instance->up();
      $this->log("Applied migration {$migration}");
      $newMigrations[] = $migration;
    }
    if (!empty($newMigrations)) {
      $this->saveMigrations($newMigrations);
    } else {
      $this->log("All migrations are applied");
    }
  }

  public function createMigrationTable() {
    $sql = "CREATE TABLE IF NOT EXISTS migrations (";
    $sql .= "id INT AUTO_INCREMENT PRIMARY KEY,";
    $sql .= "migration VARCHAR(255),";
    $sql .= "created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
    $sql .= ") ENGINE=INNODB;";
    $this->pdo->exec($sql);
  }

  public function getAppliedMigrations() {
    $sql = "SELECT migration FROM migrations;";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(\PDO::FETCH_COLUMN);
  }

  public function saveMigrations(array $migrations) {
    $strValues = implode(",", array_map(fn ($m) => "('$m')", $migrations));
    $sql = "INSERT INTO migrations (migration) VALUES $strValues;";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();
  }

  public function prepare($sql) {
    return $this->pdo->prepare($sql);
  }

  protected function log($message) {
    $dt = date('Y-m-d H:i:s');
    echo "[{$dt}] - {$message}" . PHP_EOL;
  }
}