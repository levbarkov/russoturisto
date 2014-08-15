<?php
/**
 * Класс работы с русским языком, содержит часто используемые названия
 */
class ru {

    /** возвращает название месяца в именительном падеже
     *
     * @param <INT> $GGm
     * @return <STRING>
     */
    function GGgetMonthName($GGm) {
            switch ($GGm){
                    case 1: return "Январь"; break;
                    case 2: return "Февраль"; break;
                    case 3: return "Март"; break;
                    case 4: return "Апрель"; break;
                    case 5: return "Май"; break;
                    case 6: return "Июнь"; break;
                    case 7: return "Июль"; break;
                    case 8: return "Август"; break;
                    case 9: return "Сентябрь"; break;
                    case 10: return "Октябрь"; break;
                    case 11: return "Ноябрь"; break;
                    case 12: return "Декабрь"; break;
                    case 0: return ""; break;
            }
    }

    /** воздвращает название месяца в именительном падеже
     *
     * @param <INT> $GGm
     * @return <STRING>
     */
    function GGgetMonthNames($GGm) {
            switch ($GGm){
                    case 1: return "Января"; break;
                    case 2: return "Февраля"; break;
                    case 3: return "Марта"; break;
                    case 4: return "Апреля"; break;
                    case 5: return "Мая"; break;
                    case 6: return "Июня"; break;
                    case 7: return "Июля"; break;
                    case 8: return "Августа"; break;
                    case 9: return "Сентября"; break;
                    case 10: return "Октября"; break;
                    case 11: return "Ноября"; break;
                    case 12: return "Декабря"; break;
                    case 0: return ""; break;
            }
    }
    function GGgetWeekName($GGm) {
            switch ($GGm){
                    case 0: return "воскресенье"; break;
                    case 1: return "понедельник"; break;
                    case 2: return "вторник"; break;
                    case 3: return "среда"; break;
                    case 4: return "четверг"; break;
                    case 5: return "пятница"; break;
                    case 6: return "суббота"; break;
            }
    }

}
?>
