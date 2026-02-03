-- =========================
-- UTILISATEURS
-- =========================
INSERT INTO utilisateurs (id, nom, prenom, email, mot_de_passe, role)
VALUES
('a6fbb748-d840-4a8f-90bb-5f74f3425988', 'Dupont', 'Jean', 'jean.dupont@mail.com', '$2a$10$xztcHWAdFuDIl79VqLqVreXIwss7FpwGjlQircBo931SqJQmHBAte', 'client'),
('26c23c74-1296-45b5-aab8-a73606fb16d7', 'Martin', 'Alice', 'alice.martin@mail.com', '$2a$10$xztcHWAdFuDIl79VqLqVredG.PgH34himoHS4/JJ1djIR/5NBMh0i', 'commercant'),
('6e83face-1464-4b60-a8f8-41e7a2550b68', 'Admin', 'Super', 'admin@mail.com', '$2a$10$xztcHWAdFuDIl79VqLqVrelQNpUqyxnFTLrGucetCY.uOv4shCNsm', 'admin');

-- =========================
-- TRANSACTIONS
-- =========================
INSERT INTO transactions (id, emetteur_id, recepteur_id, montant, hash)
VALUES
(
  'b24daee3-8bba-4ed7-ac18-55d07e1faee3',
  'a6fbb748-d840-4a8f-90bb-5f74f3425988',
  '26c23c74-1296-45b5-aab8-a73606fb16d7',
  49.99,
  '9a8b64bb1668aae447964e503998fe5e9686c4101556a33edd13d826aa0b69e3'
),
(
  'caf91a0f-9f2b-4f29-8430-fce7183ebc86',
  'a6fbb748-d840-4a8f-90bb-5f74f3425988',
  '26c23c74-1296-45b5-aab8-a73606fb16d7',
  120.00,
  '1f18df802162364679d7ea5019a68cc3e4ced6242ad247202b7912146c587222'
);

-- =========================
-- LOGS
-- =========================
INSERT INTO logs (id, acteur_id, action_type, details)
VALUES
(
  '5456e189-741f-4c1d-b165-d339eccc7529',
  'a6fbb748-d840-4a8f-90bb-5f74f3425988',
  'CREATION_TRANSACTION',
  '{"transaction_id":"aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa","montant":49.99}'
),
(
  'f2aa6bf1-a140-4ebd-9417-e6aa9549569b',
  '26c23c74-1296-45b5-aab8-a73606fb16d7',
  'RECEPTION_PAIEMENT',
  '{"transaction_id":"aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa"}'
),
(
  '8e2fa852-3c42-4043-ac65-56bc4e5e956d',
  '6e83face-1464-4b60-a8f8-41e7a2550b68',
  'CONNEXION_ADMIN',
  '{"ip":"127.0.0.1"}'
);
