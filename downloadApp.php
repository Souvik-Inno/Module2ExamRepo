<?php
  use Fpdf\Fpdf;

  require("vendor/autoload.php");
  $rowId = $_POST["rowId"];
  require("classes/App.php");
  $appData = new App();
  $downloads = $appData->downloadApp($rowId);
  $pdf = new Fpdf();
  $pdf->AddPage();
  $pdf->SetFont('Arial', 'B', 16);
  $pdf->Cell(0, 10, 'Form Details', 1, 1, 'C');
  $pdf->Cell(90, 10, 'App Name: ', 1, 0);
  $pdf->Cell(100, 10, $rowId, 1, 1);
  $pdf->Output('F', 'files/app.apk');
  echo $downloads;
?>