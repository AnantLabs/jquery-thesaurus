<?PHP
/*
* @package Thesaurus
* @author sheiko
* @copyright (c) Dmitry Sheiko http://dsheiko.com
*/

class Lib_Mbstring
{
    /**
     * Converts mb. string to set of its UTF8 codes
     * @param string $data
     * @return string
     */
    public static function ordString($data)
    {
        $mb_hex = '';
        $bytes = 4;
        for ($i = 0; $i < mb_strlen($data, 'UTF-8'); $i++) {
            $mb_hex .= sprintf("\\u%04x",
                self::ordUTF8(mb_substr($data, $i, 1, 'UTF-8'), 0, $bytes)
            );
        }
        return $mb_hex;
    }

    /**
     * Ord function extension taken from http://de2.php.net/manual/en/function.ord.php#78032
     * @param string $c
     * @param int $index
     * @param int $bytes
     * @return int
     */
    public static function ordUTF8($c, $index = 0, &$bytes = null)
    {
      $len = strlen($c);
      $bytes = 0;

      if ($index >= $len)
        return false;

      $h = ord($c{$index});

      if ($h <= 0x7F) {
        $bytes = 1;
        return $h;
      }
      else if ($h < 0xC2)
        return false;
      else if ($h <= 0xDF && $index < $len - 1) {
        $bytes = 2;
        return ($h & 0x1F) <<  6 | (ord($c{$index + 1}) & 0x3F);
      }
      else if ($h <= 0xEF && $index < $len - 2) {
        $bytes = 3;
        return ($h & 0x0F) << 12 | (ord($c{$index + 1}) & 0x3F) << 6
                                 | (ord($c{$index + 2}) & 0x3F);
      }
      else if ($h <= 0xF4 && $index < $len - 3) {
        $bytes = 4;
        return ($h & 0x0F) << 18 | (ord($c{$index + 1}) & 0x3F) << 12
                                 | (ord($c{$index + 2}) & 0x3F) << 6
                                 | (ord($c{$index + 3}) & 0x3F);
      }
      else
        return false;
    }
}