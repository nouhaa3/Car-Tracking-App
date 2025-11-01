-- ============================================
-- MIGRATIONS SQL MANUELLES
-- À exécuter si php artisan migrate ne fonctionne pas
-- ============================================

-- 1. Table documents_vehicule
CREATE TABLE IF NOT EXISTS documents_vehicule (
    idDocument BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    voiture_id BIGINT UNSIGNED NOT NULL,
    type ENUM('Carte grise', 'Assurance', 'Contrôle technique', 'Garantie', 'Facture achat', 'Autre') NOT NULL,
    nom_fichier VARCHAR(255) NOT NULL,
    chemin VARCHAR(255) NOT NULL,
    date_expiration DATE NULL,
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (voiture_id) REFERENCES voitures(idVoiture) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. SoftDeletes sur voitures
ALTER TABLE voitures 
ADD COLUMN deleted_at TIMESTAMP NULL AFTER updated_at;

-- 3. SoftDeletes sur interventions
ALTER TABLE interventions 
ADD COLUMN deleted_at TIMESTAMP NULL AFTER updated_at;

-- 4. Assigned_to sur interventions
ALTER TABLE interventions 
ADD COLUMN assigned_to BIGINT UNSIGNED NULL AFTER voiture_id,
ADD CONSTRAINT fk_interventions_assigned_to 
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL;

-- 5. Vérifier que les tables existent
SELECT 
    'documents_vehicule' as table_name, 
    COUNT(*) as row_count 
FROM documents_vehicule
UNION ALL
SELECT 'voitures', COUNT(*) FROM voitures WHERE deleted_at IS NOT NULL
UNION ALL
SELECT 'interventions', COUNT(*) FROM interventions WHERE deleted_at IS NOT NULL;

-- ============================================
-- ROLLBACK (si besoin)
-- ============================================

-- DROP TABLE IF EXISTS documents_vehicule;
-- ALTER TABLE voitures DROP COLUMN deleted_at;
-- ALTER TABLE interventions DROP COLUMN deleted_at;
-- ALTER TABLE interventions DROP FOREIGN KEY fk_interventions_assigned_to;
-- ALTER TABLE interventions DROP COLUMN assigned_to;
