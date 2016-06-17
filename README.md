
Livro Caixa Simples 

Gestão de finanças com este aplicativo simples.

Fonte original dos arquivos neste [site](http://www.paulocollares.com.br/2013/06/sistema-simples-de-livro-caixa-em-php/)




# Xyko Arteiro #

#Instalação básica:#

No servidor desconpactar em uma pasta ex.: c:\xampp\htdocs\livrocaixa  (htdocs ou www).
Criar BD mysql ou inportar o aquivo lc-db-install.sql no PHPAdmin!

Com um editor alterar no arquivo conf/config.php (+- linha 16) alterar para a sua configuração de acesso, ex.:

// Informe os dados para conexão com o seu banco de dados.
$_SG['servidor'] = 'localhost';    // Servidor MySQL
$_SG['usuario'] = 'root';          // Usuário MySQL
$_SG['senha'] = '';          // Senha MySQL
$_SG['banco'] = 'livrocaixa';        // Banco de dados MySQL
$_SG['paginaLogin'] = 'login.php'; // Página de login
$_SG['tabela'] = 'usuarios';       // Nome da tabela do db onde os usuários são cadastrados.

// ===============================

Após instalar acessar no browser http://localhost/livrocaixa

Efetuar o cadastro do usuário para o primeiro acesso e pronto.
