
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
    investor_id int NOT NULL,
    quantity INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(company_id),
    FOREIGN KEY (investor_id) REFERENCES investors(investor_id)
    ) ENGINE InnoDB;


INSERT INTO tokens (company_id, investor_id, quantity)
VALUES
(1,  1,         100),
(1,  2,      50),
(1,  3,      90),
(2,  1,         70),
(2,  2,      45),
(2,  3,      65);


SELECT companies.company_name, tokens.quantity
  FROM tokens 
  JOIN companies ON tokens.company_id = companies.company_id
  JOIN investors ON tokens.investor_id = investors.investor_id
  WHERE investors.investor_name="birba";


  SELECT investors.investor_name, tokens.quantity
  FROM tokens 
  JOIN investors ON tokens.investor_id = investors.investor_id
  JOIN companies ON tokens.company_id = companies.company_id
  WHERE companies.company_name="Necsto";