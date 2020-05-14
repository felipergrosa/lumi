-- Adminer 4.7.6 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

CREATE DATABASE `nobre_luminarias` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;
USE `nobre_luminarias`;

DROP TABLE IF EXISTS `clientes`;
CREATE TABLE `clientes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) DEFAULT NULL,
  `razao_social` varchar(100) DEFAULT NULL,
  `cpfcnpj` varchar(16) DEFAULT NULL,
  `rgie` varchar(16) DEFAULT NULL,
  `suframa` varchar(30) DEFAULT NULL,
  `suframa_validade` date DEFAULT NULL,
  `desconto` varchar(10) DEFAULT NULL,
  `natureza_operacao` varchar(50) DEFAULT NULL,
  `email1` varchar(50) DEFAULT NULL,
  `email2` varchar(50) DEFAULT NULL,
  `tel1` varchar(20) DEFAULT NULL,
  `tel2` varchar(20) DEFAULT NULL,
  `fax` varchar(20) DEFAULT NULL,
  `site` varchar(100) DEFAULT NULL,
  `contato_compras` varchar(50) DEFAULT NULL,
  `contato_compras_fone` varchar(15) DEFAULT NULL,
  `contato_compras_email` varchar(50) DEFAULT NULL,
  `contato_cobranca` varchar(50) DEFAULT NULL,
  `contato_cobranca_fone` varchar(15) DEFAULT NULL,
  `contato_cobranca_email` varchar(50) DEFAULT NULL,
  `contato_cobranca_funcao` varchar(50) DEFAULT NULL,
  `grupo` int(11) DEFAULT NULL,
  `segmento_mercado` varchar(50) DEFAULT NULL,
  `regiao` int(11) DEFAULT NULL,
  `endereco` int(11) DEFAULT NULL,
  `endereco_cobranca` int(11) DEFAULT NULL,
  `endereco_cobranca_contato` varchar(50) DEFAULT NULL,
  `endereco_cobranca_contato_telefone` varchar(50) DEFAULT NULL,
  `endereco_cobranca_contato_email` varchar(50) DEFAULT NULL,
  `endereco_entrega` int(11) DEFAULT NULL,
  `endereco_entrega_cnpj` varchar(20) DEFAULT NULL,
  `endereco_entrega_ie` varchar(30) DEFAULT NULL,
  `endereco_entrega_contato` varchar(50) DEFAULT NULL,
  `endereco_entrega_contato_telefone` varchar(50) DEFAULT NULL,
  `endereco_entrega_contato_email` varchar(50) DEFAULT NULL,
  `restricao` int(11) NOT NULL DEFAULT 0,
  `restricao_texto` text DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `atividade` int(11) DEFAULT NULL,
  `site_user` varchar(50) DEFAULT NULL,
  `site_pass` varchar(50) DEFAULT NULL,
  `site_email_confirmado` int(11) DEFAULT NULL,
  `data_cadastro` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `endereco` (`endereco`),
  KEY `endereco_cobranca` (`endereco_cobranca`),
  KEY `endereco_entrega` (`endereco_entrega`),
  CONSTRAINT `clientes_ibfk_1` FOREIGN KEY (`endereco`) REFERENCES `dados_enderecos` (`id`),
  CONSTRAINT `clientes_ibfk_2` FOREIGN KEY (`endereco_cobranca`) REFERENCES `dados_enderecos` (`id`),
  CONSTRAINT `clientes_ibfk_3` FOREIGN KEY (`endereco_entrega`) REFERENCES `dados_enderecos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `clientes_empresas`;
CREATE TABLE `clientes_empresas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente` int(11) NOT NULL,
  `empresa` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cliente` (`cliente`),
  KEY `empresa` (`empresa`),
  CONSTRAINT `clientes_empresas_ibfk_1` FOREIGN KEY (`cliente`) REFERENCES `clientes` (`id`),
  CONSTRAINT `clientes_empresas_ibfk_2` FOREIGN KEY (`empresa`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `dados_enderecos`;
CREATE TABLE `dados_enderecos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cep` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL,
  `endereco` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `complemento` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bairro` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cidade` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` int(11) DEFAULT NULL,
  `numero` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `estado` (`estado`),
  CONSTRAINT `dados_enderecos_ibfk_1` FOREIGN KEY (`estado`) REFERENCES `dados_estados` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `dados_estados`;
CREATE TABLE `dados_estados` (
  `id` int(11) NOT NULL,
  `sigla` char(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nome` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  UNIQUE KEY `sigla_id` (`sigla`,`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

TRUNCATE `dados_estados`;
INSERT INTO `dados_estados` (`id`, `sigla`, `nome`) VALUES
(12,	'AC',	'Acre'),
(27,	'AL',	'Alagoas'),
(13,	'AM',	'Amazonas'),
(16,	'AP',	'Amapá'),
(29,	'BA',	'Bahia'),
(23,	'CE',	'Ceará'),
(53,	'DF',	'Distrito Federal'),
(32,	'ES',	'Espírito Santo'),
(52,	'GO',	'Goiás'),
(21,	'MA',	'Maranhão'),
(31,	'MG',	'Minas Gerais'),
(50,	'MS',	'Mato Grosso do Sul'),
(51,	'MT',	'Mato Grosso'),
(15,	'PA',	'Pará'),
(25,	'PB',	'Paraíba'),
(26,	'PE',	'Pernambuco'),
(22,	'PI',	'Piauí'),
(41,	'PR',	'Paraná'),
(33,	'RJ',	'Rio de Janeiro'),
(24,	'RN',	'Rio Grande do Norte'),
(11,	'RO',	'Rondônia'),
(14,	'RR',	'Roraima'),
(43,	'RS',	'Rio Grande do Sul'),
(42,	'SC',	'Santa Catarina'),
(28,	'SE',	'Sergipe'),
(35,	'SP',	'São Paulo'),
(17,	'TO',	'Tocantins');

DROP TABLE IF EXISTS `empresas`;
CREATE TABLE `empresas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `razao_social` varchar(100) DEFAULT NULL,
  `nome_fantasia` varchar(100) DEFAULT NULL,
  `data_fundacao` date DEFAULT NULL,
  `data_cadastro` date DEFAULT NULL,
  `capital_social` double DEFAULT NULL,
  `cpfcnpj` varchar(20) DEFAULT NULL,
  `ie` varchar(20) DEFAULT NULL,
  `fax` varchar(20) DEFAULT NULL,
  `descricao_atividade` varchar(50) DEFAULT NULL,
  `site` varchar(50) DEFAULT NULL,
  `contato1` varchar(30) DEFAULT NULL,
  `funcao1` varchar(50) DEFAULT NULL,
  `tel1` varchar(20) DEFAULT NULL,
  `email1` varchar(50) DEFAULT NULL,
  `contato2` varchar(50) DEFAULT NULL,
  `funcao2` varchar(50) DEFAULT NULL,
  `tel2` varchar(20) DEFAULT NULL,
  `email2` varchar(50) DEFAULT NULL,
  `endereco` varchar(100) DEFAULT NULL,
  `recebe_saldo_produto` int(11) DEFAULT NULL,
  `contrato_assinado` int(11) DEFAULT NULL,
  `data_assinatura` date DEFAULT NULL,
  `perfil` int(11) DEFAULT NULL,
  `indicador_tipo_atividade` int(11) DEFAULT NULL,
  `comissao_gerenciamento` double DEFAULT NULL,
  `data_pagamento` date DEFAULT NULL,
  `observacao` text DEFAULT NULL,
  `cartao_correio_n` varchar(50) DEFAULT NULL,
  `contrato_correio_n` varchar(50) DEFAULT NULL,
  `atividade` int(11) DEFAULT NULL,
  `site_user` varchar(50) DEFAULT NULL,
  `site_pass` varchar(50) DEFAULT NULL,
  `site_email_confirmado` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `pedidos`;
CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `representante` int(11) NOT NULL,
  `empresa` int(11) NOT NULL,
  `cliente` int(11) NOT NULL,
  `prioridade` int(11) NOT NULL,
  `frete` int(11) NOT NULL,
  `num_pedido_representante` int(11) NOT NULL,
  `data_pedido` date NOT NULL,
  `data_previsao_faturamento` date NOT NULL,
  `num_pedido_compra` int(11) NOT NULL,
  `tabela_preco` int(11) NOT NULL,
  `cond_pagto` int(11) NOT NULL,
  `transportadora` int(11) NOT NULL,
  `desconto_padrao` double NOT NULL,
  `desconto_adic1` double NOT NULL,
  `desconto_adic2` double NOT NULL,
  `desconto_adic3` double NOT NULL,
  `desconto_part_com` double NOT NULL,
  `natureza_operacao` int(11) NOT NULL,
  `observacao` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_cadastro` datetime NOT NULL,
  `usuario_cadastro` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `empresa` (`empresa`),
  KEY `cliente` (`cliente`),
  CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`empresa`) REFERENCES `empresas` (`id`),
  CONSTRAINT `pedidos_ibfk_2` FOREIGN KEY (`cliente`) REFERENCES `clientes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(50) NOT NULL,
  `pass` varchar(50) DEFAULT NULL,
  `nome` varchar(200) DEFAULT NULL,
  `cpfcnpj` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `general_level` varchar(5) DEFAULT NULL,
  `group` int(11) DEFAULT NULL,
  `chat` tinyint(1) DEFAULT NULL,
  `tipo_usuario` varchar(10) DEFAULT NULL,
  `atividade` tinyint(1) DEFAULT NULL,
  `force_pass` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login_UNIQUE` (`login`),
  KEY `usuarios_group_idx` (`group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `usuarios_acessos`;
CREATE TABLE `usuarios_acessos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(10) NOT NULL,
  `tela` varchar(200) NOT NULL,
  `usuario_permissao` varchar(10) DEFAULT NULL,
  `usuario_revoga` varchar(10) DEFAULT NULL,
  `data_permissao` datetime DEFAULT NULL,
  `data_revoga` datetime DEFAULT NULL,
  `status` varchar(5) DEFAULT NULL,
  `atalho` varchar(5) DEFAULT NULL,
  `atalho_sort` double DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `usuarios_acessos_complementar`;
CREATE TABLE `usuarios_acessos_complementar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` int(11) DEFAULT NULL,
  `tipo` varchar(45) NOT NULL,
  `valor` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `acessos_complementar_usuario_idx` (`usuario`),
  CONSTRAINT `acessos_complementar_usuario` FOREIGN KEY (`usuario`) REFERENCES `usuarios` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `usuarios_acessos_complementar_tab`;
CREATE TABLE `usuarios_acessos_complementar_tab` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `referencia` varchar(20) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `descricao` varchar(200) NOT NULL,
  `atividade` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `usuarios_atalhos`;
CREATE TABLE `usuarios_atalhos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` int(11) NOT NULL,
  `tela` varchar(200) NOT NULL,
  `sort` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario` (`usuario`),
  CONSTRAINT `usuarios_atalhos_ibfk_1` FOREIGN KEY (`usuario`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `usuarios_group`;
CREATE TABLE `usuarios_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `usuarios_group_acessos`;
CREATE TABLE `usuarios_group_acessos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group` int(11) DEFAULT NULL,
  `tela` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `atalho` varchar(5) DEFAULT NULL,
  `atalho_sort` decimal(10,0) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `acessos_tela_idx` (`tela`),
  KEY `acessos_group_idx` (`group`),
  CONSTRAINT `acessos_group` FOREIGN KEY (`group`) REFERENCES `usuarios_group` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `acessos_tela0` FOREIGN KEY (`tela`) REFERENCES `configuracoes_acessos_menus` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 2020-05-14 03:57:57
