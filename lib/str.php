<?php
/**
 * основные строковые функции
 */
class str {

    /**
     * Функция заменяет в строке символ \n на html-аналог <br>
     *
     * @param <string> $txt
     * @return <string>
     */
    function n2br($txt){
        return nl2br($txt);
    }

    public static function  get_substr( $str, $strlen, $str_more=" ..." ) {
        return preg_replace('~^(.{' . $strlen . '}\S*)\s.+$~us', '$1' . $str_more, $str);
    }

    public static function  get_substr_clean( $str, $strlen ){
            return str::get_substr( strip_tags($str), $strlen );
    }

    /**
     * распознает в тексте ссылки и почту
     * @param <type> $text
     * @return <string> возвращает измененую строку
     */
    public static function recognizeLinks($text) {
        preg_match_all('~((http://|https://|ftp://)?([a-zA-Z0-9_\.\-]+\.[a-z]{2,4})(:[0-9]+)?(/[^\?\s/<]*)*(\?[^\s#<]*)?(#[^\s<]*)?)~', $text, $matches);
        if (count($matches)) {
            foreach ($matches[0] as $i => $t) {
                $url = $t;
                if (! preg_match('/^(http|https|ftp)/', $url))
                    $url = 'http://' . $url;
                $link = "<a href='{$url}' target='_blank' rel='nofollow'>{$t}</a>";
                $text = str_replace($t, $link, $text);
            }
        }
        preg_match_all('~(<a([^>]+)>([^<]+)</a>@<a([^>]+)>([^<]+)</a>)~', $text, $matches);
        if (count($matches)) {
            foreach ($matches[0] as $i => $t) {
                $email = $matches[3][$i] . '@' . $matches[5][$i];
                $link = "<a href='mailto:{$email}'>{$email}</a>";
                $text = str_replace($t, $link, $text);
            }
        }
        preg_match_all('~(([a-zA-Z0-9\._%\-]+)@<a([^>]+)>([^<]+)</a>)~', $text, $matches);
        if (count($matches)) {
            foreach ($matches[0] as $i => $t) {
                $email = $matches[2][$i] . '@' . $matches[4][$i];
                $link = "<a href='mailto:{$email}'>{$email}</a>";
                $text = str_replace($t, $link, $text);
            }
        }

        return $text;
    }

    
}


?>
