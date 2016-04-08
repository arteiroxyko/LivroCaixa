<?php
session_start();
session_destroy(); // Destrói a sessão limpando todos os valores salvos
include("./conf/config.php");
include './conf/functions.php';
require_once './conf/versao.php';

// Formato 24 horas (de 1 a 24)
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
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title id='titulo'>Livro caixa</title>
		<link href="./conf/img/favicon.png" rel="icon" type="image/png"/>
        <meta name="LANGUAGE" content="Portuguese" />
        <meta name="AUDIENCE" content="all" />
        <meta name="RATING" content="GENERAL" />
		<link href="./conf/css/styles.css" rel="stylesheet" type="text/css" />
		<script LANGUAGE="JavaScript" src="./conf/js/scripts.js"></script>
		<script src="./conf/js/jquery.js"></script>
		<script LANGUAGE="JavaScript" src="./conf/js/jquery.validar.formulario.js"></script>
		<script>
		function showTimer() {
		var time=new Date();
		var hour=time.getHours();
		var minute=time.getMinutes();
		var second=time.getSeconds();
		if(hour<10)   hour  ="0"+hour;
		if(minute<10) minute="0"+minute;
		if(second<10) second="0"+second;
		var st=hour+":"+minute+":"+second;
		document.getElementById("timer").innerHTML=st; 
		}
		function initTimer() {

		setInterval(showTimer,1000);
		}
		</script>
		<script type="text/javascript">
			$(document).ready(function(){
				$('#formulario').validate({
					rules: {
						usuario: {
							required: true,
							minlength: 4
						},
						nome: {
							required: true
						},
						sobrenome: {
							required: true
						},
						senha: {
							required: true,
							minlength: 6
						},
						senhaconf: {
							required: true,
							equalTo: "#senha"
						},
					},
					messages: {
						usuario: {
							required: "Campo obrigatório.",
							minlength: "Mínimo 4 caracteres."
						},
						senha: {
							required: "Campo obrigatório.",
							minlength: "Mínimo 6 caracteres."
						},
						nome: {
							required: "Campo obrigatório."
						},
						sobrenome: {
							required: "Campo obrigatório."
						},
						senhaconf: {
							required: "Campo obrigatório.",
							equalTo: "Senhas não conferem."
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
    </head>
    <body style="padding:10px" onLoad="initTimer();">


        <table cellpadding="1" cellspacing="10"  width="900" align="center" style="background-color:#033">

            <tr>
                <td colspan="11" style="background-color:#005B5B;">
                    <h2 style="color:#FFF; margin:5px"><?php echo $mensagem ?>, seja bem vindo ao sistema Livro Caixa Simples.</h2>
                </td>
                <td colspan="2" align="right" style="background-color:#005B5B;">
                    <a style="color:#FFF" title="Este sistema deve ser utilizado apenas para fins pessoais, e não contábeis." href="?mes=<?php echo date('m') ?>&ano=<?php echo date('Y') ?>">Hoje:<strong> <?php echo date('d') ?> de <?php echo mostraMes(date('m')) ?> de <?php echo date('Y') ?><br><br><span id="timer"></span></strong></a>&nbsp; 
                </td>
            </tr>
        </table>
        <br />
        <br />
        <table cellpadding="1" cellspacing="10"  width="900" align="center" >

            <tr>
                <td colspan="11" align="center" >
                    <b>Faça login para acessar o sistema.</b>
                    <br><br><br>
                            <form method="post" action="valida.php">

                                Usuário: <input type='text' name='usuario'><br><br>
                                        Senha: <input type='password' name='senha'><br>
                                                <br>
                                                    <input type='submit' value='Entrar'>

                                                        </form>
														<a href="javascript:;" style="font-size:16px; color:#4169E1" onclick="abreFecha('cad_usuario');" title="Cadastrar"> Cadastrar usuario</a><br>
														    </td>
                                                            </tr>
                                                            </table>
															<table width="900" align="center">
															<tr style="display:none; background-color:#E0E0E0" id="cad_usuario">
															<td >
															<b>
															<center><br>Informe os dados do novo usuário.</center><br></b>
															<form id="formulario" method="post" action="cadastro.php">
															<input type="hidden" name="acao" value="cadastrar" />
															<b>Usuário:
															</b> <input type="text" name="usuario" id="usuario" size="10" maxlength="15" /><br><br>
															<b>Nome:</b> <input type="text" name="nome" id="nome" size="20" maxlength="100" />
															<b>Sobrenome:</b> <input type="text" name="sobrenome" id="sobrenome" size="40" maxlength="100" />
															<br><br>
															<b>Senha:</b> <input type="password" name="senha" id="senha" onkeyup="passwordStrength(this.value)"/>
															<b>Confirmar:</b> <input type="password" name="senhaconf" id="senhaconf">&nbsp;
															<br>
															<label for="passwordStrength"><font size=2>Força da senha</font></label><br>
															<div id="passwordDescription"></div>
															<div id="passwordStrength" class="strength0"></div>
															<center>
															<input type="submit" class="input" value="Cadastrar" />
															</form>
															</td>
															</tr>
															</table>
                                                            <table cellpadding="5" cellspacing="0" width="900" align="center">
                                                                <tr>
                                                                    <td align="right">
                                                                        <hr size="2" />
                                                                        <em><?php echo "$desenvolvedor $versao"?></em>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            </body>
                                                            </html>
