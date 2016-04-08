<?php
include("./conf/config.php");
protegePagina();
$usuario=$_SESSION['usuarioID'];

$qr=mysql_query("SELECT * FROM usuarios where id='$usuario'");
$row=mysql_fetch_array($qr);
$n=$row['n_acesso_f'];
$n_acesso=$n+1;
mysql_query("UPDATE usuarios SET n_acesso_f='$n_acesso' WHERE id='$usuario'");

$data = date("YmdHis");
mysql_query("UPDATE usuarios SET ultimavisita='$data' WHERE id='$usuario'");
echo mysql_error();

@session_start();
session_destroy(); // Destrói a sessão limpando todos os valores salvos
session_unset(); // Limpa as variavéis globais da seção

header( 'Location: login.php' ) ;
?>