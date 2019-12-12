<?php

$mCargo = new Model_Sistema_Cargo();
$rCargo = $mCargo->fetchAll()->toArray();

$pdf = new Zend_Pdf();
$page = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);

// Image
$image_name = APPLICATION_PATH ."/../public/img/logo/rcControls.png";
$image = Zend_Pdf_Image::imageWithPath($image_name);
$page->drawImage($image, 30, 780, 120, 820);

//$page->setLineWidth(1)->drawLine(570, 750, 25, 750);

$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 14);
$page->drawText('RELATÓRIO DE CARGOS/FUNÇÕES', 200, 780, 'UTF-8');

$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD),8);
$x0 = 50;
$y1 = 740;
$page->drawLine(40, 750, 550, 750);
$page->drawLine(40, 735, 550, 735);
$page->drawLine(40, 735, 40, 750);
$page->drawLine(100, 735, 100, 750);
$page->drawText('CODIGO' , $x0, $y1, 'UTF-8');
$x0 = $x0 + 80;
$page->drawLine(260, 735, 260, 750);
$page->drawText('NOME', $x0, $y1, 'UTF-8');
$x0 = $x0 + 155;
$page->drawLine(350, 735, 350, 750);
$page->drawText('ATIVO' , $x0, $y1, 'UTF-8');
$x0 = $x0 + 140;
$page->drawText('OBSERVAÇÃO', $x0, $y1, 'UTF-8');
$page->drawLine(550, 735, 550, 750);
$y1 = 725;
$x0 = 50;
$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA),8);
foreach ($rCargo as $value) {
    $page->drawLine(40, $y1 - 10, 550, $y1 - 10);
    $page->drawLine(40, $y1 - 10, 40, $y1+10);
    $page->drawText($value['id_cargo'] , $x0, $y1);
    $page->drawLine(100, $y1 - 10, 100, $y1+10);
    $x0 = $x0 + 60;
    $page->drawText($value['st_nome'], $x0, $y1);
    $page->drawLine(260, $y1 - 10, 260, $y1+10);
    $x0 = $x0 + 180;
    $page->drawText($value['id_ativo']==1? 'SIM':'NÃO', $x0, $y1);
    $page->drawLine(350, $y1 - 10, 350, $y1+10);
    $x0 = $x0 + 70;
    $page->drawText($value['ds_observacao'], $x0, $y1);
    $page->drawLine(550, $y1 - 10, 550, $y1+10);
    $y1 = $y1 - 20;
    $x0 = 50;
}

$page->setLineWidth(1)->drawLine(40, 20, 550, 20);

$pdf->pages[] = $page;
header("Content-type: application/pdf");
echo $pdf->render();