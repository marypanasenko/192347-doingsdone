CREATE DATABASE done
DEFAULT CHARACTER SET utf8
DEFAULT COLLATE utf8_general_ci;

USE done;

CREATE TABLE users (
id INT AUTO_INCREMENT PRIMARY KEY,
date DATETIME,
email CHAR(50) NOT NULL,
user_name CHAR(50),
user_pass CHAR(50),
contacts TEXT
);

CREATE TABLE projects (
id INT AUTO_INCREMENT PRIMARY KEY,
project_name CHAR(30) NOT NULL,
user_id INT,
FOREIGN KEY (user_id) REFERENCES users(id)
);


CREATE TABLE  tasks (
id INT AUTO_INCREMENT PRIMARY KEY,
date_start DATETIME,
date_done DATETIME,
task_status BIT DEFAULT 0,
task_name CHAR (150) NOT NULL,
file CHAR (250),
task_deadline DATETIME,
user_id INT,
project_id INT,
FOREIGN KEY (user_id) REFERENCES users(id),
FOREIGN KEY (project_id) REFERENCES projects(id)
);

CREATE UNIQUE INDEX email ON users(email);

CREATE INDEX task_name ON tasks(task_name);



