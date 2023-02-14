<?php

namespace Dolphy\Core\Helpers;

class Directory {

  static function load($path, $recursive = false) {
    if (!is_bool($recursive)) $recursive = false;
    if (!is_dir($path)) return;

    // Open Directory
    if (($handle = opendir($path)) !== false) {
      while (($entry = readdir($handle)) !== false) {
        if ($entry === '.' || $entry === '..') continue;

        $filepath = "{$path}/{$entry}";
        if (is_dir($filepath)) {
          if ($recursive) {
            self::load($filepath, $recursive);
          }
          continue;
        }

        if (!is_file($filepath)) continue;
        if (substr($entry, -4) !== '.php') continue;

        require_once $filepath;
      }
      // Close directory
      closedir($handle);
    }
  }

}

