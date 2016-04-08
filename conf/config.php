<?php
header('Content-Type: text/html; charset=utf-8');
/**
* Agradescimentos a: Thiago Belem. link http://thiagobelem.net/
* e-mail: contato@thiagobelem.net 
*/
// ==============================
// CONFIGURAÇÃO DO SCRIPT
// ==============================
// Alterne entre true e false de acordo com sua necessidade.
$_SG['conectaServidor'] = true;    // Abrir uma conexão com o servidor MySQL?
$_SG['abreSessao'] = true;         // Iniciar a sessão com um session_start()?
$_SG['caseSensitive'] = false;     // Usar case-sensitive onde 'thiago' é diferente de 'THIAGO' na tela de login?
$_SG['validaSempre'] = true;       // Evitar que, ao mudar os dados do usuário no banco de dados como a senha por exemplo, o mesmo contiue logado?

// Informe os dados para conexão com o seu banco de dados.
$_SG['servidor'] = 'localhost';    // Servidor MySQL
$_SG['usuario'] = 'root';          // Usuário MySQL
$_SG['senha'] = '';          // Senha MySQL
$_SG['banco'] = 'livrocaixa';         // Banco de dados MySQL
$_SG['paginaLogin'] = 'login.php'; // Página de login
$_SG['tabela'] = 'usuarios';       // Nome da tabela do db onde os usuários são cadastrados.

// ===============================
// NÃO EDITE A PARTIR DESTE PONTO
// ===============================

// Verifica se precisa fazer a conexão com o MySQL
if ($_SG['conectaServidor'] == true) {
  $_SG['link'] = mysql_connect($_SG['servidor'], $_SG['usuario'], $_SG['senha']) or die("MySQL: Não foi possível conectar-se ao servidor.");
  mysql_select_db($_SG['banco'], $_SG['link']) or die("MySQL: Não foi possível conectar-se ao banco de dados.");
  mysql_query("SET NAMES 'utf8'");
  mysql_query('SET character_set_connection=utf8');
  mysql_query('SET character_set_client=utf8');
  mysql_query('SET character_set_results=utf8');
}
// Verifica se precisa iniciar a sessão
if ($_SG['abreSessao'] == true)
  session_start();
/**
* Função que valida um usuário e senha
*
* @param string $usuario - O usuário a ser validado
* @param string $senha - A senha a ser validada
*
* @return bool - Se o usuário foi validado ou não (true/false)
*/
function validaUsuario($usuario, $senha) {
  global $_SG;
  $cS = ($_SG['caseSensitive']) ? 'BINARY' : '';
  // Usa a função addslashes para escapar as aspas
  $nusuario = addslashes($usuario);
  $nsenha = addslashes($senha);
  // Monta uma consulta SQL (query) para procurar um usuário compativel com os dados fornecidos na tela de login, e interpretar a criptografia SHA1.
  $sql = "SELECT `id`, `nome`, `sobrenome` FROM `".$_SG['tabela']."` WHERE ".$cS." `usuario` = '".$nusuario."' AND ".$cS." `senha` = SHA1('".$nsenha."') LIMIT 1";
  $query = mysql_query($sql);
  $resultado = mysql_fetch_assoc($query);
  // Verifica se encontrou algum registro
  if (empty($resultado)) {
    // Nenhum registro foi encontrado => o usuário é inválido
    return false;
  } else {
    // Definimos dois valores na sessão com os dados do usuário
    $_SESSION['usuarioID'] = $resultado['id']; // Pega o valor da coluna 'id do registro encontrado no MySQL
    $_SESSION['usuarioNome'] = $resultado['nome']; // Pega o valor da coluna 'nome' do registro encontrado no MySQL
    $_SESSION['usuarioSobrenome'] = $resultado['sobrenome']; // Pega o valor da coluna 'nome' do registro encontrado no MySQL
    // Verifica a opção se sempre validar o login
    if ($_SG['validaSempre'] == true) {
      // Definimos dois valores na sessão com os dados do login
      $_SESSION['usuarioLogin'] = $usuario;
      $_SESSION['usuarioSenha'] = $senha;
    }
    return true;
  }
}
/**
* Função que protege as páginas
*/
function protegePagina() {
  global $_SG;
  if (!isset($_SESSION['usuarioID']) OR !isset($_SESSION['usuarioNome'])) {
    // Não há usuário logado, manda pra página de login
    expulsaVisitante();
  } else {
    // Há usuário logado, verifica se precisa validar o login novamente
    if ($_SG['validaSempre'] == true) {
      // Verifica se os dados salvos na sessão batem com os dados do banco de dados
      if (!validaUsuario($_SESSION['usuarioLogin'], $_SESSION['usuarioSenha'])) {
        // Os dados não batem, manda pra tela de login
        expulsaVisitante();
      }
    }
  }
}
/**
* Função para expulsar um visitante
*/
function expulsaVisitante() {
  global $_SG;
  // Remove as variáveis da sessão (caso elas existam)
  unset($_SESSION['usuarioID'], $_SESSION['usuarioNome'], $_SESSION['usuarioLogin'], $_SESSION['usuarioSenha']);
  // Manda pra tela de login
  header("Location: ".$_SG['paginaLogin']);

}