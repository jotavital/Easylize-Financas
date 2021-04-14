-- MySQL Script generated by MySQL Workbench
-- Wed Apr 14 17:57:40 2021
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema easylize_financas
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `easylize_financas` ;

-- -----------------------------------------------------
-- Schema easylize_financas
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `easylize_financas` DEFAULT CHARACTER SET utf8mb4 ;
USE `easylize_financas` ;

-- -----------------------------------------------------
-- Table `easylize_financas`.`usuario`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `easylize_financas`.`usuario` ;

CREATE TABLE IF NOT EXISTS `easylize_financas`.`usuario` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(255) NOT NULL,
  `sobrenome` VARCHAR(255) NOT NULL,
  `nome_usuario` VARCHAR(45) NOT NULL,
  `email` VARCHAR(200) NOT NULL,
  `senha` VARCHAR(100) NOT NULL,
  `prazo_aviso` INT(2) NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC) VISIBLE);


-- -----------------------------------------------------
-- Table `easylize_financas`.`despesa`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `easylize_financas`.`despesa` ;

CREATE TABLE IF NOT EXISTS `easylize_financas`.`despesa` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome_despesa` VARCHAR(255) NOT NULL,
  `descricao_despesa` VARCHAR(255) NOT NULL,
  `imagem` BLOB NOT NULL,
  `data_despesa` DATE NOT NULL,
  `data_vencimento` DATE NOT NULL,
  `valor` FLOAT NOT NULL,
  `fk_usuario` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `despesa_fk0` (`fk_usuario` ASC) VISIBLE,
  CONSTRAINT `despesa_fk0`
    FOREIGN KEY (`fk_usuario`)
    REFERENCES `easylize_financas`.`usuario` (`id`));


-- -----------------------------------------------------
-- Table `easylize_financas`.`categoria`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `easylize_financas`.`categoria` ;

CREATE TABLE IF NOT EXISTS `easylize_financas`.`categoria` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome_categoria` VARCHAR(255) NOT NULL,
  `descricao_categoria` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`));


-- -----------------------------------------------------
-- Table `easylize_financas`.`categoria_despesa`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `easylize_financas`.`categoria_despesa` ;

CREATE TABLE IF NOT EXISTS `easylize_financas`.`categoria_despesa` (
  `fk_categoria` INT NOT NULL,
  `fk_despesa` INT NOT NULL,
  PRIMARY KEY (`fk_categoria`, `fk_despesa`),
  INDEX `categoria_despesa_fk1` (`fk_despesa` ASC) VISIBLE,
  CONSTRAINT `categoria_despesa_fk0`
    FOREIGN KEY (`fk_categoria`)
    REFERENCES `easylize_financas`.`categoria` (`id`),
  CONSTRAINT `categoria_despesa_fk1`
    FOREIGN KEY (`fk_despesa`)
    REFERENCES `easylize_financas`.`despesa` (`id`));


-- -----------------------------------------------------
-- Table `easylize_financas`.`receita`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `easylize_financas`.`receita` ;

CREATE TABLE IF NOT EXISTS `easylize_financas`.`receita` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome_receita` VARCHAR(255) NOT NULL,
  `descricao_receita` VARCHAR(255) NOT NULL,
  `data_receita` DATE NOT NULL,
  `valor` FLOAT NOT NULL,
  `fk_usuario` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `receita_fk0` (`fk_usuario` ASC) VISIBLE,
  CONSTRAINT `receita_fk0`
    FOREIGN KEY (`fk_usuario`)
    REFERENCES `easylize_financas`.`usuario` (`id`));


-- -----------------------------------------------------
-- Table `easylize_financas`.`meta`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `easylize_financas`.`meta` ;

CREATE TABLE IF NOT EXISTS `easylize_financas`.`meta` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome_meta` VARCHAR(255) NOT NULL,
  `descricao_meta` VARCHAR(255) NOT NULL,
  `prazo_meta` DATE NOT NULL,
  `valor_total` FLOAT NOT NULL,
  `valor_atingido` FLOAT NOT NULL,
  `fk_usuario` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `meta_fk0` (`fk_usuario` ASC) VISIBLE,
  CONSTRAINT `meta_fk0`
    FOREIGN KEY (`fk_usuario`)
    REFERENCES `easylize_financas`.`usuario` (`id`));


-- -----------------------------------------------------
-- Table `easylize_financas`.`economia`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `easylize_financas`.`economia` ;

CREATE TABLE IF NOT EXISTS `easylize_financas`.`economia` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome_economia` VARCHAR(255) NOT NULL,
  `descricao_economia` VARCHAR(255) NOT NULL,
  `valor` FLOAT NOT NULL,
  `fk_usuario` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `economia_fk0` (`fk_usuario` ASC) VISIBLE,
  CONSTRAINT `economia_fk0`
    FOREIGN KEY (`fk_usuario`)
    REFERENCES `easylize_financas`.`usuario` (`id`));


-- -----------------------------------------------------
-- Table `easylize_financas`.`categoria_receita`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `easylize_financas`.`categoria_receita` ;

CREATE TABLE IF NOT EXISTS `easylize_financas`.`categoria_receita` (
  `fk_categoria` INT NOT NULL,
  `fk_receita` INT NOT NULL,
  PRIMARY KEY (`fk_categoria`),
  INDEX `categoria_receita_fk1` (`fk_receita` ASC) VISIBLE,
  CONSTRAINT `categoria_receita_fk0`
    FOREIGN KEY (`fk_categoria`)
    REFERENCES `easylize_financas`.`categoria` (`id`),
  CONSTRAINT `categoria_receita_fk1`
    FOREIGN KEY (`fk_receita`)
    REFERENCES `easylize_financas`.`receita` (`id`));


-- -----------------------------------------------------
-- Table `easylize_financas`.`categoria_economia`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `easylize_financas`.`categoria_economia` ;

CREATE TABLE IF NOT EXISTS `easylize_financas`.`categoria_economia` (
  `fk_categoria` INT NOT NULL,
  `fk_economia` INT NOT NULL,
  PRIMARY KEY (`fk_categoria`),
  INDEX `categoria_economia_fk1` (`fk_economia` ASC) VISIBLE,
  CONSTRAINT `categoria_economia_fk0`
    FOREIGN KEY (`fk_categoria`)
    REFERENCES `easylize_financas`.`categoria` (`id`),
  CONSTRAINT `categoria_economia_fk1`
    FOREIGN KEY (`fk_economia`)
    REFERENCES `easylize_financas`.`economia` (`id`));


-- -----------------------------------------------------
-- Table `easylize_financas`.`categoria_meta`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `easylize_financas`.`categoria_meta` ;

CREATE TABLE IF NOT EXISTS `easylize_financas`.`categoria_meta` (
  `fk_categoria` INT NOT NULL,
  `fk_meta` INT NOT NULL,
  PRIMARY KEY (`fk_categoria`),
  INDEX `categoria_meta_fk1` (`fk_meta` ASC) VISIBLE,
  CONSTRAINT `categoria_meta_fk0`
    FOREIGN KEY (`fk_categoria`)
    REFERENCES `easylize_financas`.`categoria` (`id`),
  CONSTRAINT `categoria_meta_fk1`
    FOREIGN KEY (`fk_meta`)
    REFERENCES `easylize_financas`.`meta` (`id`));


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;