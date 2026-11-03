-- Replace the placeholder before execution; do not use this sample password.
CREATE USER IF NOT EXISTS 'college_app'@'localhost' IDENTIFIED BY 'REPLACE_WITH_A_STRONG_LOCAL_PASSWORD';

GRANT SELECT, INSERT, UPDATE ON college_db.* TO 'college_app'@'localhost';
SHOW GRANTS FOR 'college_app'@'localhost';

-- This application account intentionally cannot CREATE, ALTER, DROP, or DELETE.
