-- ISPfinance V1.0 Foundation Migration
-- Phase 1: RBAC, company, warehouse, audit foundation

CREATE TABLE IF NOT EXISTS roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL UNIQUE,
  description VARCHAR(255) DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS permissions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(100) NOT NULL UNIQUE,
  description VARCHAR(255) DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS role_permissions (
  role_id INT NOT NULL,
  permission_id INT NOT NULL,
  PRIMARY KEY(role_id, permission_id),
  FOREIGN KEY(role_id) REFERENCES roles(id),
  FOREIGN KEY(permission_id) REFERENCES permissions(id)
);

CREATE TABLE IF NOT EXISTS companies (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  address TEXT,
  phone VARCHAR(50),
  currency VARCHAR(10) DEFAULT 'IDR',
  timezone VARCHAR(50) DEFAULT 'Asia/Jakarta',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS branches (
  id INT AUTO_INCREMENT PRIMARY KEY,
  company_id INT NOT NULL,
  name VARCHAR(100) NOT NULL,
  address TEXT,
  status ENUM('active','inactive') DEFAULT 'active',
  FOREIGN KEY(company_id) REFERENCES companies(id)
);

CREATE TABLE IF NOT EXISTS warehouses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  branch_id INT NOT NULL,
  name VARCHAR(100) NOT NULL,
  address TEXT,
  status ENUM('active','inactive') DEFAULT 'active',
  FOREIGN KEY(branch_id) REFERENCES branches(id)
);

CREATE TABLE IF NOT EXISTS audit_logs (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  user_id INT DEFAULT NULL,
  module VARCHAR(100) NOT NULL,
  action VARCHAR(50) NOT NULL,
  reference_id VARCHAR(100) DEFAULT NULL,
  old_value JSON DEFAULT NULL,
  new_value JSON DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS document_sequences (
  id INT AUTO_INCREMENT PRIMARY KEY,
  document_type VARCHAR(50) UNIQUE,
  prefix VARCHAR(50),
  current_number INT DEFAULT 0
);

INSERT IGNORE INTO roles(name,description) VALUES
('direktur','Akses laporan dan persetujuan'),
('admin_keuangan','Mengelola transaksi dan billing'),
('admin_gudang','Mengelola inventory');

INSERT IGNORE INTO permissions(code,description) VALUES
('dashboard.view','Melihat dashboard'),
('finance.manage','Mengelola keuangan'),
('billing.manage','Mengelola tagihan'),
('inventory.manage','Mengelola inventory'),
('report.view','Melihat laporan'),
('settings.manage','Mengelola setting');
