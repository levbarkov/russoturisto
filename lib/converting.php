<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class converting{

    /**
     * ПЕРЕВОДИМ В КОДИРОВКУ UTF-8
     * @param <str> $e24ss
     * @return <str>
     */
    function utf82win1251 ($e24ss) {
    //    $mess=shift;
        $conv='';
            $utf8 = array(
                                    chr(0xD0).chr(0xB0),chr(0xD0).chr(0xB1),chr(0xD0).chr(0xB2),chr(0xD0).chr(0xB3),chr(0xD0).chr(0xB4),
                                    chr(0xD0).chr(0xB5),chr(0xD1).chr(0x91),chr(0xD0).chr(0xB6),chr(0xD0).chr(0xB7),chr(0xD0).chr(0xB8),
                                    chr(0xD0).chr(0xB9),chr(0xD0).chr(0xBA),chr(0xD0).chr(0xBB),chr(0xD0).chr(0xBC),chr(0xD0).chr(0xBD),
                                    chr(0xD0).chr(0xBE),chr(0xD0).chr(0xBF),chr(0xD1).chr(0x80),chr(0xD1).chr(0x81),chr(0xD1).chr(0x82),
                                    chr(0xD1).chr(0x83),chr(0xD1).chr(0x84),chr(0xD1).chr(0x85),chr(0xD1).chr(0x86),chr(0xD1).chr(0x87),
                                    chr(0xD1).chr(0x88),chr(0xD1).chr(0x89),chr(0xD1).chr(0x8A),chr(0xD1).chr(0x8B),chr(0xD1).chr(0x8C),
                                    chr(0xD1).chr(0x8D),chr(0xD1).chr(0x8E),chr(0xD1).chr(0x8F),chr(0xD0).chr(0x90),chr(0xD0).chr(0x91),
                                    chr(0xD0).chr(0x92),chr(0xD0).chr(0x93),chr(0xD0).chr(0x94),chr(0xD0).chr(0x95),chr(0xD0).chr(0x81),
                                    chr(0xD0).chr(0x96),chr(0xD0).chr(0x97),chr(0xD0).chr(0x98),chr(0xD0).chr(0x99),chr(0xD0).chr(0x9A),
                                    chr(0xD0).chr(0x9B),chr(0xD0).chr(0x9C),chr(0xD0).chr(0x9D),chr(0xD0).chr(0x9E),chr(0xD0).chr(0x9F),
                                    chr(0xD0).chr(0xA0),chr(0xD0).chr(0xA1),chr(0xD0).chr(0xA2),chr(0xD0).chr(0xA3),chr(0xD0).chr(0xA4),
                                    chr(0xD0).chr(0xA5),chr(0xD0).chr(0xA6),chr(0xD0).chr(0xA7),chr(0xD0).chr(0xA8),chr(0xD0).chr(0xA9),
                                    chr(0xD0).chr(0xAA),chr(0xD0).chr(0xAB),chr(0xD0).chr(0xAC),chr(0xD0).chr(0xAD),chr(0xD0).chr(0xAE),
                                    chr(0xD0).chr(0xAF),chr(0xD1).chr(0x97),chr(0xD0).chr(0x87),chr(0xD1).chr(0x94),chr(0xD1).chr(0x96),
                                    chr(0xD0).chr(0x86),chr(0xD2).chr(0x91),chr(0xD2).chr(0x90),chr(0xD1).chr(0x9E),chr(0xD0).chr(0x8E),
                                    chr(0xD1).chr(0x91),chr(0xD0).chr(0x81),chr(0xD0).chr(0x84),'в„–',chr(160)
                                    );
            $win = array(
                                    'а','б','в','г','д',
                                    'е','ё','ж','з','и',
                                    'й','к','л','м','н',
                                    'о','п','р','с','т',
                                    'у','ф','х','ц','ч',
                                    'ш','щ','ъ','ы','ь',
                                    'э','ю','я','А','Б',
                                    'В','Г','Д','Е','Ё',
                                    'Ж','З','И','Й','К',
                                    'Л','М','Н','О','П',
                                    'Р','С','Т','У','Ф',
                                    'Х','Ц','Ч','Ш','Щ',
                                    'Ъ','Ы','Ь','Э','Ю',
                                    'Я','ї','Ї','є','і',
                                    'І','ґ','Ґ','ў','Ў',
                                    'ё','Ё','Є','№',' ');

            $i=0;
            for($i = 0; $i < count($utf8); $i++) {
            //	print ($i);
                    $e24ss = str_replace( $utf8[$i], $win[$i], $e24ss );
            //    $mess=~s/$sym/$conv{$sym}/g;
            }
            return $e24ss;
    }

}

?>
