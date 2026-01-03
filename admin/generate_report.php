<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role']!=='admin'){
    exit("Unauthorized");
}

require "../config/db.php";
require "lib/fpdf.php";   // ⚠ 确保 fpdf.php 位置正确！ admin/lib/fpdf.php

/* 读取筛选条件 */
$where="WHERE 1 ";
if(!empty($_GET['status'])) $where.="AND o.status='".$_GET['status']."' ";
if(!empty($_GET['from']))   $where.="AND DATE(o.created_at)>='".$_GET['from']."' ";
if(!empty($_GET['to']))     $where.="AND DATE(o.created_at)<='".$_GET['to']."' ";

/* 读取订单数据 */
$q=mysqli_query($conn,"
SELECT o.*,u.username
FROM orders o
JOIN users u ON o.user_id=u.id
$where ORDER BY o.created_at DESC
");

$pdf=new FPDF("P","mm","A4");
$pdf->AddPage();
$pdf->SetFont("Arial","B",18);
$pdf->Cell(0,12,"Order Report",0,1,"C");

$pdf->SetFont("Arial","",11);
$pdf->Ln(5);

/* Table Header */
$pdf->SetFont("Arial","B",12);
$pdf->Cell(20,10,"ID",1);
$pdf->Cell(45,10,"User",1);
$pdf->Cell(35,10,"Total (RM)",1);
$pdf->Cell(45,10,"Status",1);
$pdf->Cell(45,10,"Date",1);
$pdf->Ln();

/* Table Data */
$pdf->SetFont("Arial","",11);

while($o=mysqli_fetch_assoc($q)){
    $pdf->Cell(20,10,$o['id'],1);
    $pdf->Cell(45,10,$o['username'],1);
    $pdf->Cell(35,10,number_format($o['total_amount'],2),1);
    $pdf->Cell(45,10,ucwords($o['status']),1);
    $pdf->Cell(45,10,$o['created_at'],1);
    $pdf->Ln();
}

$pdf->Ln(5);
$pdf->SetFont("Arial","I",10);
$pdf->Cell(0,8,"Generated on ".date("Y-m-d H:i:s")." by Admin",0,1,"R");

$pdf->Output("D","Order_Report_".date("Ymd_His").".pdf");
?>
