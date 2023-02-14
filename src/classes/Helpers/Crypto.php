<?php

namespace Dolphy\Core\Helpers;

final class Crypto {

  const CIPHER = 'AES-128-CBC';

  public static function rand($length = 10) {
    if (!is_int($length)) $length = 10;

    $range = array_merge(range('a', 'z'), range('A', 'Z'), range(0, 9));
    $rangeLength = count($range);

    $value = '';

    for ($i = 0; $i < $length; $i++) {
      $value .= $range[mt_rand(0, $rangeLength - 1)];
    }

    return $value;
  }

  public static function encrypt($data) {
    $key = self::rand(25);
    $data = serialize($data);

    $ivLength = openssl_cipher_iv_length(self::CIPHER);
    $iv = openssl_random_pseudo_bytes($ivLength);
    $ciphertext = openssl_encrypt($data, self::CIPHER, $key, 0, $iv);

    $result = array($key, $iv, $ciphertext);
    return base64_encode(implode('|||', $result));
  }

  public static function decrypt($data) {
    if (($data = base64_decode($data)) !== false) {
      $chunk = explode('|||', $data);
      if (count($chunk) !== 3) return NULL;

      list($key, $iv, $ciphertext) = explode('|||', $data);
      $data = openssl_decrypt($ciphertext, self::CIPHER, $key, 0, $iv);

      return unserialize($data);
    }

    return NULL;
  }

}
