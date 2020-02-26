<?php

if( isset($_POST['filename']) ) {
    $filepath = dirname(__FILE__, 3) . '/uploads/pdfs/'.basename($_POST['filename']);
    unlink( $filepath );
    echo 'detete '. basename($_POST['filename']) ;
}