<?php

/**
 * 
 * КЛАСС ДЛЯ РАБОТЫ С ZIP-АРХИВАМИ
 *
 */


class zip {

    /**
     * Распаковать 1 файл из архива
     * @param <type> $zipfile
     * @param <type> $filename2
     * @param <type> $dest
     * @return ZipArchive
     */
    function extractOneFile ( $zipfile, $filename2, $dest='.' ){
        $zip = new ZipArchive;

        // выделяем только имя файла
        $pfile = pathinfo($filename2);  $just_filename = str_replace($pfile['dirname'].'/', '', $filename2);

        // учитываем что криво класс работает с русскими файлами
        $dos_name =  iconv('UTF-8//TRANSLIT', 'cp866', $just_filename );

        if ( $zip->open( $zipfile ) ){
                $fp = $zip->getStream($dos_name); //file inside archive
                if(!$fp)  die("Error: can't get stream to zipped file");

                $ofp = fopen( $filename2, 'w' );
                if ( ! $fp )    throw new Exception('Unable to extract the file.');

                while ( ! feof( $fp ) )
                    fwrite( $ofp, fread($fp, 8192) );
        }
        fclose($fp);
        fclose($ofp);
        $zip->close();
        return;    
    }


    /**
     * Распаковать Все файлы в папку
     * @param <type> $zipfile
     * @param <type> $dest папка для распаковки архива
     * @return ZipArchive
     */
    function extractAllFiles ( $zipfile, $dest='.' )
    {
        $zip = new ZipArchive;
        if ( $zip->open( $zipfile ) ){

            for ( $i=0; $i < $zip->numFiles; $i++ ) {

                $entry = $zip->getNameIndex($i);
                if ( substr( $entry, -1 ) == '/' ) continue; // skip directories

                // учитываем что криво класс работает с русскими файлами
                $rus_name =  iconv('cp866', 'UTF-8//TRANSLIT', $entry );

                $fp = $zip->getStream( $entry );
                $ofp = fopen( $dest.'/'.$rus_name, 'w' );
                if ( ! $fp )
                    throw new Exception('Unable to extract the file.');

                while ( ! feof( $fp ) )
                    fwrite( $ofp, fread($fp, 8192) );

                fclose($fp);
                fclose($ofp);
            }
            $zip->close();
        }
        else
            return false;

        return $zip;
    }
	
}
?>
