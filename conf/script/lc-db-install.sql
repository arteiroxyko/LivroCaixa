-- Adminer 4.2.4 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP DATABASE IF EXISTS `livrocaixa`;
CREATE DATABASE `livrocaixa` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `livrocaixa`;

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) DEFAULT NULL,
  `usuario` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `comprovantes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comp` mediumblob NOT NULL,
  `nome` varchar(150) CHARACTER SET utf8 NOT NULL,
  `tipo` varchar(150) CHARACTER SET utf8 NOT NULL,
  `ext` varchar(15) CHARACTER SET utf8 NOT NULL,
  `tamanho` varchar(15) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


CREATE TABLE `exclusoes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_mov_exc` int(11) NOT NULL,
  `tipo_mov` int(11) NOT NULL,
  `valor_mov` decimal(12,2) NOT NULL,
  `cat_mov` int(11) NOT NULL,
  `conta_mov` int(11) NOT NULL,
  `data_exc` date NOT NULL,
  `desc_mov` longtext NOT NULL,
  `usuario_mov` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `historico` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_mov` int(11) NOT NULL,
  `just_id` int(11) NOT NULL,
  `conta_mov` int(11) NOT NULL,
  `data` date NOT NULL,
  `usuario` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `just_ed` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `just` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `just_ed` (`id`, `just`) VALUES
(1,	'Dia.'),
(2,	'Mês.'),
(3,	'Ano.'),
(4,	'Tipo.'),
(5,	'Categoria.'),
(6,	'Descrição.'),
(7,	'Valor.'),
(8,	'Conta.'),
(9,	'Comprovante.');

CREATE TABLE `movimentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dia` int(2) unsigned zerofill DEFAULT NULL,
  `mes` int(2) unsigned zerofill DEFAULT NULL,
  `ano` int(4) DEFAULT NULL,
  `tipo` int(1) DEFAULT NULL,
  `valor` decimal(12,2) DEFAULT NULL,
  `nparcela` int(2) DEFAULT NULL,
  `parcelas` int(2) DEFAULT NULL,
  `cat` int(11) DEFAULT NULL,
  `conta` int(1) DEFAULT NULL,
  `usuario` int(11) DEFAULT NULL,
  `descricao` longtext,
  `edicao` longtext,
  `comp_img` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `orcamento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mes` int(2) NOT NULL,
  `ano` int(4) NOT NULL,
  `valor` decimal(12,2) NOT NULL,
  `conta` int(1) NOT NULL,
  `usuario` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `usuarios` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `sobrenome` varchar(100) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `senha` varchar(150) NOT NULL,
  `data` date NOT NULL,
  `ultimavisita` datetime DEFAULT NULL,
  `n_acesso_f` int(11) NOT NULL DEFAULT '0',
  `visa` decimal(12,2) NOT NULL DEFAULT '0.00',
  `dia_venc_v` int(11) DEFAULT NULL,
  `master` decimal(12,2) NOT NULL DEFAULT '0.00',
  `dia_venc_m` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario` (`usuario`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- 2016-05-01 23:26:32
