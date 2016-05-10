USE `livrocaixa`;

INSERT INTO just_ed (just) VALUES ('Comprovante.');

ALTER TABLE `movimentos`
ADD `comp_img` int(11) NULL;

CREATE TABLE `comprovantes` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `comp` mediumblob NOT NULL,
  `nome` varchar(150) COLLATE 'utf8_general_ci' NOT NULL,
  `tipo` varchar(150) COLLATE 'utf8_general_ci' NOT NULL,
  `ext` varchar(15) COLLATE 'utf8_general_ci' NOT NULL,
  `tamanho` varchar(15) COLLATE 'utf8_general_ci' NOT NULL
) ENGINE='MyISAM' COLLATE 'utf8_bin';