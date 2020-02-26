<?php
require('fpdf.php');
include 'barcode.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// For demonstration purposes, get pararameters that are passed in through $_GET or set to the default value
$filepath = (isset($_GET["filepath"])?$_GET["filepath"]:"");
$text = (isset($_GET["text"])?$_GET["text"]:"0");
$size = (isset($_GET["size"])?$_GET["size"]:"20");
$orientation = (isset($_GET["orientation"])?$_GET["orientation"]:"horizontal");
$code_type = (isset($_GET["codetype"])?$_GET["codetype"]:"code128");
$print = (isset($_GET["print"])&&$_GET["print"]=='true'?true:false);
$sizefactor = (isset($_GET["sizefactor"])?$_GET["sizefactor"]:"1");

// This function call can be copied into your project and can be made from anywhere in your code
$url = 'barcode.php?size=50&text=12654785214000';
$image1 = "im.png";


/*$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(40,10,'<img src=" '. $url . '"></td></tr></table>');
//$pdf->Image(barcode( $filepath, $text, $size, $orientation, $code_type, $print, $sizefactor ), 10, 10, -300);
$pdf->Output();*/

//$pdf->Output('D', 'test.pdf');

?>