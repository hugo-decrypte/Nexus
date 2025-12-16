DROP TABLE IF EXISTS logs;
DROP TABLE IF EXISTS transactions;
DROP TABLE IF EXISTS utilisateurs;


CREATE TABLE utilisateurs (
      id VARCHAR(50) AUTO_INCREMENT PRIMARY KEY,
      nom VARCHAR(50) NOT NULL,
      prenom VARCHAR(50) NOT NULL,
      email VARCHAR(100) UNIQUE NOT NULL,
      mot_de_passe VARCHAR(255) NOT NULL,
      role ENUM('client', 'commercant', 'admin') DEFAULT 'client',
      date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE transactions (
      id VARCHAR(50) AUTO_INCREMENT PRIMARY KEY,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      emetteur_id INT NULL,
      recepteur_id INT NULL,
      montant DECIMAL(10,2) NOT NULL,
      hash CHAR(64) NOT NULL UNIQUE,
      FOREIGN KEY (emetteur_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE SET NULL,
      FOREIGN KEY (recepteur_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE SET NULL,
      INDEX (created_at)
);

CREATE TABLE logs (
      id VARCHAR(50) AUTO_INCREMENT PRIMARY KEY,
      acteur_id INT,
      action_type VARCHAR(100) NOT NULL,
      details JSON,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (acteur_id) REFERENCES utilisateurs(id_utilisateur)
);
