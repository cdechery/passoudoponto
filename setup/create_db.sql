SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

DROP SCHEMA IF EXISTS `doacoes` ;
CREATE SCHEMA IF NOT EXISTS `doacoes` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci ;
USE `doacoes` ;

-- -----------------------------------------------------
-- Table `doacoes`.`usuario`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `doacoes`.`usuario` ;

CREATE  TABLE IF NOT EXISTS `doacoes`.`usuario` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `nome` VARCHAR(100) NOT NULL ,
  `sobrenome` VARCHAR(40) NULL ,
  `login` VARCHAR(20) NOT NULL ,
  `senha` VARCHAR(32) NOT NULL ,
  `email` VARCHAR(120) NOT NULL ,
  `sexo` CHAR(1) NULL,
  `data_nascimento` DATE NULL,
  `lat` FLOAT(10,6) NULL ,
  `lng` FLOAT(10,6) NULL ,
  `avatar` VARCHAR(100) NULL ,
  `data_cadastro` DATETIME NOT NULL ,
  `data_atualizacao` DATETIME NULL ,
  `tipo` CHAR(1) NOT NULL ,
  `fg_geral_email` CHAR(1) NOT NULL DEFAULT 'S' ,
  `fg_notif_int_email` CHAR(1) NOT NULL DEFAULT 'S' ,
  `fg_de_inst_email` CHAR(1) NOT NULL DEFAULT 'S' ,
  `fg_de_pessoa_email` CHAR(1) NOT NULL DEFAULT 'S' ,
  `lim_emails_item` SMALLINT NOT NULL DEFAULT 10,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `email_UNIQUE` (`email` ASC) ,
  UNIQUE INDEX `login_UNIQUE` (`login` ASC) 
 )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `doacoes`.`categoria`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `doacoes`.`categoria` ;

CREATE  TABLE IF NOT EXISTS `doacoes`.`categoria` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `nome` VARCHAR(45) NOT NULL ,
  `descricao` VARCHAR(200) NULL ,
  `icone` VARCHAR(50) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `doacoes`.`situacao`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `doacoes`.`situacao` ;

CREATE  TABLE IF NOT EXISTS `doacoes`.`situacao` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `descricao` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `doacoes`.`item`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `doacoes`.`item` ;

CREATE  TABLE IF NOT EXISTS `doacoes`.`item` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(70) NOT NULL ,
  `descricao` VARCHAR(250) NOT NULL ,
  `status` CHAR(1) NOT NULL ,
  `data_inclusao` DATETIME NOT NULL ,
  `data_doacao` DATETIME NULL ,
  `usuario_id` INT NOT NULL ,
  `categoria_id` INT NOT NULL ,
  `situacao_id` INT NOT NULL ,
  'qtd_emails' SMALLINT NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_item_doador_idx` (`usuario_id` ASC) ,
  INDEX `fk_item_categoria1_idx` (`categoria_id` ASC) ,
  INDEX `fk_item_situacao1_idx` (`situacao_id` ASC) )
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `doacoes`.`item_temp`
-- -----------------------------------------------------
CREATE TABLE `controle_notif_email` (
  `item_id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `fg_email_enviado` char(1) DEFAULT 'N',
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB;


-- -----------------------------------------------------
-- Table `doacoes`.`item_temp`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `doacoes`.`item_temp` ;

CREATE TABLE IF NOT EXISTS `doacoes`.`item_temp` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `data_criacao` DATETIME NOT NULL ,
  `usuario_id` INT NOT NULL ,
  PRIMARY KEY (`id`, `usuario_id`) ,
  INDEX `fk_item_usuario_idx` (`usuario_id` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `doacoes`.`imagem`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `doacoes`.`imagem` ;

CREATE  TABLE IF NOT EXISTS `doacoes`.`imagem` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `nome_arquivo` VARCHAR(50) NOT NULL ,
  `descricao` VARCHAR(40) NULL ,
  `item_id` INT NOT NULL ,
  `temp_item_id` INT NULL,
  PRIMARY KEY (`id`) ,
  INDEX `fk_imagem_item1_idx` (`item_id` ASC),
  INDEX `fk_imagem_item2_idx` (`temp_item_id` ASC) )
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `doacoes`.`imagem`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `doacoes`.`tmp_imagem_arquivos` ;

CREATE  TABLE IF NOT EXISTS `doacoes`.`tmp_imagem_arquivos` (
  `nome_arquivo` VARCHAR(50) NOT NULL
)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `doacoes`.`interesse`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `doacoes`.`interesse` ;

CREATE  TABLE IF NOT EXISTS `doacoes`.`interesse` (
  `categoria_id` INT NOT NULL AUTO_INCREMENT ,
  `usuario_id` INT NOT NULL ,
  `data_inclusao` DATETIME NOT NULL ,
  `fg_ativo` CHAR(1) NOT NULL DEFAULT 'S' ,
  `raio_busca` SMALLINT NOT NULL DEFAULT 5 ,
  PRIMARY KEY (`categoria_id`, `usuario_id`) ,
  INDEX `fk_categoria_has_pessoa_pessoa1_idx` (`usuario_id` ASC) ,
  INDEX `fk_categoria_has_pessoa_categoria1_idx` (`categoria_id` ASC) )
ENGINE = InnoDB;

USE `doacoes` ;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;