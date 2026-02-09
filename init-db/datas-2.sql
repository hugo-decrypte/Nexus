-- =========================
-- UTILISATEURS
-- =========================
INSERT INTO "utilisateurs" ("id", "nom", "prenom", "email", "mot_de_passe", "role") VALUES
('30ecd90b-d236-48c7-8d52-0a742d6f69be',	'Prarg',	'Alice',	'alice.prarg@mail.com',	'$2y$12$DgDM3nHwAqfQbMcDXGVQpuDZ5xtGCKDWuJBeuV4ro.D1Cz7p6TbJC',	'client'),
('1981ce62-42cb-4500-bafe-b95d916f935d',	'Dupont',	'Bob',	'bob.dupont@mail.com',	'$2y$12$hHMqfakqvbE3QGKtSeJcKO9bAfniusQ3.ABf.yvIZuTo9Mm6MBez.',	'commercant'),
('4aa909fa-2f38-4800-a4a7-f2d1f53f977e',	'admin',	'admin',	'admin@mail.com',	'$2y$12$ZNjitqvG9iux4rytmjZc2.HdlFEnN8RurA95Eh64o2Gsv/em9SvYu',	'admin'),
('23a14125-1275-4597-b054-8ee4415f367c',	'Reignier',	'Eloi',	'reignier.eloi@mail.com',	'$2y$12$ZuUfqYmGoOAY1ahExrRNiezmzy8LnAiBTcSYpUxHkcZ3sqa02dA/.',	'client'),
('a5308758-6fe6-46af-a51f-53e17c117153',	'Bena',	'Hugo',	'bena.hugo@mail.com',	'$2y$12$zLJZ7Tetj0e0tmzi6gPjv.qtJfeSRQuESDe4aPWsDhUQAAJx0prKu',	'client'),
('b6c383f0-38a5-416f-a31e-ff4880575056',	'Hayrapetyan',	'Arman',	'hayrapetyan.arman@mail.com',	'$2y$12$RpQQDpqS3ByIIEHi6RkMluaSPQnjqV3doCAgCrxT7l1ae27oiGrKe',	'client'),
('6e71b328-b175-46f7-8673-f4502e23e5d5',	'Cazottes',	'Alexandre',	'cazottes.alexandre@mail.com',	'$2y$12$xfjRgDpCGXOvYzrqjTHqEe9f1KncoADDxCLj0SAbfJL9TgC0YSpnW',	'client'),
('9d19ffb0-929b-4c7c-ae5d-21bcf2a9f72a',	'Leveque',	'Tuline',	'leveque.tuline@mail.com',	'$2y$12$s2OkiGLk3JHVSvyNhPAQS.96NiUyg8jXjZsvQ9yU4roMZ/i5PnWOO',	'commercant');

-- =========================
-- TRANSACTIONS
-- =========================
INSERT INTO transactions (id, emetteur_id, recepteur_id, montant, hash)
VALUES
('b24daee3-8bba-4ed7-ac18-55d07e1faee3',	'4aa909fa-2f38-4800-a4a7-f2d1f53f977e',	'30ecd90b-d236-48c7-8d52-0a742d6f69be',	49.99,	'9a8b64bb1668aae447964e503998fe5e9686c4101556a33edd13d826aa0b69e3'),
('caf91a0f-9f2b-4f29-8430-fce7183ebc86',	'4aa909fa-2f38-4800-a4a7-f2d1f53f977e',	'1981ce62-42cb-4500-bafe-b95d916f935d',	120.00,	'1f18df802162364679d7ea5019a68cc3e4ced6242ad247202b7912146c587222'),
('d24834a1-6ab4-4c18-9e1e-b8184627bdd7',	'4aa909fa-2f38-4800-a4a7-f2d1f53f977e',	'23a14125-1275-4597-b054-8ee4415f367c',	2000.00,	'801b238915fa964c560c3b9a986e4e7ef520825a5891e9282a2177210bf9f917'),
('51625c48-7827-41f9-b5ac-c8d2b07018a0',	'4aa909fa-2f38-4800-a4a7-f2d1f53f977e',	'a5308758-6fe6-46af-a51f-53e17c117153',	400.00,	'0f78ae4e39bb9249039c503e76fe6331642aa40faadae56edb9fa1ac4ccb9da6'),
('ff0a1381-19eb-44e4-a46d-be05b077fc77',	'4aa909fa-2f38-4800-a4a7-f2d1f53f977e',	'9d19ffb0-929b-4c7c-ae5d-21bcf2a9f72a',	800.00,	'78fbd3d2f7dbdd84e2b6ac486cb0b17983a7eae51bad10d2be5e65c16b78bb13'),
('18a3c478-1ad0-40de-aec9-147d167768b6',	'4aa909fa-2f38-4800-a4a7-f2d1f53f977e',	'b6c383f0-38a5-416f-a31e-ff4880575056',	100000.00,	'dee8118c20fdb7e85d17452d699c7edaee02364ff177216bc9ab7aeee9ab9b27'),
('81154e15-1ab5-437c-93ee-5e855eb75ee3',	'4aa909fa-2f38-4800-a4a7-f2d1f53f977e',	'6e71b328-b175-46f7-8673-f4502e23e5d5',	1800.00,	'47ee88541c093810fde5aaf979aa7accf5beb4b1e44b685359e044d8d9f0dd0c'),
('12b1dc1a-9c0d-4133-b686-5e1ee0ee07cb',	'b6c383f0-38a5-416f-a31e-ff4880575056',	'9d19ffb0-929b-4c7c-ae5d-21bcf2a9f72a',	2200.00,	'148f321b42917b702ed5463a3179c46f6f1f4202bb8f35b22128f92649e9e364'),
('813ca84e-55a2-432d-9700-9bbea4a2c54d',	'b6c383f0-38a5-416f-a31e-ff4880575056',	'1981ce62-42cb-4500-bafe-b95d916f935d',	18500.00,	'47bf4952b045f8921181bb9284cbaef766c23498f8934e51238ff12a5491b391'),
('5153c7c6-20d6-4258-b36b-003961b2e47f',	'23a14125-1275-4597-b054-8ee4415f367c',	'1981ce62-42cb-4500-bafe-b95d916f935d',	500.00,	'58ce97f3764dc7cc80245eb828ecaf1bd3b5b1304d5d4552cc0320f1a91266bf'),
('9e60ed2c-3ec7-4845-abeb-4d8d89cba919',	'6e71b328-b175-46f7-8673-f4502e23e5d5',	'9d19ffb0-929b-4c7c-ae5d-21bcf2a9f72a',	750.00,	'69d0b70cd922847180baf0f4beb908bf6c832e70e92c3109ba76567a3f44c3fe'),
('e7dcefc1-2237-4bcc-b8a9-0774ce0fb05b',	'a5308758-6fe6-46af-a51f-53e17c117153',	'9d19ffb0-929b-4c7c-ae5d-21bcf2a9f72a',	225.00,	'f123c808e8ea178808b2b7c00cd1329f56d64338d1059be6d54e5f792c029e2f');

-- =========================
-- LOGS
-- =========================
INSERT INTO logs (id, acteur_id, action_type, details)
VALUES
    ('5456e189-741f-4c1d-b165-d339eccc7529',	'4aa909fa-2f38-4800-a4a7-f2d1f53f977e',	'CREATION_TRANSACTION',	'{"transaction_id":"b24daee3-8bba-4ed7-ac18-55d07e1faee3","montant":49.99}'),
    ('f2aa6bf1-a140-4ebd-9417-e6aa9549569b',	'30ecd90b-d236-48c7-8d52-0a742d6f69be',	'RECEPTION_PAIEMENT',	'{"transaction_id":"b24daee3-8bba-4ed7-ac18-55d07e1faee3"}'),
    ('d139157a-0870-425b-bfce-672feaff15bc',	'4aa909fa-2f38-4800-a4a7-f2d1f53f977e',	'CREATION_TRANSACTION',	'{"transaction_id":"b24daee3-8bba-4ed7-ac18-55d07e1faee3","montant":120.00}'),
    ('820b6ed9-aa03-4935-8348-f2cf93f819b5',	'1981ce62-42cb-4500-bafe-b95d916f935d',	'RECEPTION_PAIEMENT',	'{"transaction_id":"b24daee3-8bba-4ed7-ac18-55d07e1faee3"}'),
    ('8e2fa852-3c42-4043-ac65-56bc4e5e956d',	'4aa909fa-2f38-4800-a4a7-f2d1f53f977e',	'CONNEXION',	'{"acteur_id":"4aa909fa-2f38-4800-a4a7-f2d1f53f977e"}'),
    ('2bc1d4f5-e3cb-4fde-af0f-534e3849c1b9',	'4aa909fa-2f38-4800-a4a7-f2d1f53f977e',	'CREATION_TRANSACTION',	'{"transaction_id":"d24834a1-6ab4-4c18-9e1e-b8184627bdd7","montant":2000}'),
    ('9af7e1ee-43ea-475f-ab1e-650d54fc3adf',	'23a14125-1275-4597-b054-8ee4415f367c',	'RECEPTION_PAIEMENT',	'{"transaction_id":"d24834a1-6ab4-4c18-9e1e-b8184627bdd7"}'),
    ('dcbc62a3-2cd4-4e1a-91ad-4bb0f65535c5',	'4aa909fa-2f38-4800-a4a7-f2d1f53f977e',	'CREATION_TRANSACTION',	'{"transaction_id":"51625c48-7827-41f9-b5ac-c8d2b07018a0","montant":400}'),
    ('170e0b6e-eda7-4cd2-bcee-86c5ed42d77e',	'a5308758-6fe6-46af-a51f-53e17c117153',	'RECEPTION_PAIEMENT',	'{"transaction_id":"51625c48-7827-41f9-b5ac-c8d2b07018a0"}'),
    ('56f38393-76e8-456b-a6a4-9c50fc3585a1',	'4aa909fa-2f38-4800-a4a7-f2d1f53f977e',	'CREATION_TRANSACTION',	'{"transaction_id":"ff0a1381-19eb-44e4-a46d-be05b077fc77","montant":800}'),
    ('e1983d40-5862-42c8-8b80-ed9476f6be1c',	'9d19ffb0-929b-4c7c-ae5d-21bcf2a9f72a',	'RECEPTION_PAIEMENT',	'{"transaction_id":"ff0a1381-19eb-44e4-a46d-be05b077fc77"}'),
    ('ad615cb0-ff4b-4cad-9961-ed9c42f8ddfa',	'4aa909fa-2f38-4800-a4a7-f2d1f53f977e',	'CREATION_TRANSACTION',	'{"transaction_id":"18a3c478-1ad0-40de-aec9-147d167768b6","montant":100000}'),
    ('9830fba2-d9ba-44be-aee1-5ae54d5f5688',	'b6c383f0-38a5-416f-a31e-ff4880575056',	'RECEPTION_PAIEMENT',	'{"transaction_id":"18a3c478-1ad0-40de-aec9-147d167768b6"}'),
    ('cd67f752-6bb0-4501-ac0c-54f7ef840699',	'4aa909fa-2f38-4800-a4a7-f2d1f53f977e',	'CREATION_TRANSACTION',	'{"transaction_id":"81154e15-1ab5-437c-93ee-5e855eb75ee3","montant":1800}'),
    ('3fe8e99c-ca2e-4b42-ad4f-17ca9accf071',	'6e71b328-b175-46f7-8673-f4502e23e5d5',	'RECEPTION_PAIEMENT',	'{"transaction_id":"81154e15-1ab5-437c-93ee-5e855eb75ee3"}'),
    ('3eea1481-2119-40f8-85cd-cb7ce2709ed8',	'b6c383f0-38a5-416f-a31e-ff4880575056',	'CREATION_TRANSACTION',	'{"transaction_id":"12b1dc1a-9c0d-4133-b686-5e1ee0ee07cb","montant":2200}'),
    ('60f97e54-1979-4cae-a49a-7924d6f8041c',	'9d19ffb0-929b-4c7c-ae5d-21bcf2a9f72a',	'RECEPTION_PAIEMENT',	'{"transaction_id":"12b1dc1a-9c0d-4133-b686-5e1ee0ee07cb"}'),
    ('73fddbc3-cb07-46a5-924a-993ebba3b58c',	'b6c383f0-38a5-416f-a31e-ff4880575056',	'CREATION_TRANSACTION',	'{"transaction_id":"813ca84e-55a2-432d-9700-9bbea4a2c54d","montant":18500}'),
    ('0c2aa441-70b4-4607-a290-8bc3351b7d21',	'1981ce62-42cb-4500-bafe-b95d916f935d',	'RECEPTION_PAIEMENT',	'{"transaction_id":"813ca84e-55a2-432d-9700-9bbea4a2c54d"}'),
    ('b618f075-e0c6-4de1-931a-db55a2b9291d',	'23a14125-1275-4597-b054-8ee4415f367c',	'CREATION_TRANSACTION',	'{"transaction_id":"5153c7c6-20d6-4258-b36b-003961b2e47f","montant":500}'),
    ('119579e5-7487-41bb-9518-a9eb858b9479',	'1981ce62-42cb-4500-bafe-b95d916f935d',	'RECEPTION_PAIEMENT',	'{"transaction_id":"5153c7c6-20d6-4258-b36b-003961b2e47f"}'),
    ('e5dc9c13-083f-4add-a40e-f975e4c6061f',	'6e71b328-b175-46f7-8673-f4502e23e5d5',	'CREATION_TRANSACTION',	'{"transaction_id":"9e60ed2c-3ec7-4845-abeb-4d8d89cba919","montant":750}'),
    ('0cd583e7-cbaa-48c9-b2c2-42b248fa4493',	'9d19ffb0-929b-4c7c-ae5d-21bcf2a9f72a',	'RECEPTION_PAIEMENT',	'{"transaction_id":"9e60ed2c-3ec7-4845-abeb-4d8d89cba919"}'),
    ('4bd04e4e-bd2c-4eac-a7b4-8d8b4d61a67b',	'a5308758-6fe6-46af-a51f-53e17c117153',	'CREATION_TRANSACTION',	'{"transaction_id":"e7dcefc1-2237-4bcc-b8a9-0774ce0fb05b","montant":225}'),
    ('b7266de0-17da-4158-b964-f10a41614c7b',	'9d19ffb0-929b-4c7c-ae5d-21bcf2a9f72a',	'RECEPTION_PAIEMENT',	'{"transaction_id":"e7dcefc1-2237-4bcc-b8a9-0774ce0fb05b"}');
