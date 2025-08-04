-- Insertion d'enseignants sénégalais dans la table enseignants
-- Exécutez ce code dans MySQL Workbench

INSERT INTO enseignants (nom, prenom, email, telephone, specialite, created_at, updated_at) VALUES
('Diop', 'Fatou', 'fatou.diop@ecole.sn', '778123456', 'Mathématiques', NOW(), NOW()),
('Sall', 'Mamadou', 'mamadou.sall@ecole.sn', '778234567', 'Français', NOW(), NOW()),
('Ndiaye', 'Aissatou', 'aissatou.ndiaye@ecole.sn', '778345678', 'Histoire-Géographie', NOW(), NOW()),
('Ba', 'Ousmane', 'ousmane.ba@ecole.sn', '778456789', 'Sciences', NOW(), NOW()),
('Diallo', 'Mariama', 'mariama.diallo@ecole.sn', '778567890', 'Anglais', NOW(), NOW()),
('Cissé', 'Ibrahima', 'ibrahima.cisse@ecole.sn', '778678901', 'Physique-Chimie', NOW(), NOW()),
('Thiam', 'Aminata', 'aminata.thiam@ecole.sn', '778789012', 'SVT', NOW(), NOW()),
('Gueye', 'Modou', 'modou.gueye@ecole.sn', '778890123', 'Éducation Physique', NOW(), NOW()),
('Fall', 'Khadija', 'khadija.fall@ecole.sn', '778901234', 'Arts Plastiques', NOW(), NOW()),
('Sy', 'Abdoulaye', 'abdoulaye.sy@ecole.sn', '778012345', 'Informatique', NOW(), NOW()),
('Toure', 'Fatoumata', 'fatoumata.toure@ecole.sn', '778123456', 'Espagnol', NOW(), NOW()),
('Kane', 'Moussa', 'moussa.kane@ecole.sn', '778234567', 'Allemand', NOW(), NOW()),
('Seck', 'Awa', 'awa.seck@ecole.sn', '778345678', 'Musique', NOW(), NOW()),
('Mbaye', 'Cheikh', 'cheikh.mbaye@ecole.sn', '778456789', 'Technologie', NOW(), NOW()),
('Faye', 'Ndeye', 'ndeye.faye@ecole.sn', '778567890', 'Mathématiques', NOW(), NOW());

-- Vérification de l'insertion
SELECT * FROM enseignants ORDER BY nom, prenom; 