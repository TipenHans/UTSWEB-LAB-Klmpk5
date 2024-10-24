CREATE TABLE users (
     id INT AUTO_INCREMENT PRIMARY KEY,
     username VARCHAR(50) NOT NULL,
     email VARCHAR(100) NOT NULL,
     password VARCHAR(255) NOT NULL
 );


CREATE TABLE todos (
     id INT AUTO_INCREMENT PRIMARY KEY,
     user_id INT,
     title VARCHAR(255) NOT NULL,
     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
     FOREIGN KEY (user_id) REFERENCES users(id)
 );


CREATE TABLE tasks (
     id INT AUTO_INCREMENT PRIMARY KEY,
     todo_id INT,
     task_name VARCHAR(255) NOT NULL,
     is_completed BOOLEAN DEFAULT 0,
     deadline DATETIME NOT NULL,
     description TEXT,
     FOREIGN KEY (todo_id) REFERENCES todos(id)
 );
