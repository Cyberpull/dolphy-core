<?php

namespace Dolphy\Core\Helpers;

class DB {

  const PREFIX = 'dolphy_';

  static function tbl($name, $prefixed = true) {
    global $wpdb;

    if (!is_bool($prefixed)) $prefixed = true;

    $table = (Plugin::isActiveForNetwork()) ? $wpdb->base_prefix : $wpdb->prefix;
    if ($prefixed) $table .= self::PREFIX;
    $table .= $name;

    return $table;
  }

  static function get_foreign_key($table, $name) {
    global $wpdb;
    $query = $wpdb->prepare("SELECT * FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND CONSTRAINT_NAME   = %s AND CONSTRAINT_TYPE   = 'FOREIGN KEY'", $name);
    return $wpdb->get_row($query);
  }

  static function drop_foreign_key($table, $name) {
    global $wpdb;

    if (self::get_foreign_key($table, $name)) {
      $query = $wpdb->prepare("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$name}`");
      $wpdb->query($query);
    }
  }

  static function create_foreign_keys($table, $map) {
    global $wpdb;

    if (!is_array($map)) $map = array();
    if (!count($map)) return;

    $constraintList = array();
    $sql = "ALTER TABLE `$table` ";

    foreach ($map as $reference => list($tbl_field, $ref_field)) {
      $constraint = "fk_" . $table . "_to_" . $reference;

      if (!DB::get_foreign_key($table, $constraint)) {
        $constraintEntry = "ADD CONSTRAINT `$constraint` FOREIGN KEY (`$tbl_field`) REFERENCES `$reference`(`$ref_field`)";
        $constraintEntry .= " ON UPDATE CASCADE ON DELETE CASCADE";
        $constraintList[] = $constraintEntry;
      }
    }

    if (count($constraintList)) {
      $sql .= implode(", ", $constraintList);
      $wpdb->query($sql);
    }
  }

  static function create_triggers($table) {
    self::create_insert_trigger($table);
    self::create_update_trigger($table);
  }

  static function create_insert_trigger($table) {
    global $wpdb;

    $before_insert = $table."_before_insert";

    $wpdb->query("DROP TRIGGER IF EXISTS $before_insert");
    mysqli_multi_query($wpdb->dbh, "
    CREATE TRIGGER $before_insert
    BEFORE INSERT
    ON `$table`
    FOR EACH ROW
    BEGIN
    SET NEW.createdAt = NOW();
    SET NEW.updatedAt = NOW();
    END;
    ");
  }

  static function create_update_trigger($table) {
    global $wpdb;

    $before_update = $table."_before_update";

    $wpdb->query("DROP TRIGGER IF EXISTS $before_update");
    mysqli_multi_query($wpdb->dbh, "
    CREATE TRIGGER $before_update
    BEFORE UPDATE
    ON `$table`
    FOR EACH ROW
    BEGIN
    SET NEW.updatedAt = NOW();
    END;
    ");
  }

}
