CREATE TABLE participants(

    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    event VARCHAR(100),
    attended BOOLEAN DEFAULT FALSE
);