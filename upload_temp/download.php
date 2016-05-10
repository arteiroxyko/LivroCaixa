<?php
include("../conf/config.php"); // Inclui o arquivo com o sistema de segurança
protegePagina(); // Chama a função que protege a página
$id = $_GET['id'];


   $res = mysqli_query($_SG['conexao'], "select * from COMPROVANTES WHERE id=$id");
   $registro = mysqli_fetch_array($res);
   $conteudo_blob = $registro['comp'];
   $tipo= $registro['tipo'];
	$tipo2= $registro['ext'];
	$nome= $registro['nome'];



   //$registro = ibase_fetch_assoc($res, IBASE_TEXT);
   //$conteudo_blob = $registro["COMP_IMG"];
   //$img_blob = imagecreatefromstring($conteudo_blob);

   // as 2 linhas abaixo criam o arquivo, salvando-o em disco
   //$arq_destino = 'foto3.jpg';
   //imagejpeg($img_blob, $arq_destino)or die('Não foi possível criar o arquivo ' . $arq_destino . '.');

   // as 2 linhas abaixo exibem o arquivo no browser
   // para obter a imagem em outro arquivo <img src="este_programa.php">
    header('Content-Description: File Transfer');
	header('Content-Disposition: attachment; filename="'.$nome.'"');
	header("Content-type: ".$tipo."");
	header('Content-Transfer-Encoding: binary');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	header('Expires: 0');
    echo $conteudo_blob;

?>
