DROP TABLE IF EXISTS logs;
DROP TABLE IF EXISTS transactions;
DROP TABLE IF EXISTS utilisateurs;

CREATE TYPE user_role AS ENUM ('client', 'commercant', 'admin');

CREATE TABLE utilisateurs (
      id UUID PRIMARY KEY,
      nom VARCHAR(50) NOT NULL,
      prenom VARCHAR(50) NOT NULL,
      email VARCHAR(100) UNIQUE NOT NULL,
      mot_de_passe VARCHAR(255) NOT NULL,
      role user_role DEFAULT 'client',
      date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE transactions (
      id UUID PRIMARY KEY,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      emetteur_id VARCHAR(50) NULL,
      recepteur_id VARCHAR(50) NULL,
      montant DECIMAL(10,2) NOT NULL,
      hash CHAR(64) NOT NULL UNIQUE,
      FOREIGN KEY (emetteur_id) REFERENCES utilisateurs(id) ON DELETE SET NULL,
      FOREIGN KEY (recepteur_id) REFERENCES utilisateurs(id) ON DELETE SET NULL
);

CREATE TABLE logs (
      id UUID PRIMARY KEY,
      acteur_id UUID,
      action_type VARCHAR(100) NOT NULL,
      details JSON,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (acteur_id) REFERENCES utilisateurs(id)
);

CREATE INDEX idx_logs_created_at ON logs(created_at);
