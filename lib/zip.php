<?php

/**
 * 
 * ����� ��� ������ � ZIP-��������
 *
 */


class zip {

    /**
     * ����������� 1 ���� �� ������
     * @param <type> $zipfile
     * @param <type> $filename2
     * @param <type> $dest
     * @return ZipArchive
     */
    function extractOneFile ( $zipfile, $filename2, $dest='.' ){
        $zip = new ZipArchive;

        // �������� ������ ��� �����
        $pfile = pathinfo($filename2);  $just_filename = str_replace($pfile['dirname'].'/', '', $filename2);

        // ��������� ��� ����� ����� �������� � �������� �������
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
     * ����������� ��� ����� � �����
     * @param <type> $zipfile
     * @param <type> $dest ����� ��� ���������� ������
     * @return ZipArchive
     */
    function extractAllFiles ( $zipfile, $dest='.' )
    {
        $zip = new ZipArchive;
        if ( $zip->open( $zipfile ) ){

            for ( $i=0; $i < $zip->numFiles; $i++ ) {

                $entry = $zip->getNameIndex($i);
                if ( substr( $entry, -1 ) == '/' ) continue; // skip directories

                // ��������� ��� ����� ����� �������� � �������� �������
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
