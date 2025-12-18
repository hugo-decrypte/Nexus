-- =========================
-- UTILISATEURS
-- =========================
INSERT INTO utilisateurs (id, nom, prenom, email, mot_de_passe, role)
VALUES
('11111111-1111-1111-1111-111111111111', 'Dupont', 'Jean', 'jean.dupont@test.com', 'password123', 'client'),
('22222222-2222-2222-2222-222222222222', 'Martin', 'Alice', 'alice.martin@test.com', 'password123', 'commercant'),
('33333333-3333-3333-3333-333333333333', 'Admin', 'Super', 'admin@test.com', 'adminpass', 'admin');

-- =========================
-- TRANSACTIONS
-- =========================
INSERT INTO transactions (id, emetteur_id, recepteur_id, montant, hash)
VALUES
(
  'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
  '11111111-1111-1111-1111-111111111111',
  '22222222-2222-2222-2222-222222222222',
  49.99,
  'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa'
),
(
  'bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb',
  '11111111-1111-1111-1111-111111111111',
  '22222222-2222-2222-2222-222222222222',
  120.00,
  'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb'
);

-- =========================
-- LOGS
-- =========================
INSERT INTO logs (id, acteur_id, action_type, details)
VALUES
(
  'cccccccc-cccc-cccc-cccc-cccccccccccc',
  '11111111-1111-1111-1111-111111111111',
  'CREATION_TRANSACTION',
  '{"transaction_id":"aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa","montant":49.99}'
),
(
  'dddddddd-dddd-dddd-dddd-dddddddddddd',
  '22222222-2222-2222-2222-222222222222',
  'RECEPTION_PAIEMENT',
  '{"transaction_id":"aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa"}'
),
(
  'eeeeeeee-eeee-eeee-eeee-eeeeeeeeeeee',
  '33333333-3333-3333-3333-333333333333',
  'CONNEXION_ADMIN',
  '{"ip":"127.0.0.1"}'
);
