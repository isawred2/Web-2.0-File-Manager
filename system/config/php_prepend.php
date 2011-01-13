<?
/****************************************************************
*
*  This file should be defined as prepend file in php.ini
*
*****************************************************************/

// output header for zip compression
if (ini_get('zlib.output_compression') == 1) {
    $encode = $_SERVER["HTTP_ACCEPT_ENCODING"];
    if (strpos($encode, 'gzip') > -1) {
        header("Content-Encoding: gzip");
    } elseif (strpos($encode, 'deflate') > -1) {
        header("Content-Encoding: deflate");
    }
}

?>