<?php

namespace Dolphy\Core\Database;

use Dolphy\Core\Exceptions\Exception;
use Dolphy\Helpers\DB;

class BaseTable {

  const TABLE = '';

  final public static function table() {
    return DB::tbl(static::TABLE);
  }

  final public static function findBy($field, $value) {
    return self::findWhere(array($field => $value));
  }

  final public static function findWhere(array $where) {
    global $wpdb;

    $table = self::table();
    $query = "SELECT * FROM `{$table}`";

    if (count($where)) {
      foreach ($where as $field => $value) {
        $conds[] = $wpdb->prepare("`{$field}` = %s", $value);
      }

      $query .= " WHERE ".implode(" AND ", $conds);
    }

    return $wpdb->get_row($query);
  }

  final public static function insert(array $data, $format = null) {
    global $wpdb;

    $wpdb->insert(self::table(), $data, $format);
    if ($wpdb->insert_id === false) return false;

    return self::findBy('id', $wpdb->insert_id);
  }

  final public static function delete(array $where, $where_format = null) {
    global $wpdb;
    $wpdb->delete(self::table(), $where, $where_format);
    // return self::findBy('id', $wpdb->insert_id);
  }

}
