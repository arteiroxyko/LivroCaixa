USE livrocaixa;

ALTER TABLE `usuarios`
ADD `ultimavisita` datetime NOT NULL AFTER `data`;

ALTER TABLE `usuarios`
ADD `n_acesso_f` int(11) NOT NULL AFTER `ultimavisita`;

ALTER TABLE `usuarios`
CHANGE `senha` `senha` varchar(150) COLLATE 'utf8_general_ci' NOT NULL AFTER `usuario`,
CHANGE `ultimavisita` `ultimavisita` datetime NULL AFTER `data`,
CHANGE `n_acesso_f` `n_acesso_f` int(11) NOT NULL DEFAULT '0' AFTER `ultimavisita`,
CHANGE `visa` `visa` decimal(12,2) NOT NULL DEFAULT '0' AFTER `n_acesso_f`,
CHANGE `master` `master` decimal(12,2) NOT NULL DEFAULT '0' AFTER `visa`;

ALTER TABLE `usuarios`
ADD `dia_venc_v` int(11) NULL AFTER `visa`,
ADD `dia_venc_m` int(11) NULL;

INSERT INTO `just_ed` (`just`) VALUES ('Conta.');

ALTER TABLE `historico`
CHANGE `id_hist` `id` int(11) NOT NULL AUTO_INCREMENT FIRST;