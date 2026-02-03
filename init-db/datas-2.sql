-- =========================
-- UTILISATEURS
-- =========================
INSERT INTO "utilisateurs" ("id", "nom", "prenom", "email", "mot_de_passe", "role") VALUES
('30ecd90b-d236-48c7-8d52-0a742d6f69be',	'Prarg',	'Alice',	'alice.prarg@mail.com',	'$2y$12$DgDM3nHwAqfQbMcDXGVQpuDZ5xtGCKDWuJBeuV4ro.D1Cz7p6TbJC',	'client'),
('1981ce62-42cb-4500-bafe-b95d916f935d',	'Dupont',	'Bob',	'bob.dupont@mail.com',	'$2y$12$hHMqfakqvbE3QGKtSeJcKO9bAfniusQ3.ABf.yvIZuTo9Mm6MBez.',	'commercant'),
('4aa909fa-2f38-4800-a4a7-f2d1f53f977e',	'admin',	'admin',	'admin@mail.com',	'$2y$12$ZNjitqvG9iux4rytmjZc2.HdlFEnN8RurA95Eh64o2Gsv/em9SvYu',	'admin');

-- =========================
-- TRANSACTIONS
-- =========================
INSERT INTO transactions (id, emetteur_id, recepteur_id, montant, hash)
VALUES
(
  'b24daee3-8bba-4ed7-ac18-55d07e1faee3',
  '4aa909fa-2f38-4800-a4a7-f2d1f53f977e',
  '30ecd90b-d236-48c7-8d52-0a742d6f69be',
  49.99,
  '9a8b64bb1668aae447964e503998fe5e9686c4101556a33edd13d826aa0b69e3'
),
(
  'caf91a0f-9f2b-4f29-8430-fce7183ebc86',
  '4aa909fa-2f38-4800-a4a7-f2d1f53f977e',
  '1981ce62-42cb-4500-bafe-b95d916f935d',
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
  '4aa909fa-2f38-4800-a4a7-f2d1f53f977e',
  'CREATION_TRANSACTION',
  '{"transaction_id":"b24daee3-8bba-4ed7-ac18-55d07e1faee3","montant":49.99}'
),
(
  'f2aa6bf1-a140-4ebd-9417-e6aa9549569b',
  '30ecd90b-d236-48c7-8d52-0a742d6f69be',
  'RECEPTION_PAIEMENT',
  '{"transaction_id":"b24daee3-8bba-4ed7-ac18-55d07e1faee3"}'
),
(
  '8e2fa852-3c42-4043-ac65-56bc4e5e956d',
  '4aa909fa-2f38-4800-a4a7-f2d1f53f977e',
  'CONNEXION_ADMIN',
  '{"ip":"127.0.0.1"}'
);
