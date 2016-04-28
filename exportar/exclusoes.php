<?php
include("../conf/config.php"); // Inclui o arquivo com o sistema de segurança
protegePagina(); // Chama a função que protege a página
include '../conf/functions.php';
$usuario=$_SESSION['usuarioID'];

//PDF
define ('FPDF_FONTPATH', 'font/');
require('./fpdf/fpdf.php');

$conta = $_POST['conta'];


$mes_hoje = date('m');
$ano_hoje = date('Y');

//Sub classe para cabeçalho e rodapé
class PDF extends FPDF {

function Header(){
$nomeconta = $_POST['nome'];
$data=date("d/m/Y H:i");
$this->SetDrawColor(47,79,79);
$this->SetLineWidth(0.2);
$this->SetFillColor(0,110,110);
$this->SetFont('Arial','B',12);
$this->SetTextColor(255,255,255);
$this->Cell(15,1,utf8_decode("Exclusões: $nomeconta"),'LBT',0,'L',1);
$this->SetFont('Arial','',8);
$this->Cell(4,1,"$data ",'RBT',1,'R',1);
$this->Ln(0.8);
}

function Footer(){
$nome=$_SESSION['usuarioNome'];
$this->SetY(-2);
$this->SetFont('Arial','I',7);
$this->AliasNbPages();
$this->SetTextColor(0,0,0);
$this->Cell(0,0.6,utf8_decode("* Atenção: movimentos sem descrição da categoria podem ser exibidos, devido exclusão desta, da base de dados do sistema."),0,1,'L');
$this->SetFont('Arial','I',7);
$this->Cell(0,0.2,utf8_decode("Origem de dados: Livro Caixa.  Usuário: $nome.      |    Classe FPDF"),0,0,'L');
$this->Cell(0,0.2,utf8_decode("Página ").$this->PageNo().'/{nb}',0,0,'R');

}
}

//Início do corpo do pdf
$pdf=new PDF ('P','cm','A4');
$pdf->AddPage();
$pdf->SetFont('Arial','',14);
$pdf->SetTextColor(0,0,255);
$pdf->Cell(19,1,utf8_decode("Relatório de exclusões de movimentos."),0,1,'C',0);
$pdf->Ln(0.7);

$qrv=mysqli_query($_SG['conexao'], "SELECT * FROM exclusoes WHERE conta_mov='$conta' && usuario_mov='$usuario' ORDER By id DESC");
if (mysqli_num_rows($qrv)!==0){

$qrg=mysqli_query($_SG['conexao'], "SELECT SUM(valor_mov) as total FROM exclusoes WHERE tipo_mov=1 && conta_mov='$conta' && usuario_mov='$usuario'");
$rowg=mysqli_fetch_array($qrg);
$entradas=$rowg['total'];

$qrg=mysqli_query($_SG['conexao'], "SELECT SUM(valor_mov) as total FROM exclusoes WHERE tipo_mov=0 && conta_mov='$conta' && usuario_mov='$usuario'");
$rowg=mysqli_fetch_array($qrg);
$saidas=$rowg['total'];

$entradasexc=formata_dinheiro($entradas);
$saidasexc=formata_dinheiro($saidas);

$pdf->SetFillColor(169,169,169);
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Arial','',12);
$pdf->Cell(11,0.6,utf8_decode('Descrição do movimento'),0,0,'L',1);
$pdf->Cell(3.7,0.6,'Categoria',0,0,'L',1);
$pdf->Cell(2.4,0.6,'Valor',0,0,'L',1);
$pdf->Cell(1.9,0.6,'Data',0,1,'L',1);

//Exclusões
$cont=0;
while ($row=mysqli_fetch_array($qrv)){
$cont++;

$cat=$row['cat_mov'];
$qr2=mysqli_query($_SG['conexao'], "SELECT nome FROM categorias WHERE id='$cat'");
$row2=mysqli_fetch_array($qr2);
$categoria=$row2['nome'];
$valor=formata_dinheiro($row['valor_mov']);
$tipo=$row['tipo_mov'];
$data=date('d/m/Y', strtotime($row['data_exc']));

if ($cont%2==0){
$pdf->SetFillColor(211,211,211);}
if ($cont%2!=0){
$pdf->SetFillColor(232,232,232);}

$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Times','',9);
$pdf->Cell(11,0.5,utf8_decode($row['desc_mov']),0,0,'L',1);
$pdf->Cell(3.7,0.5,utf8_decode($row2['nome']),0,0,'L',1);
if ($tipo==0){
$pdf->SetFont('Times','',9);
$pdf->SetTextColor(255,0,0);
$pdf->Cell(2.4,0.5,"$valor",0,0,'L',1);}
if ($tipo==1){
$pdf->SetFont('Times','',9);
$pdf->SetTextColor(0,0,255);
$pdf->Cell(2.4,0.5,"$valor",0,0,'L',1);}
$pdf->SetFont('Times','',9);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(1.9,0.5,$data,0,1,'L',1);

}

$pdf->Ln(2);

//Balanço geral
if ($entradas>0){
$pdf->SetFont('Times','',7);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(16,0.5,utf8_decode('Total exclusões de entradas:'),0,0,'R',0);
$pdf->SetFont('Arial','',6);
$pdf->SetTextColor(0,0,255);
$pdf->Cell(3,0.5,"$entradasexc    ",0,1,'R',0);}
if ($entradas=0){
$pdf->Cell(16,0.5,'',0,1,'R',0);}

if ($saidas>0){
$pdf->SetFont('Times','',7);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(16,0.5,utf8_decode('Total exclusões de saídas:   '),0,0,'R',0);
$pdf->SetFont('Arial','',6);
$pdf->SetTextColor(255,0,0);
$pdf->Cell(3,0.5,"$saidasexc    ",0,1,'R',0);}
if ($saidas=0){
$pdf->Cell(16,0.5,'',0,1,'R',0);}

$pdf->Cell(19,0,'','B',1,'C',0);
$pdf->SetFont('Arial','',12);
$pdf->SetTextColor(0,0,255);
$pdf->Cell(19,0.7,utf8_decode('Fim do relatório.'),0,1,'C',0);

ob_start();
$pdf->Output("Exclusões_$mes_hoje-$ano_hoje.pdf",'D');

}else{
$pdf->SetFont('Times','',12);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(19,1,utf8_decode('Não há exclusões para está conta.'),0,0,'L',0);
$pdf->Ln(1);

$pdf->Cell(19,0,'','B',1,'C',0);
$pdf->SetFont('Arial','',12);
$pdf->SetTextColor(0,0,255);
$pdf->Cell(19,0.7,utf8_decode('Fim do relatório.'),0,1,'C',0);

ob_start();
$pdf->Output("Exclusões_$mes_hoje-$ano_hoje.pdf",'D');
}
?>
