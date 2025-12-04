CREATE DATABASE student_election;
USE student_election;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(50) NOT NULL,
    fullname VARCHAR(200) NOT NULL,
    contact_no VARCHAR(20) NOT NULL,
    section VARCHAR(100) NOT NULL,
    email VARCHAR(200) NOT NULL UNIQUE,
    course ENUM('CBAM','BEED','PADC') NOT NULL,
    club VARCHAR(255) NOT NULL, -- stores 1-3 clubs as comma-separated values
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','voter') NOT NULL DEFAULT 'voter',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE candidates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    position VARCHAR(100) NOT NULL,
    votes INT DEFAULT 0
);


-- vote_details table (one row per position selected)
CREATE TABLE vote_details (
  id INT AUTO_INCREMENT PRIMARY KEY,
  vote_id INT NOT NULL,
  position VARCHAR(100),
  candidate_id INT,
  FOREIGN KEY (vote_id) REFERENCES votes(id),
  FOREIGN KEY (candidate_id) REFERENCES candidates(id)
);

ALTER TABLE candidates
ADD COLUMN election_type VARCHAR(50) NOT NULL;

ALTER TABLE candidates
ADD COLUMN partylist VARCHAR(100) AFTER position;


ALTER TABLE users ADD username VARCHAR(50) NOT NULL AFTER name;

CREATE TABLE `announcements` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  `category` VARCHAR(50) NOT NULL,
  `target_audience` VARCHAR(100) DEFAULT 'All',
  `start_date` DATE NOT NULL,
  `end_date` DATE DEFAULT NULL,
  `posted_by` VARCHAR(50) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE votes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(50) NOT NULL,
    candidate_id INT NOT NULL,
    date_voted TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (candidate_id) REFERENCES candidates(id)
);






