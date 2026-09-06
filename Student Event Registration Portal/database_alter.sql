-- CampusConnect 2026 - Reference for your EXISTING database
-- Database: studentdb   Table: students (already created by you)

-- Your existing table (for reference - do NOT re-run this):
-- CREATE TABLE students (
--     Student_id   INT PRIMARY KEY AUTO_INCREMENT,
--     Full_Name    VARCHAR(20) NOT NULL,
--     email        VARCHAR(20) NOT NULL UNIQUE,
--     College_Name VARCHAR(25) NOT NULL,
--     Location     VARCHAR(20) NOT NULL,
--     Event        VARCHAR(20)
-- );

-- REQUIRED: your table has no Password column, but the practical exam
-- requires a password field for login. Run this once in your database:

USE studentdb;

ALTER TABLE students ADD COLUMN Password VARCHAR(255) NOT NULL;

-- VARCHAR(255) is used (not 20) so we can store a properly hashed
-- password instead of plain text - a hash needs 60+ characters.

-- Verification queries (for your SQL screenshots)
-- DESCRIBE students;
-- SELECT * FROM students;
