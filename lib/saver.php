<?php
/** Стандарные методы сохранения */

interface standartStoreMethods {
    /** Сохраняет объект в базе
     * @return true | false
     */
    public function save();

    /**Загружает объект из базы
     * @return true | false
     */
    public function load( $id );

    /**Удаляет объект из базы
     * @return true | false
     */
    public function delete();
}
?>
