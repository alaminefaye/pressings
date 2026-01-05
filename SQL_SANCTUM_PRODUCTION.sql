-- ===============================================================
-- 🚀 SQL pour déployer Sanctum sur production (sans accès SSH)
-- À exécuter dans phpMyAdmin ou votre client MySQL
-- ===============================================================

-- Sélectionner la base de données (adapter le nom si nécessaire)
-- USE votre_nom_de_base_de_donnees;

-- ===============================================================
-- Créer la table personal_access_tokens
-- ===============================================================

CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================================
-- Vérification
-- ===============================================================

-- Vérifier que la table a été créée
SHOW TABLES LIKE 'personal_access_tokens';

-- Vérifier la structure de la table
DESCRIBE personal_access_tokens;

-- ===============================================================
-- Insertion dans la table migrations (pour tracking Laravel)
-- ===============================================================

INSERT INTO `migrations` (`migration`, `batch`) 
VALUES ('2026_01_05_193454_create_personal_access_tokens_table', 
        (SELECT COALESCE(MAX(batch), 0) + 1 FROM migrations m));

-- ===============================================================
-- ✅ TERMINÉ !
-- ===============================================================

-- Vous pouvez maintenant :
-- 1. Uploader les fichiers modifiés via FTP :
--    - app/Models/User.php
--    - composer.json
--    - composer.lock
--    - config/sanctum.php
--
-- 2. Exécuter via FTP/SSH :
--    composer install --no-dev
--
-- 3. Nettoyer les caches via un script PHP ou l'admin panel
--
-- 4. Tester l'API :
--    curl -X POST http://pressings.universaltechnologiesafrica.com/api/auth/send-otp
--      -H "Content-Type: application/json"
--      -d '{"phone":"0707070701"}'
--
-- ===============================================================

