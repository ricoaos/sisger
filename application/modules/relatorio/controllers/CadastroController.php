<?php 

$pdf = new Zend_Pdf();
$pdfPage = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
$font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_COURIER);
$pdfPage->setFont($font, 12);
$pdfPage->drawText('Under-Linux',45, 800,'UTF-8');
$pdfPage->setFont($font, 10);
$texto = 'Foi anunciado o lançamento da série M5 para o Eclipse 3.6, também denominado Hélio, em homenagem ao Deus Grego do Sol. Com base em versões anteriores, o Eclipse 3.6 M5 inclui diversas correções de bugs e apresenta novas funcionalidades.';
$conc = wordwrap($texto, 60, '\n');
$d = explode('\n', $conc);
$stringpos = 780; // posicao x do meu texto
$stringdif = 12; // diferença entre cada quebra de linha.
$pdfPage->setFont($font, 9); 

foreach($d as $c)
{
    $pdfPage->drawText($c, 260, $stringpos, 'UTF-8');
    $stringpos = ($stringpos-$stringdif); //subtrai para que a linha fique embaixo
}

$pdf->pages[0]= $pdfPage;
//$pdf->save('exemplo.pdf');
header('Content-type: application/pdf');
echo $pdf->render();  