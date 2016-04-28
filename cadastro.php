<?php
include("./conf/config.php");

if (isset($_POST['acao']) && $_POST['acao'] == 'cadastrar') {

	$usuario = $_POST['usuario'];
	$nome = $_POST['nome'];
	$sobrenome = $_POST['sobrenome'];
	$senha = $_POST['senha'];
	$senhaconf = $_POST['senhaconf'];
	$datacad = date("Ymd");
	
if (empty($usuario)){ 
echo "<script>
alert('O campo Usuário é obrigatório, por favor tente novamente.'); location.href='index.php'; historico.go(-1);
</script>";
exit();
}

if ($senha!=$senhaconf){
	echo "<script>
	alert('As senhas não conferem, por favor tente novamente.'); location.href='login.php'; historico.go(-1);
	</script>";
	exit();
}

$qr=mysqli_query($_SG['conexao'], "SELECT id, nome FROM usuarios where usuario='$usuario'");
$linhas=mysqli_num_rows($qr);
	
if ($linhas!=0){
echo "<script>
alert('Este usuário já existe, por favor tente novamente.'); location.href='login.php'; historico.go(-1);
</script>";
exit();	
}

mysqli_query($_SG['conexao'], "INSERT INTO usuarios (nome, sobrenome, usuario, senha, data) VALUES ('$nome', '$sobrenome', '$usuario', SHA2('$senha',512), '$datacad')");
echo mysqli_error($_SG['conexao']);
echo "<script>
alert('Usuário cadastrado com sucesso.'); location.href='login.php'; historico.go(-1);
</script>";
exit();

}


if (isset($_POST['acao']) && $_POST['acao'] == 'alterar_senha') {
	
	$pagina = $_POST['pagina'];
	$usuario = $_POST['usuario'];
	$novasenha = $_POST['novasenha'];
	$novasenhaconf = $_POST['novasenhaconf'];
	
if ($novasenha!=$novasenhaconf){
	echo "<script>
	alert('As senhas não conferem, por favor tente novamente.'); location.href='$pagina'; historico.go(-1);
	</script>";
	exit();
}

mysqli_query($_SG['conexao'], "UPDATE usuarios SET senha=SHA2('$novasenha',512) WHERE id='$usuario'");
echo mysqli_error($_SG['conexao']);
echo "<script>
alert('Senha alterada com sucesso.'); location.href='$pagina'; historico.go(-1);
</script>";
exit();	
	
}

?>