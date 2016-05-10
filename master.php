<?php
include("./conf/config.php");
protegePagina();
include './conf/functions.php';
require_once './conf/versao.php';
$usuario=$_SESSION['usuarioID'];

//Apagar movimentos
if (isset($_GET['acao']) && $_GET['acao'] == 'apagar') {
    $id = $_GET['id'];
    $log=mysqli_query($_SG['conexao'], "SELECT * FROM movimentos WHERE id='$id'");
    $logexc=mysqli_fetch_array($log);
    $idmov=$logexc['id'];
    $tipomov=$logexc['tipo'];
    $descmov=$logexc['descricao'];
    $valormov=$logexc['valor'];
    $catmov=$logexc['cat'];
    $contamov=$logexc['conta'];
    $dataexc = date("Ymd");
	$id_comp_img=$logexc['comp_img'];

    mysqli_query($_SG['conexao'],"INSERT INTO exclusoes (id_mov_exc,tipo_mov,desc_mov,valor_mov,cat_mov,conta_mov,data_exc,usuario_mov) values ('$idmov','$tipomov','$descmov','$valormov','$catmov','$contamov','$dataexc','$usuario')");
    mysqli_query($_SG['conexao'], "DELETE FROM movimentos WHERE id='$id'");
    mysqli_query($_SG['conexao'], "DELETE FROM historico WHERE id_mov='$id'");
	$qr1=mysqli_query($_SG['conexao'], "SELECT * FROM movimentos WHERE comp_img='$id_comp_img'");
	$row1=mysqli_fetch_array($qr1);
	if (empty($row1)){
		mysqli_query($_SG['conexao'], "DELETE FROM comprovantes WHERE id='$id_comp_img'");
	}
    header("Location: ?mes=" . $_GET['mes'] . "&ano=" . $_GET['ano'] . "&ok=2");
    exit();
}

//Editar categorias
if (isset($_POST['acao']) && $_POST['acao'] == 'editar_cat') {
    $id = $_POST['id'];
    $nome = $_POST['nome'];

    mysqli_query($_SG['conexao'], "UPDATE categorias SET nome='$nome' WHERE id='$id'");
    echo mysqli_error($_SG['conexao']);
    header("Location: ?mes=" . $_GET['mes'] . "&ano=" . $_GET['ano'] . "&cat_ok=3");
    exit();
}

//Apagar categorias
if (isset($_GET['acao']) && $_GET['acao'] == 'apagar_cat') {
    $id = $_GET['id'];

    $qr=mysqli_query($_SG['conexao'], "SELECT c.id FROM movimentos g, categorias c WHERE c.id=g.cat && c.id=$id");
    if (mysqli_num_rows($qr)!==0){
        header("Location: ?mes=" . $_GET['mes'] . "&ano=" . $_GET['ano'] . "&cat_err=1");
        exit();
    }
    else{
    mysqli_query($_SG['conexao'], "DELETE FROM categorias WHERE id='$id'");
    echo mysqli_error($_SG['conexao']);
    header("Location: ?mes=" . $_GET['mes'] . "&ano=" . $_GET['ano'] . "&cat_ok=2");
    exit();
	}
}

//Editar movimentos
if (isset($_POST['acao']) && $_POST['acao'] == 'editar_mov') {
	$file_tmp = $_FILES["file"]["tmp_name"];
	$file_name = $_FILES["file"]["name"];
	$file_type = $_FILES["file"]["type"];
	$file_size = $_FILES["file"]["size"];
	$dataimagen=date('dmyHi');
	$nome_r1 = tirarAcentos($file_name);
    $nome_r2 = str_replace(" ", "", $nome_r1);
	$nome="$usuario.$dataimagen.$nome_r2";
	$caminho="./upload_temp/$usuario.$dataimagen.$nome_r2";
	$extensao = @strtolower(end(explode('.',$file_name)));
	$extesoespermitidas= array('png','jpeg','jpg','bmp','pdf','doc','docx','xls','xlsx','html','xml','rar','zip');
	$tamanhoemBytes=@round (($file_size / 1024) / 1024,2);
	$tamanhoemMB=$tamanhoemBytes." MB";
    $id = $_POST['id'];
    $dia = $_POST['dia'];
    $mes = $_POST['mes'];
    $ano = $_POST['ano'];
    $tipo = $_POST['tipo'];
    $cat = $_POST['cat'];
	$conta_lan = $_POST['conta'];
    $descricao = $_POST['descricao'];
    $valor = str_replace(",", ".", $_POST['valor']);
    $dataed = date("Ymd");
	$qred=mysqli_query($_SG['conexao'], "SELECT * FROM movimentos WHERE id='$id'");
	$rowed=mysqli_fetch_array($qred);
	$comp_cad=$rowed['comp_img'];

if (empty($valor)){ 
echo "<script>
alert('O campo VALOR é obrigatório, e precisa ser diferente de zero para editar.'); location.href='master.php';
</script>";
exit();
}
	if ($dia!=$rowed['dia']){
	mysqli_query($_SG['conexao'], "UPDATE movimentos SET dia='$dia', mes='$mes', ano='$ano', tipo='$tipo', cat='$cat', conta='$conta_lan', descricao='$descricao', valor='$valor', edicao='Editado' WHERE id='$id'");
	mysqli_query($_SG['conexao'], "INSERT INTO historico (id_mov,just_id,data,conta_mov,usuario) values ('$id','1','$dataed','3','$usuario')");
    echo mysqli_error($_SG['conexao']);}

	if ($mes!=$rowed['mes']){
	mysqli_query($_SG['conexao'], "UPDATE movimentos SET dia='$dia', mes='$mes', ano='$ano', tipo='$tipo', cat='$cat', conta='$conta_lan', descricao='$descricao', valor='$valor', edicao='Editado' WHERE id='$id'");
	mysqli_query($_SG['conexao'], "INSERT INTO historico (id_mov,just_id,data,conta_mov,usuario) values ('$id','2','$dataed','3','$usuario')");
    echo mysqli_error($_SG['conexao']);}
	
	if ($ano!=$rowed['ano']){
	mysqli_query($_SG['conexao'], "UPDATE movimentos SET dia='$dia', mes='$mes', ano='$ano', tipo='$tipo', cat='$cat', conta='$conta_lan', descricao='$descricao', valor='$valor', edicao='Editado' WHERE id='$id'");
	mysqli_query($_SG['conexao'], "INSERT INTO historico (id_mov,just_id,data,conta_mov,usuario) values ('$id','3','$dataed','3','$usuario')");
    echo mysqli_error($_SG['conexao']);}
	
	if ($tipo!=$rowed['tipo']){
	mysqli_query($_SG['conexao'], "UPDATE movimentos SET dia='$dia', mes='$mes', ano='$ano', tipo='$tipo', cat='$cat', conta='$conta_lan', descricao='$descricao', valor='$valor', edicao='Editado' WHERE id='$id'");
	mysqli_query($_SG['conexao'], "INSERT INTO historico (id_mov,just_id,data,conta_mov,usuario) values ('$id','4','$dataed','3','$usuario')");
    echo mysqli_error($_SG['conexao']);}
	
	if ($cat!=$rowed['cat']){
	mysqli_query($_SG['conexao'], "UPDATE movimentos SET dia='$dia', mes='$mes', ano='$ano', tipo='$tipo', cat='$cat', conta='$conta_lan', descricao='$descricao', valor='$valor', edicao='Editado' WHERE id='$id'");
	mysqli_query($_SG['conexao'], "INSERT INTO historico (id_mov,just_id,data,conta_mov,usuario) values ('$id','5','$dataed','3','$usuario')");
    echo mysqli_error($_SG['conexao']);}
	
	if ($descricao!=$rowed['descricao']){
	mysqli_query($_SG['conexao'], "UPDATE movimentos SET dia='$dia', mes='$mes', ano='$ano', tipo='$tipo', cat='$cat', conta='$conta_lan', descricao='$descricao', valor='$valor', edicao='Editado' WHERE id='$id'");
	mysqli_query($_SG['conexao'], "INSERT INTO historico (id_mov,just_id,data,conta_mov,usuario) values ('$id','6','$dataed','3','$usuario')");
    echo mysqli_error($_SG['conexao']);}
	
	if ($valor!=$rowed['valor']){
	mysqli_query($_SG['conexao'], "UPDATE movimentos SET dia='$dia', mes='$mes', ano='$ano', tipo='$tipo', cat='$cat', conta='$conta_lan', descricao='$descricao', valor='$valor', edicao='Editado' WHERE id='$id'");
	mysqli_query($_SG['conexao'], "INSERT INTO historico (id_mov,just_id,data,conta_mov,usuario) values ('$id','7','$dataed','3','$usuario')");
    echo mysqli_error($_SG['conexao']);}
	
	if ($conta_lan!=$rowed['conta']){
    mysqli_query($_SG['conexao'], "UPDATE movimentos SET dia='$dia', mes='$mes', ano='$ano', tipo='$tipo', cat='$cat', conta='$conta_lan', descricao='$descricao', valor='$valor', edicao='Editado' WHERE id='$id'");
	mysqli_query($_SG['conexao'], "INSERT INTO historico (id_mov,just_id,data,conta_mov,usuario) values ('$id','8','$dataed','$conta_lan','$usuario')");
    echo mysqli_error($_SG['conexao']);}
	
    if (!empty($file_tmp)){
		if (array_search($extensao, $extesoespermitidas) === false) {
			echo "<script>
			alert('São permitidos apenas arquivos nestes formatos: PNG, JPEG, PNG, BMP, PDF, DOC, DOCX, XLS, XLSX, HTML, XML, ZIP e RAR.'); location.href='master.php';
			</script>";
			exit();
		}
		if ($file_size>7340032){ 
			echo "<script>
			alert('O arquivo é muito grande. Tamanho maxímo 7Mb.'); location.href='master.php';
			</script>";
			exit();
		}
		if (empty($comp_cad)){
			copy($file_tmp, "$caminho");
			$fp = fopen($caminho, "rb");
			$filename=fread($fp, $file_size);
			$filename=addslashes($filename);
			fclose($fp);
			mysqli_query($_SG['conexao'], "INSERT INTO comprovantes (comp, nome, tipo, ext, tamanho) values ('$filename','$nome','$file_type','$extensao','$tamanhoemMB')");
			unlink($caminho);
			
			$dados=mysqli_query($_SG['conexao'], "SELECT * FROM comprovantes WHERE id=(SELECT MAX(id) FROM comprovantes)");
			$dados2=mysqli_fetch_array($dados);
			$id_img=$dados2['id'];
			mysqli_query($_SG['conexao'], "UPDATE movimentos SET edicao='Editado', comp_img='$id_img' WHERE id='$id'");
			mysqli_query($_SG['conexao'], "INSERT INTO historico (id_mov,just_id,data,conta_mov,usuario) values ('$id','9','$dataed','$conta_lan','$usuario')");

			header("Location: ?mes=" . $_GET['mes'] . "&ano=" . $_GET['ano'] . "&ok=1");
			exit();
		}
		
		copy($file_tmp, "$caminho");
		$fp = fopen($caminho, "rb");
		$filename=fread($fp, $file_size);
		$filename=addslashes($filename);
		fclose($fp);
		mysqli_query($_SG['conexao'], "UPDATE comprovantes SET comp='$filename', nome='$nome', tipo='$file_type', ext='$extensao', tamanho='$tamanhoemMB' WHERE id='$comp_cad'");
		unlink($caminho);
		mysqli_query($_SG['conexao'], "UPDATE movimentos SET edicao='Editado' WHERE id='$id'");
		mysqli_query($_SG['conexao'], "INSERT INTO historico (id_mov,just_id,data,conta_mov,usuario) values ('$id','9','$dataed','$conta_lan','$usuario')");

		header("Location: ?mes=" . $_GET['mes'] . "&ano=" . $_GET['ano'] . "&ok=1");
		exit();
	}

}

//Cadastar categorias
if (isset($_POST['acao']) && $_POST['acao'] == 2) {
    $nome = $_POST['nome'];

    mysqli_query($_SG['conexao'], "INSERT INTO categorias (nome,usuario) values ('$nome','$usuario')");
    echo mysqli_error($_SG['conexao']);
    header("Location: ?mes=" . $_GET['mes'] . "&ano=" . $_GET['ano'] . "&cat_ok=1");
    exit();
}

//Lançar movimentos
if (isset($_POST['acao']) && $_POST['acao'] == 1) {
	$file_tmp = $_FILES["file"]["tmp_name"];
	$file_name = $_FILES["file"]["name"];
	$file_type = $_FILES["file"]["type"];
	$file_size = $_FILES["file"]["size"];
	$dataimagen=date('dmyHi');
	$nome_r1 = tirarAcentos($file_name);
    $nome_r2 = str_replace(" ", "", $nome_r1);
	$nome="$usuario.$dataimagen.$nome_r2";
	$caminho="./upload_temp/$usuario.$dataimagen.$nome_r2";
	$extensao = @strtolower(end(explode('.',$file_name)));
	$extesoespermitidas= array('png','jpeg','jpg','bmp','pdf','doc','docx','xls','xlsx','html','xml','rar','zip');
	$tamanhoemBytes=@round (($file_size / 1024) / 1024,2);
	$tamanhoemMB=$tamanhoemBytes." MB";
    $data = $_POST['data'];
    $tipo = $_POST['tipo'];
    $cat = $_POST['cat'];
    $descricao = $_POST['descricao'];
    $valor_recebido = str_replace(".", "", $_POST['valor']);
	$valortotal = str_replace( ",", ".",$valor_recebido);
	$parcelas = $_POST['parcelas'];
	$valor = @$valortotal/$parcelas;
    $t = explode("/", $data);
    $dia = $t[0];
    $mes = $t[1];
    $ano = $t[2];

if (empty($valor)){ 
echo "<script>
alert('O campo VALOR é obrigatório, e precisa ser diferente de zero.'); location.href='master.php';
</script>";
exit();
}
	$n=1;
	if (!empty($file_tmp)){
		if (array_search($extensao, $extesoespermitidas) === false) {
			echo "<script>
			alert('São permitidos apenas arquivos nestes formatos: PNG, JPEG, PNG, BMP, PDF, DOC, DOCX, XLS, XLSX, HTML, XML, ZIP e RAR.'); location.href='master.php';
			</script>";
			exit();
		}
		if ($file_size>7340032){ 
			echo "<script>
			alert('O arquivo é muito grande. Tamanho maxímo 7Mb.'); location.href='master.php';
			</script>";
			exit();
		}
		copy($file_tmp, "$caminho");
		$fp = fopen($caminho, "rb");
		$filename=fread($fp, $file_size);
		$filename=addslashes($filename);
		fclose($fp);
		mysqli_query($_SG['conexao'], "INSERT INTO comprovantes (comp, nome, tipo, ext, tamanho) values ('$filename','$nome','$file_type','$extensao','$tamanhoemMB')");
		unlink($caminho);
		
	while ($n <= $parcelas) {
	$dados=mysqli_query($_SG['conexao'], "SELECT * FROM comprovantes WHERE id=(SELECT MAX(id) FROM comprovantes)");
	$dados2=mysqli_fetch_array($dados);
	$id_img=$dados2['id'];
    mysqli_query($_SG['conexao'], "INSERT INTO movimentos (dia,mes,ano,tipo,descricao,valor,cat,conta,nparcela,parcelas,usuario,comp_img) values ('$dia','$mes','$ano','$tipo','$descricao','$valor','$cat','3','$n','$parcelas','$usuario','$id_img')");

    header("Location: ?mes=" . $_GET['mes'] . "&ano=" . $_GET['ano'] . "&ok=1");
	if ($mes<=11){
	$mes++;}
	else{
	$mes = 1;
	$ano++;}
	$n++;
	}
	exit();
	}

	while ($n <= $parcelas) {
    mysqli_query($_SG['conexao'], "INSERT INTO movimentos (dia,mes,ano,tipo,descricao,valor,cat,conta,nparcela,parcelas,usuario) values ('$dia','$mes','$ano','$tipo','$descricao','$valor','$cat','3','$n','$parcelas','$usuario')");

    header("Location: ?mes=" . $_GET['mes'] . "&ano=" . $_GET['ano'] . "&ok=1");
	if ($mes<=11){
	$mes++;}
	else{
	$mes = 1;
	$ano++;}
	$n++;
	}
	exit();
}

//Limite do cartão
if (isset($_POST['acao']) && $_POST['acao'] == 'limite_master') {
    $valor_recebido = str_replace(".", "", $_POST['valor']);
	$limitemaster = str_replace( ",", ".",$valor_recebido);
	$diavenc = $_POST['diavenc'];

    mysqli_query($_SG['conexao'], "UPDATE usuarios SET master='$limitemaster', dia_venc_m='$diavenc' WHERE id='$usuario'");
    echo mysqli_error($_SG['conexao']);
    header("Location: ?mes=" . $_GET['mes'] . "&ano=" . $_GET['ano']);
    exit();
}

//Cadastrar orçamento
if (isset($_POST['acao']) && $_POST['acao'] == 'cad_orcamento') {
    $valor_recebido = str_replace(".", "", $_POST['valor']);
	$valor_orcamento = str_replace( ",", ".",$valor_recebido);
	$tipo = $_POST['tipo'];
    $data = $_POST['data'];
	$t = explode("/", $data);
    $dia = $t[0];
    $mes = $t[1];
    $ano = $t[2];
	$valida_meses=12-$mes+1;
	
if (empty($valor_orcamento)){
echo "<script>
alert('O campo VALOR é obrigatório, e precisa ser diferente de zero.'); location.href='master.php'; historico.go(-1);
</script>";
exit;
}
	if ($tipo!=0){
	mysqli_query($_SG['conexao'], "INSERT INTO orcamento (mes,ano,valor,conta,usuario) values ('$mes','$ano','$valor_orcamento','3','$usuario')");
    echo mysqli_error($_SG['conexao']);
    header("Location: ?mes=" . $_GET['mes'] . "&ano=" . $_GET['ano']);
	exit();
	}
	$n=1;
	while ($n <= $valida_meses) {
    mysqli_query($_SG['conexao'], "INSERT INTO orcamento (mes,ano,valor,conta,usuario) values ('$mes','$ano','$valor_orcamento','3','$usuario')");
    echo mysqli_error($_SG['conexao']);
    header("Location: ?mes=" . $_GET['mes'] . "&ano=" . $_GET['ano']);
	if ($mes<=11){
	$mes++;}
	$n++;
	}
	exit();
}

//Editar orçamento
if (isset($_POST['acao']) && $_POST['acao'] == 'ed_orcamento') {
    $valor_recebido = str_replace(".", "", $_POST['valor']);
	$valor_orcamento = str_replace( ",", ".",$valor_recebido);
	$tipo = $_POST['tipo'];
    $data = $_POST['data'];
	$t = explode("/", $data);
    $dia = $t[0];
    $mes = $t[1];
    $ano = $t[2];
	$valida_meses=12-$mes+1;
	
if (empty($valor_orcamento)){
echo "<script>
alert('O campo VALOR é obrigatório, e precisa ser diferente de zero.'); location.href='master.php'; historico.go(-1);
</script>";
exit();
}
	if ($tipo!=0){
	mysqli_query($_SG['conexao'], "UPDATE orcamento SET valor='$valor_orcamento' WHERE mes='$mes' && conta='3' && usuario='$usuario'");
    echo mysqli_error($_SG['conexao']);
	header("Location: ?mes=" . $_GET['mes'] . "&ano=" . $_GET['ano']);
    exit();
	}
    mysqli_query($_SG['conexao'], "UPDATE orcamento SET valor='$valor_orcamento' WHERE mes>=$mes && ano='$ano' && conta='3' && usuario='$usuario'");
    echo mysqli_error($_SG['conexao']);
	header("Location: ?mes=" . $_GET['mes'] . "&ano=" . $_GET['ano']);
    exit();
}

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

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title id='titulo'>CARTÃO MASTER</title>
<link href="./conf/img/favicon.png" rel="icon" type="image/png"/>
<meta name="LANGUAGE" content="Portuguese" />
<meta name="AUDIENCE" content="all" />
<meta name="RATING" content="GENERAL" />
<link href="./conf/css/styles.css" rel="stylesheet" type="text/css" />
<link id="scrollUpTheme" rel="stylesheet" href="./conf/css/image.css">
<link rel="stylesheet" href="./conf/css/calculadora.css">
<script LANGUAGE="JavaScript" src="./conf/js/scripts.js"></script>
<script src="./conf/js/jquery.js"></script>
<script src="./conf/js/jquery.scroll.topo.js"></script>
<script src="./conf/js/jquery.easing.js"></script>
<script src="./conf/js/jquery.easing.compatibilidade.js"></script>
<script LANGUAGE="JavaScript" src="./conf/js/jquery.validar.formulario.js"></script>
<script src="./conf/js/jquery.calc.js"></script>
<script src="./conf/js/jquery.calculadora.js"></script>
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
			src: './conf/img/topo.png'
			}
		});
	});
$('#scrollUpTheme').attr('href', './conf/css/image.css?1.1');
$('.image-switch').addClass('active');
} else {
	$(function () {
	$.scrollUp({
		animation: 'slide',
		activeOverlay: 'false'
		});
	});
$('#scrollUpTheme').attr('href', './conf/css/image.css?1.1');
$('.image-switch').addClass('active');
}
});
</script>
<script type="text/javascript">
	$(document).ready(function(){
		$('#formulario_lancamento').validate({
			rules: {
				valor: {
					required: true,
				},
				parcelas: {
					required: true,
					digits: true
				},
			},
			messages: {
				valor: {
					required: "Campo obrigatório.",
				},
				parcelas: {
					required: "Campo obrigatório.",
					digits: "Digite apenas números."
				},
			}
		});
	});
</script>
<script type="text/javascript">
	$(document).ready(function(){
		$('#form_alt_senha').validate({
			rules: {
				novasenha: {
					required: true,
					minlength: 6
				},
				novasenhaconf: {
					required: true,
					equalTo: "#novasenha"
				},
			},
			messages: {
				novasenha: {
					required: "Campo obrigatório.",
					minlength: "Mínimo 6 caracteres."
				},
				novasenhaconf: {
					required: "Campo obrigatório.",
					equalTo: "Senhas não conferem."
				},
			}
		});
	});
</script>
<script type="text/javascript">
	$(document).ready(function(){
		$('#form_cart').validate({
			rules: {
				diavenc: {
					required: true,
					maxlength: 2,
					digits: true
				},
			},
			messages: {
				diavenc: {
					required: "Campo obrigatório.",
					digits: "Apenas números"
				},
			}
		});
	});
</script>
<script>
function passwordStrength(password)
{
	var desc = new Array();
	desc[0] = "";
	desc[1] = "";
	desc[2] = "";
	desc[3] = "";
	desc[4] = "";
	desc[5] = "";
	var score   = 0;
	if (password.length > 6) score++;
	if ( ( password.match(/[a-z]/) ) && ( password.match(/[A-Z]/) ) ) score++;
	if (password.match(/\d+/)) score++;
	if ( password.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/) )	score++;
	if (password.length > 12) score++;
	document.getElementById("passwordDescription").innerHTML = desc[score];
	document.getElementById("passwordStrength").className = "strength" + score;
}
</script>
<script>
$(function () {
	$.calculator.setDefaults({showOn: 'both', buttonImageOnly: true, buttonImage: './conf/img/calc.png'});
	$('#valor').calculator(); //Calculadora comum para lançamento de movimentos
	$('#valororcamento').calculator({layout: $.calculator.scientificLayout}); //Calculadora cientifica para lançamento de orçamento
	$('#edorcamento').calculator({layout: $.calculator.scientificLayout}); //Calculadora cientifica para edição de orçamento
});
</script>

</head>
<body style="padding:10px">

<table cellpadding="1" cellspacing="10"  width="1000" align="center" style="background-color:#033">
<tr>
<td colspan="11" style="background-color:#005B5B;">
<h2 style="margin:5px"><a href=index.php style="color:#008B8B">Conta Corrente</a> | <a href=visa.php style="color:#008B8B">Cartão Visa</a> | <a href=master.php style="color:#FFF">Cartão Master</a> | <a href=./dashboard/dashboard.php style="color:#008B8B">Dashboard</a></h2>
</td>
<td colspan="2" align="right" style="background-color:#005B5B;">
<a style="color:#FFF" href="?mes=<?php echo date('m')?>&ano=<?php echo date('Y')?>">Hoje:<strong> <?php echo date('d')?> de <?php echo mostraMes(date('m'))?> de <?php echo date('Y')?></a></strong>&nbsp; 
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
<?php
$qrvisita=mysqli_query($_SG['conexao'], "SELECT * FROM usuarios where id='$usuario'");
$rowvisita=mysqli_fetch_array($qrvisita);

$qracesso=mysqli_query($_SG['conexao'], "SELECT * FROM usuarios where id='$usuario'");
$rowacesso=mysqli_fetch_array($qracesso);
$n=$rowacesso['n_acesso_f'];
$n_acesso=$n+1;
?>
<td>
<b><font color="#000" size=1><?php if ($n>0) echo "Último acesso: ".date('d/m/Y H:i:s', strtotime($rowvisita['ultimavisita'])); else echo ""?></font>
</td>
<td><font color="#000"><?php echo $mensagem?><?php echo " "?><?php echo $_SESSION['usuarioNome'];?><?php echo " "?><?php echo $_SESSION['usuarioSobrenome'];?>.</font></b>
</td>
<td align="right" style="font-size:13px; color:rgba(4, 45, 191, 1)">
<a href="javascript:;" style="font-size:12px; color:rgba(4, 45, 191, 1)" onclick="abreFecha('orcamento')"> [ Orçamento ]</a>
<a href="javascript:;" style="font-size:12px; color:rgba(4, 45, 191, 1)" onclick="abreFecha('alterar_senha')">[ Alterar senha ]</a>
<a href="logout.php" style="font-size:12px; color:rgba(4, 45, 191, 1)"><?php echo " [ Fazer logout ]"?></a>
</td>
</tr>
<tr>
<td>
<b><font color="#000" size=1><?php echo"Acesso Nº: "?><?php if ($n=0) echo "1"; else echo"$n_acesso" ?></font>
</td>
</tr>
</table>

<table cellpadding="5" cellspacing="0" width="1000" align="center">
<tr style="display:none; background-color:#E0E0E0" id="alterar_senha">
<td align="left">
<form id="form_alt_senha" method="post" action="cadastro.php">
<input type="hidden" name="acao" value="alterar_senha" />
<input type="hidden" name="pagina" value="index.php" />
<input type="hidden" name="usuario" value="<?php echo $usuario?>" />
<b>Nova senha:</b> <font color="#FF0000" size=2><input type="password" name="novasenha" id="novasenha" onkeyup="passwordStrength(this.value)"></font> <b>Confirmar nova senha:</b> <font color="#FF0000" size=2><input type="password" name="novasenhaconf" id="novasenhaconf"></font><br>
<label for="passwordStrength"><font size=2>Força da senha</font></label><br>
<div id="passwordDescription"></div>
<div id="passwordStrength" class="strength0"></div>
<p align="right">
<input type="submit" class="input" value="Alterar" /></p>
</form>
</td>
</tr>
</table>

<table cellpadding="5" cellspacing="0" width="1000" align="center">
<?php
$qr=mysqli_query($_SG['conexao'], "SELECT SUM(valor) as total FROM orcamento WHERE mes='$mes_hoje' && ano='$ano_hoje' && conta=3 && usuario='$usuario'");
$row=mysqli_fetch_array($qr);
$total=$row['total'];

$qr=mysqli_query($_SG['conexao'], "SELECT SUM(valor) as total FROM movimentos WHERE tipo=0 && conta=3 && usuario='$usuario' && mes='$mes_hoje' && ano='$ano_hoje'");
$row=mysqli_fetch_array($qr);
$gasto=$row['total'];
$resta=$total-$gasto;

if ($total>0){
$percento = @round (($gasto/$total) * 100,2);

if ($percento>100){
	$comp=100;
}
if ($percento<=100){
	$comp=$percento;
}
}
else {
	$percento='-1';
	$comp='100';
}
?>
<tr style="display:none;" id="orcamento">
<td align="left" style="font-size:11px; color:rgb(0, 0, 0)">
<a href="javascript:;" style="font-size:12px" onclick="abreFecha('lancar_orcamento')" title="Gereciar orçamento">Orçamento mensal: <?php echo formata_dinheiro($gasto)?><?php echo " de "?><?php echo formata_dinheiro($total)?><?php echo " resta "?><font color="<?php if ($resta <= 0) echo "#C00"?>"><?php echo formata_dinheiro($resta)?></font>.</a><br>
<style type="text/CSS">
.outter{
	height:20px;
	width:1000px;
	border:solid 1px #000;
}
.inner{
	height:20px;
	width:<?php echo $comp ?>%;
	background: <?php if ($percento<=0) echo "rgb(240,240,240); /* Old browsers */
	background: -moz-linear-gradient(top,  rgba(240,240,240,1) 0%, rgba(228,228,228,1) 50%, rgba(223,223,223,1) 51%, rgba(240,240,240,1) 100%); /* FF3.6-15 */
	background: -webkit-linear-gradient(top,  rgba(240,240,240,1) 0%,rgba(228,228,228,1) 50%,rgba(223,223,223,1) 51%,rgba(240,240,240,1) 100%); /* Chrome10-25,Safari5.1-6 */
	background: linear-gradient(to bottom,  rgba(240,240,240,1) 0%,rgba(228,228,228,1) 50%,rgba(223,223,223,1) 51%,rgba(240,240,240,1) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f0f0f0', endColorstr='#f0f0f0',GradientType=0 ); /* IE6-9 */"; else if (($percento >0) AND ($percento <=65)) echo "rgb(180,221,180); /* Old browsers */
	background: -moz-linear-gradient(top,  rgba(180,221,180,1) 0%, rgba(131,199,131,1) 4%, rgba(131,199,131,1) 4%, rgba(131,199,131,1) 30%, rgba(131,199,131,1) 42%, rgba(0,138,0,1) 100%, rgba(0,87,0,1) 100%, rgba(0,36,0,1) 100%); /* FF3.6-15 */
	background: -webkit-linear-gradient(top,  rgba(180,221,180,1) 0%,rgba(131,199,131,1) 4%,rgba(131,199,131,1) 4%,rgba(131,199,131,1) 30%,rgba(131,199,131,1) 42%,rgba(0,138,0,1) 100%,rgba(0,87,0,1) 100%,rgba(0,36,0,1) 100%); /* Chrome10-25,Safari5.1-6 */
	background: linear-gradient(to bottom,  rgba(180,221,180,1) 0%,rgba(131,199,131,1) 4%,rgba(131,199,131,1) 4%,rgba(131,199,131,1) 30%,rgba(131,199,131,1) 42%,rgba(0,138,0,1) 100%,rgba(0,87,0,1) 100%,rgba(0,36,0,1) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#b4ddb4', endColorstr='#002400',GradientType=0 ); /* IE6-9 */"; else if (($percento >65) AND ($percento <=85)) echo "rgb(252,243,188); /* Old browsers */
	background: -moz-linear-gradient(top,  rgba(252,243,188,1) 0%, rgba(252,232,78,1) 50%, rgba(248,219,0,1) 51%, rgba(251,239,147,1) 100%); /* FF3.6-15 */
	background: -webkit-linear-gradient(top,  rgba(252,243,188,1) 0%,rgba(252,232,78,1) 50%,rgba(248,219,0,1) 51%,rgba(251,239,147,1) 100%); /* Chrome10-25,Safari5.1-6 */
	background: linear-gradient(to bottom,  rgba(252,243,188,1) 0%,rgba(252,232,78,1) 50%,rgba(248,219,0,1) 51%,rgba(251,239,147,1) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#fcf3bc', endColorstr='#fbef93',GradientType=0 ); /* IE6-9 */"; else echo "rgb(246,25,0); /* Old browsers */
	background: -moz-linear-gradient(top,  rgba(246,25,0,1) 16%, rgba(186,0,0,1) 47%, rgba(246,25,0,1) 87%); /* FF3.6-15 */
	background: -webkit-linear-gradient(top,  rgba(246,25,0,1) 16%,rgba(186,0,0,1) 47%,rgba(246,25,0,1) 87%); /* Chrome10-25,Safari5.1-6 */
	background: linear-gradient(to bottom,  rgba(246,25,0,1) 16%,rgba(186,0,0,1) 47%,rgba(246,25,0,1) 87%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f61900', endColorstr='#f61900',GradientType=0 ); /* IE6-9 */"?>
}
</style>
<div class="outter">
<div class="inner"><center><?php if ($percento <0) echo "Não "."há "."orçamento "."cadastrado."; else echo $percento ."%"?></center></div>
</div>
</td>
</tr>
<tr style="display:none; background-color:#E0E0E0" id="lancar_orcamento">
<td>
<a href="javascript:;" style="font-size:12px; color:rgba(4, 45, 191, 1)" onclick="abreFecha('cad_orcamento')" title="Cadastre apenas uma vez."> [ Cad. Orçamento ]</a> 
<a href="javascript:;" style="font-size:12px; color:rgba(4, 45, 191, 1)" onclick="abreFecha('ed_orcamento')" title="Edite um ou mais meses"> [ Editar Orçamento ]</a>
</dt>
</tr>
<tr style="display:none; background-color:#E0E0E0" id="cad_orcamento">
<td>
<form method="post" action="?mes=<?php echo $mes_hoje?>&ano=<?php echo $ano_hoje?>">
<input type="hidden" name="acao" value="cad_orcamento" />
Valor do orçamento: R$  <input type=text value="<?php echo $total?>" name=valor id="valororcamento" length=15 onKeyPress="return(FormataReais(this,'.',',',event))">&nbsp;|&nbsp;
<input type="radio" name="tipo" value="1" checked /> Este mês &nbsp; 
<input type="radio" name="tipo" value="0" /> Este ano &nbsp;|&nbsp;
Data inicial: <input type="text" name="data" size="11" maxlength="10" value="<?php echo date('d')?>/<?php echo $mes_hoje?>/<?php echo $ano_hoje?>" />
<input type="submit" class="input" value="Gravar" />
</form>
</td>
</tr>
<tr style="display:none; background-color:#E0E0E0" id="ed_orcamento">
<td>
<form method="post" action="?mes=<?php echo $mes_hoje?>&ano=<?php echo $ano_hoje?>">
<input type="hidden" name="acao" value="ed_orcamento" />
Novo valor: R$  <input type=text value="<?php echo $total?>" name=valor id="edorcamento" length=15 onKeyPress="return(FormataReais(this,'.',',',event))">&nbsp;|&nbsp;
<input type="radio" name="tipo" value="1" checked /> Somente este &nbsp; 
<input type="radio" name="tipo" value="0" /> Este e futuros&nbsp;|&nbsp;
Data inicial: <input type="text" name="data" size="11" maxlength="10" value="<?php echo date('d')?>/<?php echo $mes_hoje?>/<?php echo $ano_hoje?>" />
<input type="submit" class="input" value="Gravar" />
</form>
</td>
</tr>
</table>

<table cellpadding="10" cellspacing="0" width="1000" align="center" >
<tr>
<td colspan="2">
<h2><?php echo mostraMes($mes_hoje)?>/<?php echo $ano_hoje?></h2>
</td>
<td align="right">
<a href="javascript:;" onclick="abreFecha('add_cat')" class="bnt">[+] Adicionar Categoria</a>
<a href="javascript:;" onclick="abreFecha('add_movimento')" class="bnt"><strong>[+] Adicionar Movimento</strong></a>
</td>
</tr>

<tr >
<td colspan="3" >

    <?php
if (isset($_GET['cat_err']) && $_GET['cat_err']==1){
?>

<div style="padding:5px; background-color:#FF6; text-align:center; color:#030">
<strong>Esta categoria não pode ser removida, pois há movimentos associados a mesma</strong>
</div>

<?php }?>

    <?php
if (isset($_GET['cat_ok']) && $_GET['cat_ok']==2){
?>

<div style="padding:5px; background-color:#9CC; text-align:center; color:#000">
<strong>Categoria removida com sucesso!</strong>
</div>

<?php }?>
    
<?php
if (isset($_GET['cat_ok']) && $_GET['cat_ok']==1){
?>

<div style="padding:5px; background-color:#9CC; text-align:center; color:#000">
<strong>Categoria Cadastrada com sucesso!</strong>
</div>

<?php }?>
    
<?php
if (isset($_GET['cat_ok']) && $_GET['cat_ok']==3){
?>

<div style="padding:5px; background-color:#9CC; text-align:center; color:#000">
<strong>Categoria alterada com sucesso!</strong>
</div>

<?php }?>

<?php
if (isset($_GET['ok']) && $_GET['ok']==1){
?>

<div style="padding:5px; background-color:#9CC; text-align:center; color:#000">
<strong>Movimento Cadastrado com sucesso!</strong>
</div>

<?php }?>

<?php
if (isset($_GET['ok']) && $_GET['ok']==2){
?>

<div style="padding:5px; background-color:#900; text-align:center; color:#FFF">
<strong>Movimento removido com sucesso!</strong>
</div>

<?php }?>
    
<?php
if (isset($_GET['ok']) && $_GET['ok']==3){
?>

<div style="padding:5px; background-color:#9CC; text-align:center; color:#000">
<strong>Movimento alterado com sucesso!</strong>
</div>

<?php }?>

<div style=" background-color:#F1F1F1; padding:10px; border:1px solid #999; margin:5px; display:none" id="add_cat">
<h3>Adicionar Categoria</h3>
<table width="100%">
<tr>
<td valign="top">
<form method="post" action="?mes=<?php echo $mes_hoje?>&ano=<?php echo $ano_hoje?>">
<input type="hidden" name="acao" value="2" />
Nome: <input type="text" name="nome" size="20" maxlength="50" />
<br />
<br />
<input type="submit" class="input" value="Gravar" />
</form>

            </td>
            <td valign="top" align="right">
                <b>Editar/Remover Categorias:</b><br/><br/>
<?php
$qr=mysqli_query($_SG['conexao'], "SELECT id, nome FROM categorias where usuario='$usuario' ORDER BY nome");
while ($row=mysqli_fetch_array($qr)){
?>
                <div id="editar2_cat_<?php echo $row['id']?>">
<?php echo $row['nome']?>  
                    
                     <a style="font-size:10px; color:#666" onclick="return confirm('Tem certeza que deseja remover esta categoria?\nAtenção: Apenas categorias sem movimentos associados poderão ser removidas.')" href="?mes=<?php echo $mes_hoje?>&ano=<?php echo $ano_hoje?>&acao=apagar_cat&id=<?php echo $row['id']?>" title="Remover">[remover]</a>
                     <a href="javascript:;" style="font-size:10px; color:#666" onclick="document.getElementById('editar_cat_<?php echo $row['id']?>').style.display=''; document.getElementById('editar2_cat_<?php echo $row['id']?>').style.display='none'" title="Editar">[editar]</a>
                    
                </div>
                <div style="display:none" id="editar_cat_<?php echo $row['id']?>">
                    
<form method="post" action="?mes=<?php echo $mes_hoje?>&ano=<?php echo $ano_hoje?>">
<input type="hidden" name="acao" value="editar_cat" />
<input type="hidden" name="id" value="<?php echo $row['id']?>" />
<input type="text" name="nome" value="<?php echo $row['nome']?>" size="20" maxlength="50" />
<input type="submit" class="input" value="Alterar" />
</form> 
                </div>

<?php }?>

            </td>
        </tr>
    </table>
</div>

<div style=" background-color:#F1F1F1; padding:10px; border:1px solid #999; margin:5px; display:none" id="add_movimento">
<h3>Adicionar Movimento</h3>
<?php
$qr=mysqli_query($_SG['conexao'], "SELECT id, nome FROM categorias where usuario='$usuario' ORDER BY nome");
if (mysqli_num_rows($qr)==0)
	echo "Adicione ao menos uma categoria";

else{
?>
<form id="formulario_lancamento" enctype="multipart/form-data" method="post" action="?mes=<?php echo $mes_hoje?>&ano=<?php echo $ano_hoje?>">
<input type="hidden" name="acao" value="1" />
<strong>Data: </strong>
<input type="text" name="data" size="11" maxlength="10" value="<?php echo date('d')?>/<?php echo date('m')?>/<?php echo date('Y')?>" />
&nbsp;  |  &nbsp;
<strong>Tipo:</strong>
<label for="tipo_receita" style="color:rgba(4, 45, 191, 1)"><input type="radio" name="tipo" value="1" id="tipo_receita" /> Receita</label>&nbsp; 
<label for="tipo_despesa" style="color:#C00"><input type="radio" name="tipo" value="0" checked id="tipo_despesa" /> Despesa</label>
&nbsp;  |  &nbsp;
<strong>Categoria:</strong>
<select name="cat">
<?php
while ($row=mysqli_fetch_array($qr)){
?>
<option value="<?php echo $row['id']?>"><?php echo $row['nome']?></option>
<?php }?>
</select>

<br />
<br />

<strong>Descrição:</strong><br />
<input type="text" name="descricao" size="100" maxlength="255" />
<br />
<br />

<font color="#000" size=1>Obs.: Em gastos parcelados, deve ser informado o valor total da compra, <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;e não o valor da parcela.</font><br />
<strong>Valor:</strong> R$ <font color="#FF0000" size=2><input type=text name=valor id="valor" length=15 onKeyPress="return(FormataReais(this,'.',',',event))"></font>
&nbsp;  |  &nbsp;
<strong>Parcelas:</strong>
<font color="#FF0000" size=2><input type="text" value="1" name="parcelas" size="2" maxlength="4" id="parcelas"/></font>

<strong>Comprovante: </strong><input id="file" name="file" type="file" />

<br />
<br />
<center>
<input type="submit" class="input" value="Gravar" />
</center>
</form>
<?php }?>
</div>
</td>
</tr>
<tr>
<td align="left" valign="top" width="450" style="background-color:#D3FFE2">

<?php
$qr=mysqli_query($_SG['conexao'], "SELECT SUM(valor) as total FROM movimentos WHERE tipo=1 && conta=3 && usuario='$usuario' && mes='$mes_hoje' && ano='$ano_hoje'");
$row=mysqli_fetch_array($qr);
$entradas=$row['total'];

$qr=mysqli_query($_SG['conexao'], "SELECT SUM(valor) as total FROM movimentos WHERE tipo=0 && conta=3 && usuario='$usuario' && mes='$mes_hoje' && ano='$ano_hoje'");
$row=mysqli_fetch_array($qr);
$saidas=$row['total'];

$resultado_mes=$saidas;

$qr=mysqli_query($_SG['conexao'], "SELECT * FROM usuarios WHERE id='$usuario'");
$row=mysqli_fetch_array($qr);
$dia_venc=$row['dia_venc_m'];
?>

    <fieldset>
        <legend><strong>Balanço Mensal</strong></legend>
        <table cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td><span style="font-size:18px; color:#000">Limite liberado no mês:</span></td>
                <td align="right"><span style="color:rgba(4, 45, 191, 1); font-size:18px"><?php echo formata_dinheiro($entradas) ?></span></td>
            </tr>
            <tr>
                <td><span style="font-size:18px; color:#000">Vencimento todo dia:</span></td>
                <td align="right"><span style="color:#000; font-size:18px"><?php echo $dia_venc?></span></td>
            </tr>
            <tr>
                <td colspan="2">
                    <hr size="1" />
                </td>
            </tr>
            <tr>
                <td><strong style="font-size:22px; color:#000">Valor da Fatura:</strong></td>
                <td align="right"><strong style="color:#C00; font-size:22px"><?php echo formata_dinheiro($saidas) ?></strong></td>
            </tr>
        </table>
    </fieldset>

</td>
<td width="15">
</td>
<td align="left" valign="top" width="450" style="background-color:#F1F1F1">
<fieldset>
<legend>Balanço Geral</legend>

<?php

$qr=mysqli_query($_SG['conexao'], "SELECT SUM(valor) as total FROM movimentos WHERE tipo=1 && conta=3 && usuario='$usuario'");
$row=mysqli_fetch_array($qr);
$entradas=$row['total'];

$qr=mysqli_query($_SG['conexao'], "SELECT SUM(valor) as total FROM movimentos WHERE tipo=0 && conta=3 && usuario='$usuario'");
$row=mysqli_fetch_array($qr);
$saidas=$row['total'];

$qr=mysqli_query($_SG['conexao'], "SELECT dia_venc_m, SUM(master) as total FROM usuarios WHERE id='$usuario'");
$row=mysqli_fetch_array($qr);
$valormaster=$row['total'];
$dia_venc_m=$row['dia_venc_m'];
$master=formata_dinheiro($valormaster);

$resultado_geral=$valormaster+$entradas-$saidas;
?>

<table cellpadding="0" cellspacing="0" width="100%">
<tr>
<td><span style="font-size:18px; color:#000">Limite total do cartão:</span></td>
<td align="right"><span style="font-size:18px"><a href="javascript:;" style="font-size:16px; color:rgba(4, 45, 191, 1)" onclick="abreFecha('limite_master');" title="Alterar limite"><?php echo $master?></a></span></td>
</tr>
<tr style="display:none; background-color:#E0E0E0" id="limite_master">
<td>
<form id="form_cart" method="post" action="?mes=<?php echo $mes_hoje?>&ano=<?php echo $ano_hoje?>">
<input type="hidden" name="acao" value="limite_master" />
<b>Valor:</b> R$  <input type=text value="<?php echo $valormaster?>" name=valor length=15 onKeyPress="return(FormataReais(this,'.',',',event))"><br>
<b>Dia vencimento:</b> <font color="#FF0000" size=2><input type="text" value="<?php echo $dia_venc_m?>" name="diavenc" id="diavenc" size="1" maxlength="2"></font>
<input type="submit" class="input" value="Gravar" />
</form>
</td>
</tr>
<tr>
<td><span style="font-size:18px; color:#000">Valor total gasto com cartão:</span></td>
<td align="right"><span style="font-size:13px; color:#C00"><?php echo formata_dinheiro($saidas)?></span></td>
</tr>
<tr>
<td colspan="2">
<hr size="1" />
</td>
</tr>
<tr>
<td><strong style="font-size:22px; color:#000">Crédito Disponível:</strong></td>
<td align="right"><strong style="font-size:22px; color:<?php if ($resultado_geral<0) echo "#C00"; else echo "rgba(4, 45, 191, 1)"?>"><?php echo formata_dinheiro($resultado_geral)?></strong></td>
</tr>
</table>

</fieldset>
</td>
</tr>
</table>

<table border="0" cellpadding="5" cellspacing="1" width="1000" align="center">
<td>
<a href="javascript:;" style="font-size:17px; color:#0000FF" onclick="abreFecha('export_pdf');" title="Relatórios PDF">[Exportar dados]</a>
</td>
</tr>
<tr style="display:none; background-color:#E0E0E0" id="export_pdf">
<td style="font-size:14px">
<b>
<center>Fatura mensal</b><br>
Informe mês e ano desejados.</center>
<br>
<br>
<form method="post" action="./exportar/movimentos.php">
<input type="hidden" name="acao" value="fatura" />
<input type="hidden" name="nome" value="Cartão de crédito Master" />
<input type="hidden" name="conta" value="3" />
<input type="hidden" name="diavenc" value="<?php echo $dia_venc_m?>" />
Mês: <input type="number" name="mes" size="2" maxlength="2" value="<?php echo $mes_hoje?>" />
<br><br>
Ano: <input type="number" name="ano" size="4" maxlength="4" value="<?php echo $ano_hoje?>" />
<br><br><center>
<input type="submit" class="input" value="Exportar" />
</form>
</td>
<td style="font-size:14px">
<b>
<center>Estatística mensal</b><br>
Informe mês e ano desejados.</center>
<br>
<br>
<form method="post" action="./exportar/estatistica.php">
<input type="hidden" name="acao" value="estatistica_mensal_cart" />
<input type="hidden" name="nome" value="Cartão de crédito Master" />
<input type="hidden" name="conta" value="3" />
Mês: <input type="number" name="mes" size="2" maxlength="2" value="<?php echo $mes_hoje?>" />
<br><br>
Ano: <input type="number" name="ano" size="4" maxlength="4" value="<?php echo $ano_hoje?>" />
<br><br><center>
<input type="submit" class="input" value="Exportar" />
</form>
</td>
<td style="font-size:14px">
<b>
<center>Estatística anual</b><br>
Informe o ano desejado.</center>
<br>
<br>
<form method="post" action="./exportar/estatistica.php">
<input type="hidden" name="acao" value="estatistica_anual_cart" />
<input type="hidden" name="nome" value="Cartão de crédito Master" />
<input type="hidden" name="conta" value="3" />
Ano: <input type="number" name="ano" size="4" maxlength="4" value="<?php echo $ano_hoje?>" />
<br><br><br><br><center>
<input type="submit" class="input" value="Exportar" />
</form>
</td>
<td style="font-size:14px">
<b>
<center>Exclusões de movimentos</b><br>
Listar exclusões desta conta.</center>
<form method="post" action="./exportar/exclusoes.php">
<input type="hidden" name="nome" value="Cartão de crédito Master" />
<input type="hidden" name="conta" value="3" />
<br><br><br><br><br><br><center><input type="submit" class="input" value="Exportar" />
</form>
</td>
</tr>
</table>

<br />

<table cellpadding="5" cellspacing="0" width="1000" align="center">
<tr>
<td colspan="3">
    <div style="float:right; text-align:right">
<form name="form_filtro_cat" method="get" action=""  >
<input type="hidden" name="mes" value="<?php echo $mes_hoje?>" >
<input type="hidden" name="ano" value="<?php echo $ano_hoje?>" >
    <b>Filtrar por categoria:</b>  <select name="filtro_cat" onchange="form_filtro_cat.submit()">
<option value="">Tudo</option>
<?php
$qr=mysqli_query($_SG['conexao'], "SELECT DISTINCT c.id, c.nome FROM categorias c, movimentos m WHERE m.cat=c.id && c.usuario='$usuario' && m.mes=$mes_hoje && m.ano=$ano_hoje && m.conta=3 ORDER BY c.nome");
while ($row=mysqli_fetch_array($qr)){
?>
<option <?php if (isset($_GET['filtro_cat']) && $_GET['filtro_cat']==$row['id'])echo "selected=selected"?> value="<?php echo $row['id']?>"><?php echo $row['nome']?></option>
<?php }?>
</select>
</form>
    </div>

<h2>Movimentos deste mês</h2>

</tr>
<tr style="background-color:#E0E0E0">
<td align="center" width="15"><b><?php echo "Dia"?></td>
<td><b><?php echo "Descrição e categoria"?></td>
<td align="center"><b><?php echo "Valor"?></td>
</tr>
<?php
$filtros="";
if (isset($_GET['filtro_cat'])){
	if ($_GET['filtro_cat']!=''){	
		$filtros="&& cat='".$_GET['filtro_cat']."'";
                
                $qr=mysqli_query($_SG['conexao'], "SELECT SUM(valor) as total FROM movimentos WHERE tipo=1 && conta=3 && usuario='$usuario' && mes='$mes_hoje' && ano='$ano_hoje' $filtros");
                $row=mysqli_fetch_array($qr);
                $entradas=$row['total'];

                $qr=mysqli_query($_SG['conexao'], "SELECT SUM(valor) as total FROM movimentos WHERE tipo=0 && conta=3 && usuario='$usuario' && mes='$mes_hoje' && ano='$ano_hoje' $filtros");
                $row=mysqli_fetch_array($qr);
                $saidas=$row['total'];

                $resultado_mes=$entradas-$saidas;
                
        }
}

$qr=mysqli_query($_SG['conexao'], "SELECT * FROM movimentos WHERE conta=3 && usuario='$usuario' && mes='$mes_hoje' && ano='$ano_hoje' $filtros ORDER By dia");
$cont=0;
while ($row=mysqli_fetch_array($qr)){
$cont++;

$cat=$row['cat'];
$qr2=mysqli_query($_SG['conexao'], "SELECT nome FROM categorias WHERE id='$cat'");
$row2=mysqli_fetch_array($qr2);
$categoria=$row2['nome'];

$comprovante=$row['comp_img']
?>
<script>
$(function () {
	$.calculator.setDefaults({showOn: 'both', buttonImageOnly: true, buttonImage: './conf/img/calc.png'});
	$("#<?php echo $row['id']?>").calculator(); //Calculadora comum para edição de movimentos
});
</script>
<tr style="background-color:<?php if ($cont%2==0) echo "#F1F1F1"; else echo "#E0E0E0"?>" >
<td align="center" width="15"><?php echo $row['dia']?></td>
<td><?php echo $row['descricao']?> <?php $parcelas=$row['parcelas']; $nparcelas=$row['nparcela']; if($parcelas>=2) echo "Parcela ".$nparcelas."/".$parcelas."."?> <em>(<a href="?mes=<?php echo $mes_hoje?>&ano=<?php echo $ano_hoje?>&filtro_cat=<?php echo $cat?>"><?php echo $categoria?></a>)</em><?php if (empty($comprovante)) echo ""; else echo "<a href=./upload_temp/download.php?id=$comprovante style=font-size:12px> [Comprovante]</a>"?> <a href="javascript:;" style="font-size:12px; color:#666" onclick="abreFecha('editar_mov_<?php echo $row['id']?>');" title="Editar">[editar]</a> <a href="javascript:;" style="font-size:12px; color:#666" onclick="abreFecha('hist_mov_<?php echo $row['id']?>');" title="Ver histórico"> [Histórico]</a><br>
</td>
<td align="right"><strong style="color:<?php if ($row['tipo']==0) echo "#C00"; else echo "rgba(4, 45, 191, 1)"?>"><?php echo formata_dinheiro($row['valor'])?></strong></td>
</tr>
    <tr style="display:none; background-color:<?php if ($cont%2==0) echo "#F1F1F1"; else echo "#E0E0E0"?>" id="editar_mov_<?php echo $row['id']?>">
        <td colspan="3">
            <hr/>
            <form enctype="multipart/form-data" method="post" action="?mes=<?php echo $mes_hoje?>&ano=<?php echo $ano_hoje?>">
            <input type="hidden" name="acao" value="editar_mov" />
            <input type="hidden" name="id" value="<?php echo $row['id']?>" />
                        
            <b>Dia:</b> <input type="text" name="dia" size="2" maxlength="2" value="<?php echo $row['dia']?>" />&nbsp;|&nbsp;
            <b>Mês:</b> <input type="text" name="mes" size="2" maxlength="2" value="<?php echo $row['mes']?>" />&nbsp;|&nbsp;
            <b>Ano:</b> <input type="text" name="ano" size="3" maxlength="4" value="<?php echo $row['ano']?>" />&nbsp;|&nbsp;
            <b>Tipo:</b> <label for="tipo_receita<?php echo $row['id']?>" style="color:rgba(4, 45, 191, 1)"><input <?php if($row['tipo']==1) echo "checked=checked"?> type="radio" name="tipo" value="1" id="tipo_receita<?php echo $row['id']?>" /> Receita</label>&nbsp; <label for="tipo_despesa<?php echo $row['id']?>" style="color:#C00"><input <?php if($row['tipo']==0) echo "checked=checked"?> type="radio" name="tipo" value="0" id="tipo_despesa<?php echo $row['id']?>" /> Despesa</label>&nbsp;&nbsp;&nbsp;|&nbsp;
            <b>Categoria:</b>
<select name="cat">
<?php
$qr2=mysqli_query($_SG['conexao'], "SELECT * FROM categorias where usuario='$usuario' ORDER BY nome");
while ($row2=mysqli_fetch_array($qr2)){
?>
    <option <?php if($row2['id']==$row['cat']) echo "selected"?> value="<?php echo $row2['id']?>"><?php echo $row2['nome']?></option>
<?php }?>
</select><br /><br />            
            <b>Descricao:</b> <input type="text" name="descricao" value="<?php echo $row['descricao']?>" size="90" maxlength="255" />
         <br /><br />
            <b>Valor:</b> R$  <input type=text id="<?php echo $row['id']?>" value="<?php echo $row['valor']?>" name=valor length=15 onKeyPress="return(FormataReais(this,'.',',',event))">
			&nbsp; | &nbsp;<b>Conta:</b><input type="radio" name="conta" value="1" <?php if($row['conta']==1) echo "checked"?> /> Conta Corrente &nbsp;<input type="radio" name="conta" value="2" <?php if($row['conta']==2) echo "checked"?> />Cartão Visa &nbsp;<input type="radio" name="conta" value="3" <?php if($row['conta']==3) echo "checked"?> />Cartão Master
			<br><br>
			<strong>Comprovante: </strong><input id="file" name="file" type="file"/> &nbsp;  |  &nbsp;             
            <input type="submit" class="input" value="Gravar" />
            </form> 
            <div style="text-align: right">
            <a style="color:#FF0000" onclick="return confirm('Tem certeza que deseja apagar?')" href="?mes=<?php echo $mes_hoje?>&ano=<?php echo $ano_hoje?>&acao=apagar&id=<?php echo $row['id']?>" title="Remover">[remover]</a> 
            </div>
            <hr/>
        </td>
    </tr>
<tr style="display:none; background-color:<?php if ($cont%2==0) echo "#F1F1F1"; else echo "#E0E0E0"?>" id="hist_mov_<?php echo $row['id']?>">
<td align="center" width="15"></td>
<td>
<?php
$id=$row['id'];
$hist=mysqli_query($_SG['conexao'], "SELECT * FROM historico WHERE id_mov = '$id' ORDER BY id");
$qrhist=mysqli_query($_SG['conexao'], "SELECT j.just, h.data, h.id FROM (just_ed j INNER JOIN historico h ON j.id = h.just_id) INNER JOIN movimentos g ON h.id_mov = g.id && g.id = '$id' ORDER BY h.id");

if (mysqli_num_rows($hist)!==0){
echo "Histórico de alterações:" ."<br>";
while ($rowhist = mysqli_fetch_array($qrhist)){
echo date('d/m/y', strtotime($rowhist['data'])) ."  -  " .$rowhist['just'] ."<br>";
}
}
else{
echo "Não há histórico de alterações.";
}
?>
</td>
<td></td>
</tr>
   
<?php
}
?>
<tr>
<td colspan="3" align="right">
<strong style="font-size:22px; color:C00"><?php echo formata_dinheiro($resultado_mes)?></strong>
</td>
</tr>
</table>

<table cellpadding="5" cellspacing="0" width="1000" align="center">
<tr>
<td align="right">
<hr size="1" />
<?php echo $versao?>  
</td>
</tr>
</table>
</body>
</html>
