CREATE DATABASE IF NOT EXISTS `keuangan`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `keuangan`;

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_nama` VARCHAR(150) NOT NULL,
  `user_username` VARCHAR(100) NOT NULL,
  `user_password` VARCHAR(255) NOT NULL,
  `user_foto` VARCHAR(255) NOT NULL DEFAULT '',
  `user_level` ENUM('administrator', 'manajemen') NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `uk_user_username` (`user_username`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `kategori` (
  `kategori_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `kategori` VARCHAR(150) NOT NULL,
  PRIMARY KEY (`kategori_id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `bank` (
  `bank_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `bank_nama` VARCHAR(150) NOT NULL,
  `bank_pemilik` VARCHAR(150) NOT NULL DEFAULT '',
  `bank_nomor` VARCHAR(100) NOT NULL DEFAULT '',
  `bank_saldo` DECIMAL(18,2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`bank_id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `transaksi` (
  `transaksi_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `transaksi_tanggal` DATE NOT NULL,
  `transaksi_jenis` ENUM('Pemasukan', 'Pengeluaran') NOT NULL,
  `transaksi_kategori` INT UNSIGNED NOT NULL,
  `transaksi_nominal` DECIMAL(18,2) NOT NULL DEFAULT 0,
  `transaksi_keterangan` TEXT NOT NULL,
  `transaksi_foto` VARCHAR(255) NOT NULL DEFAULT '',
  `transaksi_bank` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`transaksi_id`),
  KEY `idx_transaksi_tanggal` (`transaksi_tanggal`),
  KEY `idx_transaksi_kategori` (`transaksi_kategori`),
  KEY `idx_transaksi_bank` (`transaksi_bank`),
  CONSTRAINT `fk_transaksi_kategori`
    FOREIGN KEY (`transaksi_kategori`) REFERENCES `kategori` (`kategori_id`),
  CONSTRAINT `fk_transaksi_bank`
    FOREIGN KEY (`transaksi_bank`) REFERENCES `bank` (`bank_id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `hutang` (
  `hutang_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `hutang_tanggal` DATE NOT NULL,
  `hutang_nominal` DECIMAL(18,2) NOT NULL DEFAULT 0,
  `hutang_keterangan` TEXT NOT NULL,
  PRIMARY KEY (`hutang_id`),
  KEY `idx_hutang_tanggal` (`hutang_tanggal`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `piutang` (
  `piutang_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `piutang_tanggal` DATE NOT NULL,
  `piutang_nominal` DECIMAL(18,2) NOT NULL DEFAULT 0,
  `piutang_keterangan` TEXT NOT NULL,
  PRIMARY KEY (`piutang_id`),
  KEY `idx_piutang_tanggal` (`piutang_tanggal`)
) ENGINE=InnoDB;

INSERT INTO `kategori` (`kategori_id`, `kategori`)
VALUES (1, 'Lain-lain')
ON DUPLICATE KEY UPDATE `kategori` = VALUES(`kategori`);

INSERT INTO `bank` (`bank_id`, `bank_nama`, `bank_pemilik`, `bank_nomor`, `bank_saldo`)
VALUES (1, 'Kas', '', '', 0)
ON DUPLICATE KEY UPDATE `bank_nama` = VALUES(`bank_nama`);
