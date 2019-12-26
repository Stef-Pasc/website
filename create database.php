
CREATE TABLE users (
    user_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    user_type VARCHAR(20) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP) ENGINE InnoDB;

CREATE TABLE companies (
    company_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    company_name VARCHAR(20) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (user_id)) ENGINE InnoDB;

CREATE TABLE investors (
    investor_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    investor_name VARCHAR(20) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (user_id)) ENGINE InnoDB;

CREATE TABLE tokens (
    token_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    company_id int NOT NULL,
    token_name VARCHAR(20),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(company_id)
    ) ENGINE InnoDB;

CREATE TABLE token_owners (
    token_id INT NOT NULL,
    token_owners_id INT NOT NULL,
    token_quantity INT NOT NULL,
    FOREIGN KEY (token_id) REFERENCES tokens(token_id),
    FOREIGN KEY (token_owners_id) REFERENCES owners(token_owners_id)
    ) ENGINE InnoDB;

CREATE TABLE owners (
    token_owners_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    investor_id INT NOT NULL,
    FOREIGN KEY (investor_id) REFERENCES investors(investor_id),
    FOREIGN KEY (company_id) REFERENCES companies(company_id)
    ) ENGINE InnoDB;
