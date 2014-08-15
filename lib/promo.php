<?php
/**
 * Класс для програмной оптимизации
 * инициализируется в первичном файле /index.php
 *
 * пример использования: <img alt="<?=$reg['promo']->getNextKeyword() ?>" >
 */
class promo {

    /** Массив ключевых фраз */
    var $keyWords = array(
        'пример 1',
        'пример 2',
        'пример 3',
        'пример 4',
        'пример 5',
        'пример 6',
        'пример 7'
    );

    /** указатель на текущую ключевую фразу */
    var $keyIndex = 0;

    function __construct(){
        $this->keyWordsCnt = count ($this->keyWords);
        $this->data = ggo (1, "#__promo");
    }

    /**
     * получить следующую ключевую фразу
     * @return <string>
     */
    function getNextKeyword() {
        if (  $this->keyIndex >= $this->keyWordsCnt  )  $this->keyIndex=0;
        return $this->keyWords[$this->keyIndex++];
    }

    /**
     * Определяем по адресу тип статистики и возврощаем фото
     * @param <string> $url
     * @return <string>
     */
    function get_stat_img ($url){
        if (  preg_match("/metrika.yandex.ru/", $url)  ) return "yandex-metrika.gif";
        return '';
    }

    

}
?>