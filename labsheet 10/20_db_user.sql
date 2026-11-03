-- Question 20: Create a limited-privilege MySQL user for the application and grant only the required permissions instead of using root.
-- Run as a database administrator, substitute a unique password, and keep it out of source control.
CREATE USER 'webapp_user'@'localhost' IDENTIFIED BY 'REPLACE_WITH_A_LONG_RANDOM_PASSWORD';
GRANT SELECT, INSERT, UPDATE, DELETE ON webapp_db.* TO 'webapp_user'@'localhost';
SHOW GRANTS FOR 'webapp_user'@'localhost';
