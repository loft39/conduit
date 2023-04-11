<?php

namespace Conduit\Database;

use PDO;

class KeyValueStore extends Database {

  public function readAll(): array {
    $kv = $this->dbObject->prepare("SELECT * from `keyvalue`");
    $kv->execute();
    $items = $kv->fetchAll(PDO::FETCH_ASSOC);
    $return = [];

    foreach ($items as $item) {
      $return[$item['k']] = $item['v'];
    }

    return $return;
  }

  public function read($key): array {
    $kv = $this->dbObject->prepare("SELECT * from `keyvalue` where `k` = :key");
    $kv->execute([':key' => $key]);
    return $kv->fetch();
  }

  public function write($key, $value): bool
  {
    $kv = $this->dbObject->prepare(
      "INSERT INTO `keyvalue` (`k`, `v`)
               VALUES (:key, :value)
               ON DUPLICATE KEY UPDATE `v` = :value;");
    $kv->execute([':key' => $key, ':value' => $value]);

    return true;
  }

  public function delete($key): bool
  {
    $kv = $this->dbObject->prepare(
        "DELETE FROM `keyvalue` where `k` = :key;");
    $kv->execute([':key' => $key]);

    return true;
  }

}