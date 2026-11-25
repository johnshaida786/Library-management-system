-- Library Management System : TiDB Cloud (MySQL-compatible) schema
CREATE DATABASE IF NOT EXISTS library_db;
USE library_db;

CREATE TABLE IF NOT EXISTS books (
  id               INT AUTO_INCREMENT PRIMARY KEY,
  isbn             VARCHAR(20)  NOT NULL,
  title            VARCHAR(200) NOT NULL,
  author           VARCHAR(150) NOT NULL,
  category         VARCHAR(80)  DEFAULT NULL,
  total_copies     INT NOT NULL DEFAULT 1,
  available_copies INT NOT NULL DEFAULT 1,
  created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_books_isbn (isbn),
  KEY idx_books_title (title)
);

CREATE TABLE IF NOT EXISTS members (
  id        INT AUTO_INCREMENT PRIMARY KEY,
  name      VARCHAR(120) NOT NULL,
  email     VARCHAR(150) NOT NULL,
  phone     VARCHAR(20)  DEFAULT NULL,
  status    ENUM('active','inactive') NOT NULL DEFAULT 'active',
  joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_members_email (email)
);

CREATE TABLE IF NOT EXISTS transactions (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  book_id     INT NOT NULL,
  member_id   INT NOT NULL,
  issue_date  DATE NOT NULL,
  due_date    DATE NOT NULL,
  return_date DATE DEFAULT NULL,
  fine        DECIMAL(8,2) NOT NULL DEFAULT 0.00,
  status      ENUM('issued','returned') NOT NULL DEFAULT 'issued',
  KEY idx_tx_book (book_id),
  KEY idx_tx_member (member_id),
  KEY idx_tx_status_due (status, due_date)
);

-- Sample data
INSERT IGNORE INTO books (isbn,title,author,category,total_copies,available_copies) VALUES
('9780132350884','Clean Code','Robert C. Martin','Programming',3,3),
('9780596517748','JavaScript: The Good Parts','Douglas Crockford','Programming',2,2),
('9780141439518','Pride and Prejudice','Jane Austen','Fiction',4,4),
('9780143127741','Wings of Fire','A.P.J. Abdul Kalam','Biography',2,2);

INSERT IGNORE INTO members (name,email,phone,status) VALUES
('Arun Kumar','arun@example.com','9876543210','active'),
('Priya Sharma','priya@example.com','9123456780','active');

-- Verify
SHOW TABLES;
SELECT COUNT(*) AS books FROM books;
SELECT COUNT(*) AS members FROM members;
