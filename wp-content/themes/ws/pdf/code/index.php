<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('../fpdf.php');
require('../vendor/autoload.php');
$barcode = new \Com\Tecnick\Barcode\Barcode();
$MRP = 12456;
$MFGDate = 4561;
$EXPDate = 00021144;
$productData = "098{$MRP}10{$MFGDate}55{$EXPDate}";
$barcode = new \Com\Tecnick\Barcode\Barcode();
$bobj = $barcode->getBarcodeObj('C128C', "{$productData}", 450, 70, 'black', array(0, 0, 0, 0));
$imageData = $bobj->getPngData();

class PDF extends FPDF
{
    protected $col = 0; // Current column
    protected $y0;      // Ordinate of column start
    protected $B = 0;
    protected $I = 0;
    protected $U = 0;
    protected $HREF = '';
    protected $width;
    
    /*
     * html
     */
    function WriteHTML($html)
    {
        // HTML parser
        $html = str_replace("\n",' ',$html);
        $a = preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
        foreach($a as $i=>$e)
        {
            if($i%2==0)
            {
                // Text
                if($this->HREF)
                    $this->PutLink($this->HREF,$e);
                else
                    $this->Write(5,$e);
            }
            else
            {
                // Tag
                if($e[0]=='/')
                    $this->CloseTag(strtoupper(substr($e,1)));
                else
                {
                    // Extract attributes
                    $a2 = explode(' ',$e);
                    $tag = strtoupper(array_shift($a2));
                    $attr = array();
                    foreach($a2 as $v)
                    {
                        if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
                            $attr[strtoupper($a3[1])] = $a3[2];
                    }
                    $this->OpenTag($tag,$attr);
                }
            }
        }
    }

    function OpenTag($tag, $attr)
    {
        // Opening tag
        if($tag=='B' || $tag=='I' || $tag=='U')
            $this->SetStyle($tag,true);
        if($tag=='A')
            $this->HREF = $attr['HREF'];
        if($tag=='BR')
            $this->Ln(5);
    }

    function CloseTag($tag)
    {
        // Closing tag
        if($tag=='B' || $tag=='I' || $tag=='U')
            $this->SetStyle($tag,false);
        if($tag=='A')
            $this->HREF = '';
    }

    function SetStyle($tag, $enable)
    {
        // Modify style and select corresponding font
        $this->$tag += ($enable ? 1 : -1);
        $style = '';
        foreach(array('B', 'I', 'U') as $s)
        {
            if($this->$s>0)
                $style .= $s;
        }
        $this->SetFont('',$style);
    }

    function PutLink($URL, $txt)
    {
        // Put a hyperlink
        $this->SetTextColor(0,0,255);
        $this->SetStyle('U',true);
        $this->Write(5,$txt,$URL);
        $this->SetStyle('U',false);
        $this->SetTextColor(0);
    }
    /* end html */

    function Header()
    {
        // Page header
        global $title;

        $this->SetFont('Arial','B',15);

    /*    $w = $this->GetStringWidth($title)+6;
        $this->SetX((210-$w)/2);
        $this->SetDrawColor(51, 107, 117);
        $this->SetFillColor(51, 107, 117);
        $this->SetTextColor(255, 255, 255);
        $this->SetLineWidth(1);
        $this->Cell($w,9,$title,1,1,'C',true);
        $this->Ln(10);
        // Save ordinate
        $this->y0 = $this->GetY();*/
    }

    function Footer()
    {
        // Page footer
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->SetTextColor(128);
        $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
    }

    function SetCol($col)
    {
        // Set position at a given column
        $this->col = $col;
        $x = 10+$col*65;
        $this->SetLeftMargin($x);
        $this->SetX($x);
    }

    function AcceptPageBreak()
    {
        // Method accepting or not automatic page break
        if($this->col<2)
        {
            // Go to next column
            $this->SetCol($this->col+1);
            // Set ordinate to top
            $this->SetY($this->y0);
            // Keep on page
            return false;
        }
        else
        {
            // Go back to first column
            $this->SetCol(0);
            // Page break
            return true;
        }
    }

    function ChapterTitle($num, $label)
    {
        // Title
        $this->SetFont('Arial','',12);
        $this->SetFillColor(200,220,255);
        $this->Cell(0,6,"Chapter $num : $label",0,1,'L',true);
        $this->Ln(4);
        // Save ordinate
        $this->y0 = $this->GetY();
    }

    function ChapterBody($file)
    {
        // Read text file
        $txt = file_get_contents($file);
        // Font
        $this->SetFont('Times','',12);
        // Output text in a 6 cm width column
        $this->MultiCell(60,5,$txt);
        $this->Ln();
        // Mention
        $this->SetFont('','I');
        $this->Cell(0,5,'(end of excerpt)');
        // Go back to first column
        $this->SetCol(0);
    }


}









//$pdf = new PDF();
$image1 = '<div ><img  alt="Embedded Image" src="data:image/png;base64,'.base64_encode($imageData).'" /></div>';
/*$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(40,10,'Hello World33!');
$pdf->WriteHTML('<h1>image here</h1><img alt="Embedded Image" src="data:image/png;base64,'.base64_encode($imageData).'" />');*/

// $pdf->Output();
//$pdf->Output('D', 'test.pdf');




//  file_put_contents($targetPath . $timestamp . '.png', $imageData);

$TEMPIMGLOC = 'tempimg.png';

$dataURI    = $image1;
$dataPieces = explode(',',$dataURI);
$encodedImg = $dataPieces[1];
$decodedImg = base64_decode($encodedImg);

//  Check if image was properly decoded
if( $decodedImg!==false )
{
    //  Save image to a temporary location
    if( file_put_contents($TEMPIMGLOC,$decodedImg)!==false )
    {
        $pdf = new PDF();
        $title = iconv('UTF-8', 'windows-1252','20000 Leagues Under the Seas £$');
        $pdf->AddPage();


        //$pdf->SetTitle($title);
        // Insert a logo in the top-left corner at 300 dpi
        $pdf->Image('https://staging.waterstonesgiftcards.com/wp-content/uploads/2018/11/waterstone-ecard-with-no-info.jpg',0,0,210);
       // $pdf->Cell( 40, 60, $pdf->Image('https://staging.waterstonesgiftcards.com/wp-content/uploads/2019/05/gift-card.png', $pdf->GetX(), $pdf->GetY(), 100.78), 500, 0, 'L', false );



        $pdf->Cell(0,150,'Center text:',0,0,'C');
        $pdf->SetX(10);
        $pdf->Cell( 0, 200, 'Price', 0, 0, 'C' );
        $pdf->SetX(10);
        // Set font
        $pdf->SetFont('Arial','B',11);
        $pdf->Cell( 0, 265, 'You cancenter a line', 100, 100, 'C' );



        $pdf->Image($TEMPIMGLOC, 55, 125, 100);
        $pdf->Output();
        unlink($TEMPIMGLOC);
    }
}



?>