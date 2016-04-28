<?php
include("../conf/config.php"); // Inclui o arquivo com o sistema de segurança
protegePagina(); // Chama a função que protege a página
include '../conf/functions.php';
$usuario=$_SESSION['usuarioID'];


//====================================================================
// INÍCIO DA EXPORTAÇÃO DE ESTATISTICAS MENSAL
//====================================================================
if (isset($_POST['acao']) && $_POST['acao'] == 'estatistica_mensal') {
//Variável de mês a ser exportado
$mes = $_POST['mes'];
$ano = $_POST['ano'];

$mes_hoje = date('m');
$ano_hoje = date('Y');

$mesnome=mostraMes($mes);

//PDF
define ('FPDF_FONTPATH', 'font/');
require('./fpdf/fpdf.php');

//Sub classe para cabeçalho e rodapé
class PDF extends FPDF {

function Header(){
$nomeconta=$_POST['nome'];
$this->SetDrawColor(47,79,79);
$this->SetLineWidth(0.2);
$this->SetFillColor(0,110,110);
$this->SetFont('Arial','B',12);
$this->SetTextColor(255,255,255);
$this->Cell(15,1,utf8_decode(" $nomeconta"),'LBT',0,'L',1);
$this->SetFont('Arial','',8);
$data=date("d/m/Y H:i");
$this->Cell(4,1,"$data ",'RBT',1,'R',1);
$this->Ln(0.8);
}

function Footer(){
$nome=$_SESSION['usuarioNome'];
$this->SetY(-6);
$this->SetFont('Arial','I',8);
$this->AliasNbPages();
$this->SetTextColor(0,0,0);
$this->Cell(0,10,utf8_decode(" Origem de dados: Livro Caixa.  Usuário: $nome.     |    Classe FPDF"),0,0,'L');
$this->Cell(0,10,utf8_decode("Página ").$this->PageNo().'/{nb}',0,0,'R');
}
}

//Início do corpo do pdf
$pdf=new PDF ('P','cm','A4');
$pdf->AddPage();

$pdf->SetFont('Arial','',14);
$pdf->SetTextColor(0,0,255);
$pdf->Cell(19,1,utf8_decode('Relatório de Estatísticas Mensal'),0,0,'C',0);
$pdf->Ln(0.7);

$pdf->SetFont('Arial','',12);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(19,1,utf8_decode("$mesnome / $ano"),0,1,'L',0);

$qrv=mysqli_query($_SG['conexao'], "SELECT * FROM movimentos WHERE conta=1 && usuario='$usuario' && mes='$mes' && ano='$ano' ORDER By dia");
if (mysqli_num_rows($qrv)!==0){

$qrg=mysqli_query($_SG['conexao'], "SELECT SUM(valor) as total FROM movimentos WHERE tipo=1 && conta=1 && usuario='$usuario'");
$rowg=mysqli_fetch_array($qrg);
$entradasg=$rowg['total'];

$qrg=mysqli_query($_SG['conexao'], "SELECT SUM(valor) as total FROM movimentos WHERE tipo=0 && conta=1 && usuario='$usuario'");
$rowg=mysqli_fetch_array($qrg);
$saidasg=$rowg['total'];

$resultado_geral=$entradasg-$saidasg;
$balancogeral=formata_dinheiro($resultado_geral);

$qr=mysqli_query($_SG['conexao'], "SELECT SUM(valor) as total FROM movimentos WHERE tipo=1 && conta=1 && usuario='$usuario' && mes='$mes' && ano='$ano'");
$row=mysqli_fetch_array($qr);
$entradas=$row['total'];

$qrs=mysqli_query($_SG['conexao'], "SELECT SUM(valor) as total FROM movimentos WHERE tipo=0 && conta=1 && usuario='$usuario' && mes='$mes' && ano='$ano'");
$rows=mysqli_fetch_array($qrs);
$saidas=$rows['total'];

$resultado_mes=$entradas-$saidas;
$balanco=formata_dinheiro($resultado_mes);

//Estáticas de entradas
$qre=mysqli_query($_SG['conexao'], "SELECT dia, cat, SUM(valor) as total FROM movimentos WHERE tipo=1 && conta=1 && usuario='$usuario' && mes='$mes' && ano='$ano' GROUP BY cat ORDER BY dia");

if(mysqli_num_rows($qre)!==0){

$pdf->SetFillColor(169,169,169);
$pdf->SetFont('Arial','',12);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(19,0.6,utf8_decode('Entradas'),'B',1,'C',1);
$pdf->Cell(12,0.6,'Categoria:',0,0,'L',1);
$pdf->Cell(4,0.6,'Valor:',0,0,'L',1);
$pdf->Cell(3,0.6,'Percentual:',0,1,'R',1);

$cont=0;
while ($row=mysqli_fetch_array($qre)){
$cont++;

$cat=$row['cat'];
$qr2=mysqli_query($_SG['conexao'], "SELECT nome FROM categorias WHERE id='$cat'");
$row2=mysqli_fetch_array($qr2);
$categoria=$row2['nome'];
$valorcat=$row['total'];
$percento = @round (($valorcat/$entradas) * 100,1);
$valor=formata_dinheiro($row['total']);
$valortotal=formata_dinheiro($entradas);

if ($cont%2==0){
$pdf->SetFillColor(211,211,211);}
if ($cont%2!=0){
$pdf->SetFillColor(232,232,232);}

$pdf->SetFont('Times','',10);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(12,0.5,utf8_decode($row2['nome']),0,0,'L',1);
$pdf->SetFont('Times','',10);
$pdf->SetTextColor(0,0,255);
$pdf->Cell(4,0.5,"   $valor",0,0,'L',1);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(3,0.5,"$percento %   ",0,1,'R',1);

}

$pdf->SetFillColor(169,169,169);
$pdf->SetFont('Times','B',12);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(15,0.7,'Total entradas:',0,0,'L',1);
$pdf->SetFont('Arial','',12);
$pdf->SetTextColor(0,0,215);
$pdf->Cell(4,0.7,"$valortotal",0,1,'R',1);
$pdf->Ln(1);

}else{
$pdf->SetFillColor(169,169,169);
$pdf->SetFont('Arial','',12);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(19,0.6,utf8_decode('Entradas'),'B',1,'C',1);
$pdf->SetFillColor(211,211,211);
$pdf->SetFont('Times','',9);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(19,0.7,utf8_decode('Não há receitas para o período selecionado.'),0,1,'L',1);
$pdf->Ln(0.5);}

//Estáticas de saídas
$qrs=mysqli_query($_SG['conexao'], "SELECT dia, cat, SUM(valor) as total FROM movimentos WHERE tipo=0 && conta=1 && usuario='$usuario' && mes='$mes' && ano='$ano' GROUP BY cat ORDER BY dia");
if(mysqli_num_rows($qrs)!==0){

$pdf->SetFillColor(169,169,169);
$pdf->SetFont('Arial','',12);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(19,0.6,utf8_decode('Saídas'),'B',1,'C',1);
$pdf->Cell(12,0.6,'Categoria:',0,0,'L',1);
$pdf->Cell(4,0.6,'Valor:',0,0,'L',1);
$pdf->Cell(3,0.6,'Percentual:',0,1,'R',1);

$cont=0;
while ($row=mysqli_fetch_array($qrs)){
$cont++;

$cat=$row['cat'];
$qr2=mysqli_query($_SG['conexao'], "SELECT nome FROM categorias WHERE id='$cat'");
$row2=mysqli_fetch_array($qr2);
$categoria=$row2['nome'];
$valorcat=$row['total'];
$percento = @round (($valorcat/$saidas) * 100,1);
$valor=formata_dinheiro($row['total']);
$valortotals=formata_dinheiro($saidas);

if ($cont%2==0){
$pdf->SetFillColor(211,211,211);}
if ($cont%2!=0){
$pdf->SetFillColor(232,232,232);}

$pdf->SetFont('Times','',10);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(12,0.5,utf8_decode($row2['nome']),0,0,'L',1);
$pdf->SetFont('Times','',10);
$pdf->SetTextColor(255,0,0);
$pdf->Cell(4,0.5,"   $valor",0,0,'L',1);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(3,0.5,"$percento %   ",0,1,'R',1);

}

$pdf->SetFillColor(169,169,169);
$pdf->SetFont('Times','B',12);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(15,0.7,utf8_decode('Total Saídas:'),0,0,'L',1);
$pdf->SetFont('Arial','',12);
$pdf->SetTextColor(255,0,0);
$pdf->Cell(4,0.7,"$valortotals",0,1,'R',1);
$pdf->Ln(1);

}else{
$pdf->SetFillColor(169,169,169);
$pdf->SetFont('Arial','',12);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(19,0.6,utf8_decode('Entradas'),'B',1,'C',1);
$pdf->SetFillColor(211,211,211);
$pdf->SetFont('Times','',9);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(19,0.7,utf8_decode('Não há despesas para o período selecionado.'),0,1,'L',1);
$pdf->Ln(0.5);}

//Entras - Saídas
$pdf->SetFillColor(169,169,169);
$pdf->SetFont('Arial','',12);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(19,0.6,utf8_decode('Balanço'),'B',1,'C',1);
$pdf->SetFillColor(232,232,232);
$pdf->SetFont('Times','B',12);
$pdf->Cell(15,0.6,utf8_decode('Total:'),0,0,'L',1);
if  ($resultado_mes>=0){
	$pdf->SetFont('Arial','',12);
	$pdf->SetTextColor(0,0,215);
	$pdf->Cell(4,0.6,"$balanco",0,1,'R',1);}
if  ($resultado_mes<0){
	$pdf->SetFont('Arial','',12);
	$pdf->SetTextColor(255,0,0);
	$pdf->Cell(4,0.6,"$balanco",0,1,'R',1);}

//Balanço geral
$pdf->SetFont('Times','B',8);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(15,0.6,utf8_decode('Balanço geral:'),0,0,'R',0);
if  ($resultado_geral>=0){
	$pdf->SetFont('Arial','',8);
	$pdf->SetTextColor(0,0,215);
	$pdf->Cell(4,0.6,"$balancogeral    ",0,1,'R',0);}
if  ($resultado_geral<0){
	$pdf->SetFont('Arial','',8);
	$pdf->SetTextColor(255,0,0);
	$pdf->Cell(4,0.6,"$balancogeral    ",0,1,'R',0);}

$pdf->Ln(0.5);
$pdf->Cell(19,0,'','B',1,'C',0);
$pdf->SetFont('Arial','',12);
$pdf->SetTextColor(0,0,255);
$pdf->Cell(19,1,utf8_decode('Fim do relatório.'),0,1,'C',0);

ob_start();
$pdf->Output("Estatística_$mes-$ano.pdf",'D');

}else{
$pdf->SetFont('Times','',12);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(19,1,utf8_decode('Não há movimentação para o período selecionado.'),0,0,'L',0);
$pdf->Ln(0.7);

$pdf->Ln(1);
$pdf->Cell(19,0,'','B',1,'C',0);
$pdf->SetFont('Arial','',12);
$pdf->SetTextColor(0,0,255);
$pdf->Cell(19,1,utf8_decode('Fim do relatório.'),0,1,'C',0);

ob_start();
$pdf->Output("Estatística_$mes-$ano.pdf",'D');
}

}


//====================================================================
// INÍCIO DA EXPORTAÇÃO DE ESTATISTICAS ANUAL
//====================================================================
if (isset($_POST['acao']) && $_POST['acao'] == 'estatistica_anual') {
//Variável de ano a ser exportado
$ano = $_POST['ano'];

$mes_hoje = date('m');
$ano_hoje = date('Y');

//PDF
define ('FPDF_FONTPATH', 'font/');
require('./fpdf/fpdf.php');

//Sub classe para cabeçalho e rodapé
class PDF extends FPDF {

function Header(){
$nomeconta=$_POST['nome'];
$this->SetDrawColor(47,79,79);
$this->SetLineWidth(0.2);
$this->SetFillColor(0,110,110);
$this->SetFont('Arial','B',12);
$this->SetTextColor(255,255,255);
$this->Cell(15,1,utf8_decode(" $nomeconta"),'LBT',0,'L',1);
$this->SetFont('Arial','',8);
$data=date("d/m/Y H:i");
$this->Cell(4,1,"$data ",'RBT',1,'R',1);
$this->Ln(0.8);
}

function Footer(){
$nome=$_SESSION['usuarioNome'];
$this->SetY(-6);
$this->SetFont('Arial','I',8);
$this->AliasNbPages();
$this->SetTextColor(0,0,0);
$this->Cell(0,10,utf8_decode(" Origem de dados: Livro Caixa.  Usuário: $nome.     |    Classe FPDF"),0,0,'L');
$this->Cell(0,10,utf8_decode("Página ").$this->PageNo().'/{nb}',0,0,'R');
}
}

//Início do corpo do pdf
$pdf=new PDF ('P','cm','A4');
$pdf->AddPage();

$pdf->SetFont('Arial','',14);
$pdf->SetTextColor(0,0,255);
$pdf->Cell(19,1,utf8_decode('Relatório de Estatísticas Anual'),0,0,'C',0);
$pdf->Ln(0.7);

$pdf->SetFont('Arial','',12);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(19,1,utf8_decode("Estatísticas do ano $ano."),0,1,'L',0);

$qrv=mysqli_query($_SG['conexao'], "SELECT * FROM movimentos WHERE conta=1 && usuario='$usuario' && ano='$ano' ORDER By dia");
if (mysqli_num_rows($qrv)!==0){

$qr=mysqli_query($_SG['conexao'], "SELECT SUM(valor) as total FROM movimentos WHERE tipo=1 && conta=1 && usuario='$usuario' && ano='$ano'");
$row=mysqli_fetch_array($qr);
$entradas=$row['total'];
$entradastotal=formata_dinheiro($entradas);

$qrs=mysqli_query($_SG['conexao'], "SELECT SUM(valor) as total FROM movimentos WHERE tipo=0 && conta=1 && usuario='$usuario' && ano='$ano'");
$rows=mysqli_fetch_array($qrs);
$saidas=$rows['total'];

$qror=mysqli_query($_SG['conexao'], "SELECT SUM(valor) as total FROM orcamento WHERE conta=1 && usuario='$usuario' && ano='$ano'");
$rowor=mysqli_fetch_array($qror);
$orcamento=$rowor['total'];
$orcamentoformat=formata_dinheiro($orcamento);
$percentototal=@round (($saidas/$orcamento) * 100,1);

//Estáticas de entradas
$qre=mysqli_query($_SG['conexao'], "SELECT dia, cat, SUM(valor) as total FROM movimentos WHERE tipo=1 && conta=1 && usuario='$usuario' && ano='$ano' GROUP BY cat ORDER BY dia");

if(mysqli_num_rows($qre)!==0){

$pdf->SetFillColor(169,169,169);
$pdf->SetFont('Arial','',12);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(19,0.6,utf8_decode('Entradas'),'0',1,'C',1);
$pdf->SetFont('Arial','',11);
$pdf->Cell(19,0.6,utf8_decode("Percentual por categorias sobre receitas no total de $entradastotal"),'B',1,'C',1);
$pdf->SetFont('Arial','',12);
$pdf->Cell(12,0.6,'Categoria:',0,0,'L',1);
$pdf->Cell(4,0.6,'Valor:',0,0,'L',1);
$pdf->Cell(3,0.6,'Percentual:',0,1,'R',1);

$cont=0;
while ($row=mysqli_fetch_array($qre)){
$cont++;

$cat=$row['cat'];
$qr2=mysqli_query($_SG['conexao'], "SELECT nome FROM categorias WHERE id='$cat'");
$row2=mysqli_fetch_array($qr2);
$categoria=$row2['nome'];
$valorcat=$row['total'];
$percento = @round (($valorcat/$entradas) * 100,1);
$valor=formata_dinheiro($row['total']);
$valortotal=formata_dinheiro($entradas);

if ($cont%2==0){
$pdf->SetFillColor(211,211,211);}
if ($cont%2!=0){
$pdf->SetFillColor(232,232,232);}

$pdf->SetFont('Times','',10);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(12,0.5,utf8_decode($row2['nome']),0,0,'L',1);
$pdf->SetFont('Times','',10);
$pdf->SetTextColor(0,0,255);
$pdf->Cell(4,0.5,"   $valor",0,0,'L',1);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(3,0.5,"$percento %   ",0,1,'R',1);

}

$pdf->Ln(1);

}else{
$pdf->SetFillColor(169,169,169);
$pdf->SetFont('Arial','',12);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(19,0.6,utf8_decode('Entradas'),'B',1,'C',1);
$pdf->SetFillColor(211,211,211);
$pdf->SetFont('Times','',9);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(19,0.7,utf8_decode('Não há receitas para o período selecionado.'),0,1,'L',1);
$pdf->Ln(0.5);}

//Estáticas de saídas
$qrs=mysqli_query($_SG['conexao'], "SELECT dia, mes, cat, SUM(valor) as total FROM movimentos WHERE tipo=0 && conta=1 && usuario='$usuario' && ano='$ano' GROUP BY cat ORDER BY dia");
if(mysqli_num_rows($qrs)!==0){

$pdf->SetFillColor(169,169,169);
$pdf->SetFont('Arial','',12);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(19,0.6,utf8_decode('Saídas'),'0',1,'C',1);
$pdf->SetFont('Arial','',11);
$pdf->Cell(19,0.6,utf8_decode("Percentual anual sobre total de saídas, e percentual sobre orçamento total de $orcamentoformat"),'B',1,'L',1);
$pdf->SetFont('Arial','',10);
$pdf->Cell(9,0.6,'Categoria:',0,0,'L',1);
$pdf->Cell(4,0.6,'Valor:',0,0,'L',1);
$pdf->Cell(3,0.6,utf8_decode("Percentual sobre"),0,0,'C',1);
$pdf->Cell(3,0.6,utf8_decode("Percentual sobre"),0,1,'C',1);
$pdf->Cell(9,0.6,'',0,0,'L',1);
$pdf->Cell(4,0.6,'',0,0,'L',1);
$pdf->Cell(3,0.6,utf8_decode("saídas:"),0,0,'C',1);
$pdf->Cell(3,0.6,utf8_decode("orçamento:"),0,1,'C',1);

$cont=0;
while ($row=mysqli_fetch_array($qrs)){
$cont++;

$cat=$row['cat'];
$qr2=mysqli_query($_SG['conexao'], "SELECT nome FROM categorias WHERE id='$cat'");
$row2=mysqli_fetch_array($qr2);
$categoria=$row2['nome'];
$valorcat=$row['total'];
$percento = @round (($valorcat/$orcamento) * 100,1);
$percentos = @round (($valorcat/$saidas) * 100,1);
$valor=formata_dinheiro($row['total']);
$valortotals=formata_dinheiro($saidas);

if ($cont%2==0){
$pdf->SetFillColor(211,211,211);}
if ($cont%2!=0){
$pdf->SetFillColor(232,232,232);}

$pdf->SetFont('Times','',10);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(9,0.5,utf8_decode($row2['nome']),0,0,'L',1);
$pdf->SetFont('Times','',10);
$pdf->SetTextColor(255,0,0);
$pdf->Cell(4,0.5,"   $valor",0,0,'L',1);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(3,0.5,"$percentos %   ",0,0,'C',1);
$pdf->Cell(3,0.5,"$percento %   ",0,1,'C',1);

}

$pdf->SetFillColor(169,169,169);
$pdf->SetFont('Times','B',12);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(9,0.7,utf8_decode('Total Saídas:'),'B',0,'L',1);
$pdf->SetFont('Arial','',12);
$pdf->SetTextColor(255,0,0);
$pdf->Cell(7,0.7,"$valortotals",'B',0,'L',1);
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Times','',10);
$pdf->Cell(3,0.7,utf8_decode("$percentototal % do orçamento"),'B',1,'R',1);
$pdf->SetFillColor(169,169,169);
$pdf->SetFont('Times','B',12);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(16,0.7,utf8_decode('Valor restante do orçamento:'),0,0,'L',1);
$restante=$orcamento-$saidas;
$restanteformatado=formata_dinheiro($restante);
if  ($restante>=0){
	$pdf->SetFont('Arial','',12);
	$pdf->SetTextColor(0,0,215);
	$pdf->Cell(3,0.7,"$restanteformatado",0,1,'R',1);}
if  ($restante<0){
	$pdf->SetFont('Arial','',12);
	$pdf->SetTextColor(255,0,0);
	$pdf->Cell(3,0.7,"$restanteformatado",0,1,'R',1);}

$pdf->Ln(1);

}else{
$pdf->SetFillColor(169,169,169);
$pdf->SetFont('Arial','',12);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(19,0.6,utf8_decode('Entradas'),'B',1,'C',1);
$pdf->SetFillColor(211,211,211);
$pdf->SetFont('Times','',9);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(19,0.7,utf8_decode('Não há despesas para o período selecionado.'),0,1,'L',1);
$pdf->Ln(0.5);}

$pdf->Ln(0.5);
$pdf->Cell(19,0,'','B',1,'C',0);
$pdf->SetFont('Arial','',12);
$pdf->SetTextColor(0,0,255);
$pdf->Cell(19,1,utf8_decode('Fim do relatório.'),0,1,'C',0);

ob_start();
$pdf->Output("Estatística_$ano.pdf",'D');

}else{
$pdf->SetFont('Times','',12);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(19,1,utf8_decode('Não há movimentação para o período selecionado.'),0,0,'L',0);
$pdf->Ln(0.7);

$pdf->Ln(1);
$pdf->Cell(19,0,'','B',1,'C',0);
$pdf->SetFont('Arial','',12);
$pdf->SetTextColor(0,0,255);
$pdf->Cell(19,1,utf8_decode('Fim do relatório.'),0,1,'C',0);

ob_start();
$pdf->Output("Estatística_$ano.pdf",'D');
}
	
}

//====================================================================
// INÍCIO DA EXPORTAÇÃO DE ESTATISTICAS MENSAL DO CARTÃO DE CRÉDITO
//====================================================================
if (isset($_POST['acao']) && $_POST['acao'] == 'estatistica_mensal_cart') {
//Variável de mês a ser exportado
$conta = $_POST['conta'];
$nome = $_POST['nome'];
$mes = $_POST['mes'];
$ano = $_POST['ano'];

$mes_hoje = date('m');
$ano_hoje = date('Y');

$mesnome=mostraMes($mes);

//PDF
define ('FPDF_FONTPATH', 'font/');
require('./fpdf/fpdf.php');

//Sub classe para cabeçalho e rodapé
class PDF extends FPDF {

function Header(){
$nomeconta=$_POST['nome'];
$this->SetDrawColor(47,79,79);
$this->SetLineWidth(0.2);
$this->SetFillColor(0,110,110);
$this->SetFont('Arial','B',12);
$this->SetTextColor(255,255,255);
$this->Cell(15,1,utf8_decode(" $nomeconta"),'LBT',0,'L',1);
$this->SetFont('Arial','',8);
$data=date("d/m/Y H:i");
$this->Cell(4,1,"$data ",'RBT',1,'R',1);
$this->Ln(0.8);
}

function Footer(){
$nome=$_SESSION['usuarioNome'];
$this->SetY(-6);
$this->SetFont('Arial','I',8);
$this->AliasNbPages();
$this->SetTextColor(0,0,0);
$this->Cell(0,10,utf8_decode(" Origem de dados: Livro Caixa.  Usuário: $nome.     |    Classe FPDF"),0,0,'L');
$this->Cell(0,10,utf8_decode("Página ").$this->PageNo().'/{nb}',0,0,'R');
}
}

//Início do corpo do pdf
$pdf=new PDF ('P','cm','A4');
$pdf->AddPage();

$pdf->SetFont('Arial','',14);
$pdf->SetTextColor(0,0,255);
$pdf->Cell(19,1,utf8_decode("Relatório de Estatísticas Mensal."),0,0,'C',0);
$pdf->Ln(0.7);

$pdf->SetFont('Arial','',12);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(19,1,utf8_decode("$mesnome / $ano"),0,1,'L',0);

$qrv=mysqli_query($_SG['conexao'], "SELECT * FROM movimentos WHERE conta='$conta' && usuario='$usuario' && mes='$mes' && ano='$ano' ORDER By dia");
if (mysqli_num_rows($qrv)!==0){

$qrs=mysqli_query($_SG['conexao'], "SELECT SUM(valor) as total FROM movimentos WHERE tipo=0 && conta='$conta' && usuario='$usuario' && mes='$mes' && ano='$ano'");
$rows=mysqli_fetch_array($qrs);
$saidas=$rows['total'];

//Estáticas de saídas
$qrs=mysqli_query($_SG['conexao'], "SELECT dia, cat, SUM(valor) as total FROM movimentos WHERE tipo=0 && conta='$conta' && usuario='$usuario' && mes='$mes' && ano='$ano' GROUP BY cat ORDER BY dia");
if(mysqli_num_rows($qrs)!==0){

$pdf->SetFillColor(169,169,169);
$pdf->SetFont('Arial','',12);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(19,0.6,utf8_decode('Saídas'),'B',1,'C',1);
$pdf->Cell(12,0.6,'Categoria:',0,0,'L',1);
$pdf->Cell(4,0.6,'Valor:',0,0,'L',1);
$pdf->Cell(3,0.6,'Percentual:',0,1,'R',1);

$cont=0;
while ($row=mysqli_fetch_array($qrs)){
$cont++;

$cat=$row['cat'];
$qr2=mysqli_query($_SG['conexao'], "SELECT nome FROM categorias WHERE id='$cat'");
$row2=mysqli_fetch_array($qr2);
$categoria=$row2['nome'];
$valorcat=$row['total'];
$percento = @round (($valorcat/$saidas) * 100,1);
$valor=formata_dinheiro($row['total']);
$valortotals=formata_dinheiro($saidas);

if ($cont%2==0){
$pdf->SetFillColor(211,211,211);}
if ($cont%2!=0){
$pdf->SetFillColor(232,232,232);}

$pdf->SetFont('Times','',10);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(12,0.5,utf8_decode($row2['nome']),0,0,'L',1);
$pdf->SetFont('Times','',10);
$pdf->SetTextColor(255,0,0);
$pdf->Cell(4,0.5,"   $valor",0,0,'L',1);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(3,0.5,"$percento %   ",0,1,'R',1);

}

$pdf->SetFillColor(169,169,169);
$pdf->SetFont('Times','B',12);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(15,0.7,utf8_decode('Total Saídas:'),0,0,'L',1);
$pdf->SetFont('Arial','',12);
$pdf->SetTextColor(255,0,0);
$pdf->Cell(4,0.7,"$valortotals",0,1,'R',1);
$pdf->Ln(1);

}else{
$pdf->SetFillColor(169,169,169);
$pdf->SetFont('Arial','',12);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(19,0.6,utf8_decode('Entradas'),'B',1,'C',1);
$pdf->SetFillColor(211,211,211);
$pdf->SetFont('Times','',9);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(19,0.7,utf8_decode('Não há despesas para o período selecionado.'),0,1,'L',1);
$pdf->Ln(0.5);}

$pdf->Ln(0.5);
$pdf->Cell(19,0,'','B',1,'C',0);
$pdf->SetFont('Arial','',12);
$pdf->SetTextColor(0,0,255);
$pdf->Cell(19,1,utf8_decode('Fim do relatório.'),0,1,'C',0);

ob_start();
$pdf->Output("Estatística_$mes-$ano.pdf",'D');

}else{
$pdf->SetFont('Times','',12);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(19,1,utf8_decode('Não há movimentação para o período selecionado.'),0,0,'L',0);
$pdf->Ln(0.7);

$pdf->Ln(1);
$pdf->Cell(19,0,'','B',1,'C',0);
$pdf->SetFont('Arial','',12);
$pdf->SetTextColor(0,0,255);
$pdf->Cell(19,1,utf8_decode('Fim do relatório.'),0,1,'C',0);

ob_start();
$pdf->Output("Estatística_$mes-$ano.pdf",'D');
}

}


//====================================================================
// INÍCIO DA EXPORTAÇÃO DE ESTATISTICAS ANUAL DO CARTÃO DE CRÉDITO
//====================================================================
if (isset($_POST['acao']) && $_POST['acao'] == 'estatistica_anual_cart') {
//Variável de mês a ser exportado
$conta = $_POST['conta'];
$mes = $_POST['mes'];
$ano = $_POST['ano'];

$mes_hoje = date('m');
$ano_hoje = date('Y');

$mesnome=mostraMes($mes);

//PDF
define ('FPDF_FONTPATH', 'font/');
require('./fpdf/fpdf.php');

//Sub classe para cabeçalho e rodapé
class PDF extends FPDF {

function Header(){
$nomeconta = $_POST['nome'];
$this->SetDrawColor(47,79,79);
$this->SetLineWidth(0.2);
$this->SetFillColor(0,110,110);
$this->SetFont('Arial','B',12);
$this->SetTextColor(255,255,255);
$this->Cell(15,1,utf8_decode(" $nomeconta"),'LBT',0,'L',1);
$this->SetFont('Arial','',8);
$data=date("d/m/Y H:i");
$this->Cell(4,1,"$data ",'RBT',1,'R',1);
$this->Ln(0.8);
}

function Footer(){
$nome=$_SESSION['usuarioNome'];
$this->SetY(-6);
$this->SetFont('Arial','I',8);
$this->AliasNbPages();
$this->SetTextColor(0,0,0);
$this->Cell(0,10,utf8_decode(" Origem de dados: Livro Caixa.  Usuário: $nome.     |    Classe FPDF"),0,0,'L');
$this->Cell(0,10,utf8_decode("Página ").$this->PageNo().'/{nb}',0,0,'R');
}
}

//Início do corpo do pdf
$pdf=new PDF ('P','cm','A4');
$pdf->AddPage();

$pdf->SetFont('Arial','',14);
$pdf->SetTextColor(0,0,255);
$pdf->Cell(19,1,utf8_decode("Relatório Estatísticas Anual."),0,0,'C',0);
$pdf->Ln(0.7);

$pdf->SetFont('Arial','',12);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(19,1,utf8_decode("Estatísticas do ano $ano."),0,1,'L',0);

$qrv=mysqli_query($_SG['conexao'], "SELECT * FROM movimentos WHERE tipo=0 && conta='$conta' && usuario='$usuario' && ano='$ano' ORDER By dia");
if (mysqli_num_rows($qrv)!==0){

$qrs=mysqli_query($_SG['conexao'], "SELECT SUM(valor) as total FROM movimentos WHERE tipo=0 && conta='$conta' && usuario='$usuario' && ano='$ano'");
$rows=mysqli_fetch_array($qrs);
$saidas=$rows['total'];

//Estáticas de saídas
$qrs=mysqli_query($_SG['conexao'], "SELECT dia, cat, SUM(valor) as total FROM movimentos WHERE tipo=0 && conta='$conta' && usuario='$usuario' && ano='$ano' GROUP BY cat ORDER BY dia");
if(mysqli_num_rows($qrs)!==0){

$pdf->SetFillColor(169,169,169);
$pdf->SetFont('Arial','',12);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(19,0.6,utf8_decode('Saídas'),'B',1,'C',1);
$pdf->Cell(12,0.6,'Categoria:',0,0,'L',1);
$pdf->Cell(4,0.6,'Valor:',0,0,'L',1);
$pdf->Cell(3,0.6,'Percentual:',0,1,'R',1);

$cont=0;
while ($row=mysqli_fetch_array($qrs)){
$cont++;

$cat=$row['cat'];
$qr2=mysqli_query($_SG['conexao'], "SELECT nome FROM categorias WHERE id='$cat' ORDER BY nome");
$row2=mysqli_fetch_array($qr2);
$categoria=$row2['nome'];
$valorcat=$row['total'];
$percento = @round (($valorcat/$saidas) * 100,1);
$valor=formata_dinheiro($row['total']);
$valortotals=formata_dinheiro($saidas);

if ($cont%2==0){
$pdf->SetFillColor(211,211,211);}
if ($cont%2!=0){
$pdf->SetFillColor(232,232,232);}

$pdf->SetFont('Times','',10);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(12,0.5,utf8_decode($row2['nome']),0,0,'L',1);
$pdf->SetFont('Times','',10);
$pdf->SetTextColor(255,0,0);
$pdf->Cell(4,0.5,"   $valor",0,0,'L',1);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(3,0.5,"$percento %   ",0,1,'R',1);

}

$pdf->SetFillColor(169,169,169);
$pdf->SetFont('Times','B',12);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(15,0.7,utf8_decode('Total Saídas:'),0,0,'L',1);
$pdf->SetFont('Arial','',12);
$pdf->SetTextColor(255,0,0);
$pdf->Cell(4,0.7,"$valortotals",0,1,'R',1);
$pdf->Ln(1);

}else{
$pdf->SetFillColor(169,169,169);
$pdf->SetFont('Arial','',12);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(19,0.6,utf8_decode('Entradas'),'B',1,'C',1);
$pdf->SetFillColor(211,211,211);
$pdf->SetFont('Times','',9);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(19,0.7,utf8_decode('Não há despesas para o período selecionado.'),0,1,'L',1);
$pdf->Ln(0.5);}

$pdf->Ln(0.5);
$pdf->Cell(19,0,'','B',1,'C',0);
$pdf->SetFont('Arial','',12);
$pdf->SetTextColor(0,0,255);
$pdf->Cell(19,1,utf8_decode('Fim do relatório.'),0,1,'C',0);

ob_start();
$pdf->Output("Estatística_$ano.pdf",'D');

}else{
$pdf->SetFont('Times','',12);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(19,1,utf8_decode('Não há movimentação para o período selecionado.'),0,0,'L',0);
$pdf->Ln(0.7);

$pdf->Ln(1);
$pdf->Cell(19,0,'','B',1,'C',0);
$pdf->SetFont('Arial','',12);
$pdf->SetTextColor(0,0,255);
$pdf->Cell(19,1,utf8_decode('Fim do relatório.'),0,1,'C',0);

ob_start();
$pdf->Output("Estatística_$ano.pdf",'D');
}

}

?>   
