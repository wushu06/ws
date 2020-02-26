<?php
$msg = "Plugin First line of text\nSecond line of text";


$option  = get_option ('hmu_api_cron');


if( $option ) {


        $msg = 'yes option';


}else {
    $msg = 'no opyions';
}



// send email

mail("nour@thebiggerboat.co.uk","My subject",$msg);
