<?php
include("../conf/config.php"); // Inclui o arquivo com o sistema de segurança
protegePagina(); // Chama a função que protege a página
include '../conf/functions.php';
$usuario=$_SESSION['usuarioID'];

//Boas vindas em função da hora
$hora = date('G');
if (($hora >= 0) AND ($hora < 5)) {
$mensagem = "Já é madrugada";
} else if (($hora >= 5) AND ($hora < 6)) {
$mensagem = "Já esta amanhecendo";
} else if (($hora >= 6) AND ($hora < 12)) {
$mensagem = "Bom dia";
} else if (($hora >= 12) AND ($hora < 18)) {
$mensagem = "Boa tarde";
} else {
$mensagem = "Boa noite";
}

//Mês e ano hoje
if (isset($_GET['mes']))
    $mes_hoje = $_GET['mes'];
else
    $mes_hoje = date('m');

if (isset($_GET['ano']))
    $ano_hoje = $_GET['ano'];
else
    $ano_hoje = date('Y');

?>

<html lang="pt-BR">
<head>
<meta charset="utf-8">
<link href="../conf/img/favicon.png" rel="icon" type="image/png"/>
<meta content="width=device-width, initial-scale=1" name="viewport">
<title>DASHBOARD</title>
<link href="../conf/css/styles.css" rel="stylesheet" type="text/css" />
<link id="scrollUpTheme" rel="stylesheet" href="../conf/css/image.css">
<script src="../conf/js/jquery.js"></script>
<script src="../conf/js/jquery.scroll.topo.js"></script>
<script src="../conf/js/jquery.easing.js"></script>
<script src="../conf/js/jquery.easing.compatibilidade.js"></script>
<script src="../conf/js/jquery.chart.js"></script>
<script>
(function ($) {
$.getQuery = function (query) {
	query = query.replace(/[\[]/, '\\\[').replace(/[\]]/, '\\\]');
	var expr = '[\\?&]' + query + '=([^&#]*)';
	var regex = new RegExp(expr);
	var results = regex.exec(window.location.href);
		if (results !== null) {
		return results[1];
		} else {
		return false;
		}
	};
})(jQuery);

$(function () {

	$('.image-switch').click(function () {
	window.location = '?theme=image';
	});

	if ($.getQuery('theme') === 'image') {
	$(function () {
		$.scrollUp({
			animation: 'fade',
			activeOverlay: 'false',
			scrollImg: {
			active: true,
			type: 'background',
			src: '../conf/img/topo.png'
			}
		});
	});
$('#scrollUpTheme').attr('href', '../conf/css/image.css?1.1');
$('.image-switch').addClass('active');
} else {
	$(function () {
	$.scrollUp({
		animation: 'slide',
		activeOverlay: 'false'
		});
	});
$('#scrollUpTheme').attr('href', '../conf/css/image.css?1.1');
$('.image-switch').addClass('active');
}
});
</script>

</head>

<body style="padding:10px">
<br />
<table cellpadding="1" cellspacing="10"  width="1000" align="center" style="background-color:#033">
<tr>
<td colspan="11" style="background-color:#005B5B;">
<h2 style="margin:5px"><a href=../index.php style="color:#008B8B">Conta Corrente</a> | <a href=../visa.php style="color:#008B8B">Cartão Visa</a> | <a href=../master.php style="color:#008B8B">Cartão Master</a> | <a href=graficos.php style="color:#FFF">Dashboard</a></h2>
</td>
<td colspan="2" align="right" style="background-color:#005B5B;">
<a style="color:#FFF" href="?mes=<?php echo date('m')?>&ano=<?php echo date('Y')?>">Hoje:<strong> <?php echo date('d')?> de <?php echo mostraMes(date('m'))?> de <?php echo date('Y')?></strong></a>&nbsp; 
</td>
</tr>
<tr>
<td width="70">
<select onchange="location.replace('?mes=<?php echo $mes_hoje?>&ano='+this.value)">
<?php
for ($i=2015;$i<=2020;$i++){
?>
<option value="<?php echo $i?>" <?php if ($i==$ano_hoje) echo "selected=selected"?> ><?php echo $i?></option>
<?php }?>
</select>
</td>
<?php
for ($i=1;$i<=12;$i++){
	?>
    <td align="center" style="<?php if ($i!=12) echo "border-right:1px solid #FFF;"?> padding-right:5px">
    <a href="?mes=<?php echo $i?>&ano=<?php echo $ano_hoje?>" style="
    <?php if($mes_hoje==$i){?>    
    color:#033; font-size:16px; font-weight:bold; background-color:#FFF; padding:5px
    <?php }else{?>
    color:#FFF; font-size:16px;
    <?php }?>
    ">
    <?php echo mostraMes($i);?>
    </a>
    </td>
<?php
}
?>
</tr>
</table>
<table cellpadding="5" cellspacing="0" width="1000" align="center">
<tr>
<td><font size=3 color="#000"><center><?php echo $mensagem?><?php echo " "?><?php echo $_SESSION['usuarioNome'];?><?php echo " "?><?php echo $_SESSION['usuarioSobrenome'];?>, este é o seu Dashboard.</center></font></b>
</td>
<td align="right" style="font-size:13px; color:rgba(4, 45, 191, 1)">
<a href=../logout.php style="font-size:12px; color:rgba(4, 45, 191, 1)"><?php echo " [ Fazer logout ]"?></a>
</td>
</tr>
</table>
<br />
<center><font size=5><b>Dashboard para análise de movimentos lançados no Sistema Livro Caixa Simples.</font></center><br />

<?php
//SAIDAS E ENTRADAS MES
$qr=mysqli_query($_SG['conexao'], "SELECT SUM(valor) as total FROM movimentos WHERE tipo=1 AND conta=1 AND usuario='$usuario' AND mes='$mes_hoje' AND ano='$ano_hoje'");
$row=mysqli_fetch_array($qr);
$entradas=$row['total'];
$qr=mysqli_query($_SG['conexao'], "SELECT SUM(valor) as total FROM movimentos WHERE tipo=0 AND conta=1 AND usuario='$usuario' AND mes='$mes_hoje' AND ano='$ano_hoje'");
$row=mysqli_fetch_array($qr);
$saidas=$row['total'];
//SAIDAS E ENTRADAS ANO
$qr2=mysqli_query($_SG['conexao'], "SELECT SUM(valor) as total FROM movimentos WHERE tipo=1 AND conta=1 AND usuario='$usuario' AND ano='$ano_hoje'");
$row2=mysqli_fetch_array($qr2);
$entradasano=$row2['total'];
$qr2=mysqli_query($_SG['conexao'], "SELECT SUM(valor) as total FROM movimentos WHERE tipo=0 AND conta=1 AND usuario='$usuario' AND ano='$ano_hoje'");
$row2=mysqli_fetch_array($qr2);
$saidasano=$row2['total'];
//SAIDAS E ENTRADAS MES TODAS AS CONTAS
$qr3=mysqli_query($_SG['conexao'], "SELECT SUM(valor) as total FROM movimentos WHERE tipo=0 AND conta=1 AND usuario='$usuario' AND mes='$mes_hoje' AND ano='$ano_hoje'");
$row3=mysqli_fetch_array($qr3);
$gastoscctotal=$row3['total'];
$qr3=mysqli_query($_SG['conexao'], "SELECT SUM(valor) as total FROM movimentos WHERE tipo=0 AND conta=2 AND usuario='$usuario' AND mes='$mes_hoje' AND ano='$ano_hoje'");
$row3=mysqli_fetch_array($qr3);
$gastosccv=$row3['total'];
$qr3=mysqli_query($_SG['conexao'], "SELECT SUM(valor) as total FROM movimentos WHERE tipo=0 AND conta=3 AND usuario='$usuario' AND mes='$mes_hoje' AND ano='$ano_hoje'");
$row3=mysqli_fetch_array($qr3);
$gastosccm=$row3['total'];
//calcular o valor total gasto na conta corrente
$mescalculo=$mes_hoje-1;
$qr3=mysqli_query($_SG['conexao'], "SELECT SUM(valor) as total FROM movimentos WHERE tipo=0 AND conta=2 AND usuario='$usuario' AND mes='$mescalculo' AND ano='$ano_hoje'");
$row3=mysqli_fetch_array($qr3);
$gastosccvmant=$row3['total'];
$qr3=mysqli_query($_SG['conexao'], "SELECT SUM(valor) as total FROM movimentos WHERE tipo=0 AND conta=3 AND usuario='$usuario' AND mes='$mescalculo' AND ano='$ano_hoje'");
$row3=mysqli_fetch_array($qr3);
$gastosccmmant=$row3['total'];
$gastoscccalculo=$gastoscctotal-$gastosccvmant-$gastosccmmant;
if ($gastoscccalculo<0){
	$gastoscc=0;
}else{
	$gastoscc=$gastoscccalculo;
}
//SAIDAS E ENTRADAS TODAS AS CONTAS ANO
$qr3=mysqli_query($_SG['conexao'], "SELECT SUM(valor) as total FROM movimentos WHERE tipo=0 AND conta=1 AND usuario='$usuario' AND mes<='$mes_hoje' AND ano='$ano_hoje'");
$row3=mysqli_fetch_array($qr3);
$gastosccano=$row3['total'];
$qr3=mysqli_query($_SG['conexao'], "SELECT SUM(valor) as total FROM movimentos WHERE tipo=0 AND conta=2 AND usuario='$usuario' AND mes<='$mes_hoje' AND ano='$ano_hoje'");
$row3=mysqli_fetch_array($qr3);
$gastosccvano=$row3['total'];
$qr3=mysqli_query($_SG['conexao'], "SELECT SUM(valor) as total FROM movimentos WHERE tipo=0 AND conta=3 AND usuario='$usuario' AND mes<='$mes_hoje' AND ano='$ano_hoje'");
$row3=mysqli_fetch_array($qr3);
$gastosccmano=$row3['total'];

//PROGRESSÃO ENTRADAS
$meslanct1=array();
$totalemat=array();
$i1=0;
$mescalc=1;
while ($mescalc<=$mes_hoje){
$qrcem=mysqli_query($_SG['conexao'], "SELECT mes, SUM(valor) as total FROM movimentos WHERE tipo=1 AND conta=1 AND usuario='$usuario' AND mes='$mescalc' AND ano='$ano_hoje' GROUP BY mes");
$rowcem=mysqli_fetch_array($qrcem);
$entradas=$rowcem['total'];
$mesrt=$rowcem['mes'];
$mescalc++;
$mesnome1=mostraMes($mesrt);
$meslanct1[$i1]=$mesnome1;
$totalemat[$i1]=$entradas;
$i1=$i1+1;
}

//PROGRESSÃO SAIDAS
$totalsmat=array();
$i1=0;
$mescalc1=1;
while ($mescalc1<=$mes_hoje){
$qrcsm=mysqli_query($_SG['conexao'], "SELECT mes, SUM(valor) as total FROM movimentos WHERE tipo=0 AND conta=1 AND usuario='$usuario' AND mes='$mescalc1' AND ano='$ano_hoje' GROUP BY mes");
$rowcsm=mysqli_fetch_array($qrcsm);
$saidasw=$rowcsm['total'];
$mescalc1++;
$totalsmat[$i1]=$saidasw;
$i1=$i1+1;
}

//PROGRESSÃO USO DO CARTÃO DE CRÉDITO VISA
$meslancatao=array();
$totalcartaov=array();
$i4=0;
$mescalculocartao=1;
while ($mescalculocartao<=$mes_hoje){
$qrcart=mysqli_query($_SG['conexao'], "SELECT mes, SUM(valor) as total FROM movimentos WHERE tipo=0 AND conta=2 AND usuario='$usuario' AND mes='$mescalculocartao' AND ano='$ano_hoje' GROUP BY mes");
$rowcart=mysqli_fetch_array($qrcart);
$comprasv=$rowcart['total'];
$mesrc=$rowcart['mes'];
$mescalculocartao++;
$mesnomec=mostraMes($mesrc);
$meslancatao[$i4]=$mesnomec;
$totalcartaov[$i4]=$comprasv;
$i4=$i4+1;
}
//PROGRESSÃO USO DO CARTÃO DE CRÉDITO MASTER
$totalcartaom=array();
$i4=0;
$mescalculocartao1=1;
while ($mescalculocartao1<=$mes_hoje){
$qrcart2=mysqli_query($_SG['conexao'], "SELECT mes, SUM(valor) as total FROM movimentos WHERE tipo=0 AND conta=3 AND usuario='$usuario' AND mes='$mescalculocartao1' AND ano='$ano_hoje' GROUP BY mes");
$rowcart2=mysqli_fetch_array($qrcart2);
$comprasm=$rowcart2['total'];
$mescalculocartao1++;
$totalcartaom[$i4]=$comprasm;
$i4=$i4+1;
}

//CATEGORIAS SAIDAS POR MES
$catmessaida=array();
$totalcatmesesaida=array();
$i3=0;
$qrcatmesesaida=mysqli_query($_SG['conexao'], "SELECT cat, SUM(valor) as total FROM movimentos WHERE tipo=0 AND conta=1 AND usuario='$usuario' AND mes='$mes_hoje' AND ano='$ano_hoje' GROUP BY cat");
while ($rowcatemesesaida=mysqli_fetch_array($qrcatmesesaida)){
$cat2=$rowcatemesesaida['cat'];
$qr3=mysqli_query($_SG['conexao'], "SELECT nome FROM categorias WHERE id='$cat2'");
$row3=mysqli_fetch_array($qr3);
$categoriasaida=$row3['nome'];
$valorcatsaida=$rowcatemesesaida['total'];
$catmesesaida[$i3]=$categoriasaida;
$totalcatmesesaida[$i3]=$valorcatsaida;
$i3=$i3+1;
}

//CATEGORIAS ENTRADAS POR MES
$catmese=array();
$totalcatmese=array();
$i2=0;
$qrcatmese=mysqli_query($_SG['conexao'], "SELECT cat, SUM(valor) as total FROM movimentos WHERE tipo=1 AND conta=1 AND usuario='$usuario' AND mes='$mes_hoje' AND ano='$ano_hoje' GROUP BY cat");
while ($rowcatemese=mysqli_fetch_array($qrcatmese)){
$cat=$rowcatemese['cat'];
$qr2=mysqli_query($_SG['conexao'], "SELECT nome FROM categorias WHERE id='$cat'");
$row2=mysqli_fetch_array($qr2);
$categoria=$row2['nome'];
$valorcat=$rowcatemese['total'];
$catmese[$i2]=$categoria;
$totalcatmese[$i2]=$valorcat;
$i2=$i2+1;
}

//CATEGORIAS POR MES CARTÃO DE CRÉDITO VISA
$catcartaov=array();
$totalcatcartaov=array();
$i5=0;
$qrcatcartaov=mysqli_query($_SG['conexao'], "SELECT cat, SUM(valor) as total FROM movimentos WHERE tipo=0 AND conta=2 AND usuario='$usuario' AND mes='$mes_hoje' AND ano='$ano_hoje' GROUP BY cat");
while ($rowcatcartaov=mysqli_fetch_array($qrcatcartaov)){
$catcv1=$rowcatcartaov['cat'];
$qrcatcv1=mysqli_query($_SG['conexao'], "SELECT nome FROM categorias WHERE id='$catcv1'");
$rowcv3=mysqli_fetch_array($qrcatcv1);
$categoriacv=$rowcv3['nome'];
$valorcatcv=$rowcatcartaov['total'];
$catcartaov[$i5]=$categoriacv;
$totalcatcartaov[$i5]=$valorcatcv;
$i5=$i5+1;
}
//CATEGORIAS POR MES CARTÃO DE CRÉDITO MASTER
$catcartaom=array();
$totalcatcartaom=array();
$i6=0;
$qrcatcartaom=mysqli_query($_SG['conexao'], "SELECT cat, SUM(valor) as total FROM movimentos WHERE tipo=0 AND conta=3 AND usuario='$usuario' AND mes='$mes_hoje' AND ano='$ano_hoje' GROUP BY cat");
while ($rowcatcartaom=mysqli_fetch_array($qrcatcartaom)){
$catcm2=$rowcatcartaom['cat'];
$qrcatcm2=mysqli_query($_SG['conexao'], "SELECT nome FROM categorias WHERE id='$catcm2'");
$rowcm3=mysqli_fetch_array($qrcatcm2);
$categoriacm=$rowcm3['nome'];
$valorcatcm=$rowcatcartaom['total'];
$catcartaom[$i6]=$categoriacm;
$totalcatcartaom[$i6]=$valorcatcm;
$i6=$i6+1;
}
?>

<script type="text/javascript">
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
	//PROGRESSÃO ANUAL ENTRAS & RECEITAS
    var data = google.visualization.arrayToDataTable([
        ['Nome', 'Cartão Visa','Cartão Master'],
		<?php
		$k = $i4;
		for ($i4 = 0; $i4 < $k; $i4++){
		?>
        ['<?php echo $meslancatao[$i4]?>',  <?php if (empty($totalcartaov[$i4])) echo 0; else echo $totalcartaov[$i4]?>,  <?php if (empty($totalcartaom[$i4])) echo 0; else echo $totalcartaom[$i4]?>],
        <?php } ?>
    ]);
	//TOTAL DE SAIDAS POR CATEGORIA MES
	var data2 = google.visualization.arrayToDataTable([
          ['Categoria', 'Valor'],
          <?php
			$k = $i3;
			for ($i3 = 0; $i3 < $k; $i3++){
			?>
			['<?php echo $catmesesaida[$i3]?>',  <?php if (empty($totalcatmesesaida[$i3])) echo 0; else echo $totalcatmesesaida[$i3]?>],
			<?php } ?>
    ]);
	//TOTAL DE ENTRADAS POR CATEGORIA MES
	var data3 = google.visualization.arrayToDataTable([
          ['Categoria', 'Valor'],
          <?php
			$k = $i2;
			for ($i2 = 0; $i2 < $k; $i2++){
			?>
			['<?php echo $catmese[$i2]?>',  <?php if (empty($totalcatmese[$i2])) echo 0; else echo $totalcatmese[$i2]?>],
			<?php } ?>
    ]);
	//ENTRADAS E SAIDAS POR MES
	var data4 = google.visualization.arrayToDataTable([
		  ['Tipo', 'Valor'],
		  ['Entradas',<?php if (empty($entradas)) echo 0; else echo $entradas?>],
		  ['Sáidas',<?php if (empty($saidas)) echo 0; else echo $saidas?>]
    ]);
	//TOTAL GASTO EM CADA CONTA MÊS
	var data5 = google.visualization.arrayToDataTable([
		  ['Conta', 'Valor'],
		  ['Cartão Visa',<?php if (empty($gastosccv)) echo 0; else echo $gastosccv?>],
		  ['Cartão Master',<?php if (empty($gastosccm)) echo 0; else echo $gastosccm?>],
		  ['Conta Corrente',<?php if (empty($gastoscc)) echo 0; else echo $gastoscc?>],

    ]);
	//ENTRADAS E SAIDAS POR ANO  
	var data6 = google.visualization.arrayToDataTable([
		  ['Tipo', 'Valor'],
		  ['Entradas',<?php if (empty($entradasano)) echo 0; else echo $entradasano?>],
		  ['Sáidas',<?php if (empty($saidasano)) echo 0; else echo $saidasano?>]
    ]);
	//TOTAL GASTO EM CADA CONTA ANO
	var data7 = google.visualization.arrayToDataTable([
		  ['Conta', 'Valor'],
		  ['Cartão Visa',<?php if (empty($gastosccvano)) echo 0; else echo $gastosccvano?>],
		  ['Cartão Master',<?php if (empty($gastosccmano)) echo 0; else echo $gastosccmano?>],
		  ['Conta Corrente',<?php if (empty($gastosccano)) echo 0; else echo $gastosccano?>],

    ]);
	//PROGRESSÃO ENTRADAS & SAIDAS
	var data8 = google.visualization.arrayToDataTable([
         ['Nome', 'Entradas', 'Saídas'],
		<?php
		$k = $i1;
		for ($i1 = 0; $i1 < $k; $i1++){
		?>
        ['<?php echo $meslanct1[$i1]?>',  <?php if (empty($totalemat[$i1])) echo 0; else echo $totalemat[$i1]?>,  <?php if (empty($totalsmat[$i1])) echo 0; else echo $totalsmat[$i1]?>],
        <?php } ?>
    ]);
	//TOTAL DE CONSUMO POR CATEGORIA MES CARTÃO VISA
	var data9 = google.visualization.arrayToDataTable([
          ['Categoria', 'Valor'],
          <?php
			$k = $i5;
			for ($i5 = 0; $i5 < $k; $i5++){
			?>
			['<?php echo $catcartaov[$i5]?>',  <?php if (empty($totalcatcartaov[$i5])) echo 0; else echo $totalcatcartaov[$i5]?>],
			<?php } ?>
    ]);
	//TOTAL DE CONSUMO POR CATEGORIA MES CARTÃO MASTER
	var data10 = google.visualization.arrayToDataTable([
          ['Categoria', 'Valor'],
          <?php
			$k = $i6;
			for ($i6 = 0; $i6 < $k; $i6++){
			?>
			['<?php echo $catcartaom[$i6]?>',  <?php if (empty($totalcatcartaom[$i6])) echo 0; else echo $totalcatcartaom[$i6]?>],
			<?php } ?>
    ]);

	var options = {
          title: 'Cartões de crédito Visa e Master.',
          subtitle: 'Conta Corrente.',
		  curveType: 'none', //function //none
		  //legend: { position: 'bottom' } //top
		  animation:{
			startup: true, //false
			duration: 4000,
			easing: 'inAndOut' // linear - in - out
		  },
    };
	var options2 = {
        legend: 'true',
        pieSliceText: 'label', //percentage - value - none
        title: 'Saídas Conta Corrente.',
		//is3D: true,
        pieStartAngle: 100,
		responsive:false,
    };
	var options3 = {
		  legend: 'true',
          pieSliceText: 'label',
          title: 'Entradas Conta Corrente.',
		  responsive:true,
    };
	var options4 = {
        legend: 'true',
        pieSliceText: 'value', //label - percentage - none
        title: 'Conta Corrente.',
        pieStartAngle: 100,
		responsive:true,
		is3D: true,
    };
	var options5 = {
        legend: 'true',
        //pieSliceText: 'label', //label - value - none
        title: 'Todas às contas.',
        //pieStartAngle: 100,
		responsive:true,
		pieHole: 0.4,
    };
	var options6 = {
        legend: 'true',
        pieSliceText: 'label', //label - value - none
        title: 'Conta Corrente.',
        pieStartAngle: 100,
		responsive:true,
		//is3D: true,
    };
	var options7 = {
        legend: 'true',
        //pieSliceText: 'label',
        title: 'Todas às contas.',
        //pieStartAngle: 100,
		responsive:true,
		pieHole: 0.4,
		is3D:false, //se true, agira como de pizza
    };
    var options8 = {
          title: 'Conta Corrente.',
			animation:{
			startup: true,
			duration: 4000,
			easing: 'out'
		  },
          vAxis: {minValue: 0}

    };
	var options9 = {
        legend: 'true',
        pieSliceText: 'label', //label - value - none
        title: 'Cartão de crédito Visa.',
		//is3D: true,
        pieStartAngle: 100,
		responsive:false,
    };
	var options10 = {
		  legend: 'true',
          pieSliceText: 'label',
        title: 'Cartão de crédito Master.',
		  responsive:false,
    };

	var formatter = new google.visualization.NumberFormat({
        prefix: 'R$ ',
        decimalSymbol: ',',
        groupingSymbol: '.'
    });
    formatter.format(data, 1);
    formatter.format(data, 2);
	formatter.format(data2, 1);
	formatter.format(data3, 1);
	formatter.format(data4, 1);
    formatter.format(data5, 1);
	formatter.format(data6, 1);
    formatter.format(data7, 1);
    formatter.format(data8, 1);
    formatter.format(data8, 2);
	formatter.format(data9, 1);
	formatter.format(data10, 1);
	
    var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));
    chart.draw(data, options);
	var chart2 = new google.visualization.PieChart(document.getElementById('piechart'));
	chart2.draw(data2, options2);
	var chart3 = new google.visualization.PieChart(document.getElementById('piechart2'));
	chart3.draw(data3, options3);
	var chart4 = new google.visualization.PieChart(document.getElementById('piechart3'));
	chart4.draw(data4, options4);
	var chart5 = new google.visualization.PieChart(document.getElementById('donutchart'));
	chart5.draw(data5, options5);
	var chart6 = new google.visualization.PieChart(document.getElementById('piechart4'));
	chart6.draw(data6, options6);
	var chart7 = new google.visualization.PieChart(document.getElementById('donutchart2'));
	chart7.draw(data7, options7);
    var chart8 = new google.visualization.AreaChart(document.getElementById('chart_div'));
    chart8.draw(data8, options8);
	var chart9 = new google.visualization.PieChart(document.getElementById('piechart5'));
	chart9.draw(data9, options9);
	var chart10 = new google.visualization.PieChart(document.getElementById('piechart6'));
	chart10.draw(data10, options10);
		
    }
</script>
<table border="1" cellpadding="1" cellspacing="10"  width="940" align="center">
<tr>
<td align="left"><small><b>Total de Entradas e Saídas mensal.</small>
<div id="piechart3" style="width: 500px; height: 300px;"></div>

<td align="left"><small><b>Total de sáidas em todas as contas por mês.</small>
<div id="donutchart" style="width: 500px; height: 300px;"></div>
</td>
</tr>
<tr>
<td align="left"><small><b>Total de Entradas e Saídas anual.</small>
<div id="piechart4" style="width: 500px; height: 400px;"></div>
</td>
<td align="left"><small><b>Total gasto em todas as contas por ano.</small>
<div id="donutchart2" style="width: 500px; height: 400px;"></div>
</td>
</tr>
<tr>
<td colspan="4"><small><b>Progressão de entradas e saídas.</small>
<div id="chart_div" style="width: 900px; height: 500px"></div>
</td>
</tr>
<tr>
<td><small><b>Percentual de entradas por categoria.</small>
<div id="piechart2" style="width: 500px; height: 300px;"></div>
</td>
<td><small><b>Percentual de saídas por categoria.</small>
<div id="piechart" style="width: 500px; height: 300px;"></div>
</td>
</tr>
<tr>
<td colspan="4"><small><b>Progressão do uso dos cartões de crédito.</small>
<div id="curve_chart" style="width: 900px; height: 500px"></div>
</td>
</tr>
<tr>
<td align="left"><small><b>Consumo mensal do cartão de crédito por categoria.</small>
<div id="piechart5" style="width: 500px; height: 300px;"></div>
</td>
<td align="left"><small><b>Consumo mensal do cartão de crédito por categoria.</small>
<div id="piechart6" style="width: 500px; height: 300px;"></div>
</td>
</tr>
</table>
</body>
</html>