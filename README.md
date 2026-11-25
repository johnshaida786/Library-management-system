# Library Management System (PHP + MySQL)

Features: Books CRUD, Members CRUD, issue / return with fine calculation (₹5/day), dashboard, search, CSRF protection, PDO prepared statements.

## 1. Connect to your cloud SQL instance
```bash
mysql -h <CLOUD_HOST> -P 4000 -u <DB_USER> -p
# if the instance requires SSL:
mysql -h <CLOUD_HOST> -P 4000 -u <DB_USER> -p --ssl-ca=server-ca.pem
```

## 2. Create the database + tables and save the sample data
```bash
mysql -h <CLOUD_HOST> -u <DB_USER> -p < sql/schema.sql
```
Or inside the mysql prompt:
```sql
SOURCE sql/schema.sql;
USE library_db;
SHOW TABLES;
SELECT * FROM books;
```

## 3. Save (INSERT / UPDATE / DELETE) records manually
```sql
INSERT INTO books (isbn,title,author,category,total_copies,available_copies)
VALUES ('9780262033848','Introduction to Algorithms','CLRS','Computer Science',2,2);

UPDATE books SET total_copies = 5, available_copies = available_copies + 3 WHERE isbn = '9780262033848';

DELETE FROM books WHERE isbn = '9780262033848';
```

## 4. Back up / restore
```bash
mysqldump -h <CLOUD_HOST> -u <DB_USER> -p library_db > library_backup.sql
mysql     -h <CLOUD_HOST> -u <DB_USER> -p library_db < library_backup.sql
```

## 5. Point PHP at the cloud DB and run
```bash
export DB_HOST=<CLOUD_HOST> DB_PORT=3306 DB_NAME=library_db DB_USER=<DB_USER> DB_PASS='<PASSWORD>'
# export DB_SSL_CA=/path/to/server-ca.pem     # only if SSL is required
php -S localhost:8000
```
Open http://localhost:8000

(XAMPP/WAMP: copy the folder to `htdocs/library`, edit `config/config.php`, and set `BASE_URL` to `/library`.)

## Cloud checklist
- Allow your IP address in the instance's authorized networks / firewall.
- Enable the `pdo_mysql` extension in php.ini.
