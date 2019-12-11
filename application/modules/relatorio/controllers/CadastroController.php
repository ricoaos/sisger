<?php

/*$pdf = new Zend_Pdf();
$pdfPage = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
$font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_COURIER);
$pdfPage->setFont($font, 12);
$pageHeight = $pdfPage->getHeight();
$pageWidth = $pdfPage->getWidth();


$image = Zend_Pdf_Image::imageWithPath(APPLICATION_PATH ."/../public/img/logo/rcControls.png");



$logoImageHeight = 40;
//VDEdit
$logoImageWidth = 100;
//VDEdit
$tableWidth = 568;
$startPoint = ($pageWidth - $tableWidth);
$endPoint = $startPoint + $tableWidth;
$botPoint = 10;
$topPoint = $pageHeight - 30;
//$pdfPage->setLineWidth('0.3')->setLineDashingPattern(array(3, 3, 3, 3))->drawLine($startPoint, $topPoint, $startPoint, $botPoint)->drawLine($endPoint, $topPoint, $endPoint, $botPoint)->drawLine($startPoint, $topPoint, $endPoint, $topPoint)->drawLine($startPoint, $botPoint, $endPoint, $botPoint)->drawLine($startPoint, $pageHeight - $logoImageHeight - 235, $endPoint, $pageHeight - $logoImageHeight - 235)->drawLine($startPoint, $pageHeight - $logoImageHeight - 235 - 325, $endPoint, $pageHeight - $logoImageHeight - 235 - 325);
//$pdfPage->setFillColor(Zend_Pdf_Color_Html::color('#16599D'))->drawRectangle($startPoint + 2, $topPoint - $logoImageHeight - 2, $endPoint, $topPoint);
$pdfPage->drawImage($image, $startPoint, $topPoint - $logoImageHeight - 1, $startPoint + $logoImageWidth, $topPoint);

$pdfPage->drawText('Relatório de Cargos/Funções', 220, 800, 'UTF-8');

$texto = 'Foi anunciado o lançamento da série M5 para o Eclipse 3.6, também denominado Hélio, em homenagem ao Deus Grego do Sol. Com base em versões anteriores, o Eclipse 3.6 M5 inclui diversas correções de bugs e apresenta novas funcionalidades.';
$conc = wordwrap($texto, 60, '\n');
$d = explode('\n', $conc);
$stringpos = 780; // posicao x do meu texto
$stringdif = 12; // diferença entre cada quebra de linha.
$pdfPage->setFont($font, 10);


foreach ($d as $c) {
    $pdfPage->drawText($c, 260, $stringpos, 'UTF-8');
    $stringpos = ($stringpos - $stringdif); // subtrai para que a linha fique embaixo
}

$pdf->pages[] = $pdfPage;
// $pdf->save('exemplo.pdf');
header('Content-type: application/pdf');
echo $pdf->render();  */


$mCargo = new Model_Sistema_Cargo();
$rCargo = $mCargo->fetchAll()->toArray();
$pdf = new Zend_Pdf();
$page = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);

// Image
$image_name = APPLICATION_PATH ."/../public/img/logo/rcControls.png";
$image = Zend_Pdf_Image::imageWithPath($image_name);
$page->drawImage($image, 30, 770, 130, 820);
/*$page->setLineWidth(1)->drawLine(25, 25, 570, 25);
//bottom horizontal
$page->setLineWidth(1)->drawLine(25, 25, 25, 820);
//left vertical
$page->setLineWidth(1)->drawLine(570, 25, 570, 820);
//right vertical
$page->setLineWidth(1)->drawLine(570, 820, 25, 820);*/
//top horizontal
$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 8);
$font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
$page->drawText('RELATÓRIO DE CARGOS/FUNÇÕES', 237, 780, 'UTF-8');
$text = "Member details";
$x0 = 50;
$x3 = 310;
$x1 = 150;
$x2 = 220;



//$page->setStyle($h3);
$page->drawLine(50, 244, 150, 244) ;  // HEAD Bottom
$page->drawLine(50, 244, 50, 166);    // MOST Left
$page->drawLine(512, 244, 512, 166) ; // MOST Right
$page->drawLine(50, 166, 512, 166) ;      // MOST Bottom
$page->drawLine(225, 244, 225, 166);      // MOST Left
//$page->setStyle($h2);
$page->drawText('Analytics Site', 50, 252);  // Table Headers
//$page->setStyle($h3);
$page->drawText('Page Views', 80, 225) ;          // Table Headers (402) - 27
$page->drawText('Bounce Rate', 80, 200) ;     // Table Headers
$page->drawText('Avg. Time On Site', 80, 174) ;   // Table Headers
$page->drawLine(50, 192, 512, 192) ;  // Bottom
$page->drawLine(50, 218, 512, 218) ;  // Bottom




$page->drawLine(50, 740, 290, 740);
$page->drawLine(50, 720, 290, 720);
//$page->drawLine(50, 740, 290, 740);
//$page->drawLine(50, 720, 290, 720);
$page->drawText($text, 90, 727);
$y1 = 700;
foreach ($rCargo as $value) {
    $page->drawText('Name  : '.$value['id_cargo'] , $x0, $y1);
    $y1 = $y1 - 20;
    $page->drawText('Branch  : '.$value['st_nome'], $x0, $y1);
    $y1 = $y1 - 20;
    $page->drawText('Code  : '.$value['ds_observacao'] , $x0, $y1);
    $y1 = $y1 - 20;
    $page->drawText('Account code  : '.$value['id_ativo'], $x0, $y1);
}


$pdf->pages[] = $page;
header('Content-type: application/pdf');
echo  $pdf->render();
/*$pdf->save('/var/www/' . $projname . '/reports/loanledger.pdf');
$path = '/var/www/' . $projname . '/reports/loanledger.pdf';
chmod($path, 0777);*/