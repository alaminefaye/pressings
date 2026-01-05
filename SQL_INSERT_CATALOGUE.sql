-- ============================================================================
-- SCRIPT SQL - INSERTION DU CATALOGUE (Services, Types de vêtements, Prix)
-- ============================================================================
-- À exécuter dans phpMyAdmin sur votre serveur de production
-- Date: 2026-01-05
-- ============================================================================

-- 1. INSERTION DES SERVICES
-- ----------------------------------------------------------------------------
INSERT INTO `services` (`name`, `slug`, `description`, `duration_hours`, `is_active`, `created_at`, `updated_at`) VALUES
('Lavage simple', 'lavage-simple', 'Lavage à l''eau uniquement', 24, 1, NOW(), NOW()),
('Lavage + Repassage', 'lavage-repassage', 'Lavage à l''eau et repassage', 48, 1, NOW(), NOW()),
('Repassage seul', 'repassage-seul', 'Repassage uniquement', 24, 1, NOW(), NOW()),
('Nettoyage à sec', 'nettoyage-sec', 'Nettoyage à sec pour tissus délicats', 72, 1, NOW(), NOW()),
('Pressing express', 'pressing-express', 'Service rapide en 6 heures', 6, 1, NOW(), NOW());

-- 2. INSERTION DES TYPES DE VÊTEMENTS
-- ----------------------------------------------------------------------------
INSERT INTO `clothing_types` (`name`, `slug`, `description`, `icon`, `is_active`, `created_at`, `updated_at`) VALUES
('Chemise', 'chemise', 'Chemise homme ou femme', '👔', 1, NOW(), NOW()),
('Pantalon', 'pantalon', 'Pantalon classique', '👖', 1, NOW(), NOW()),
('Robe', 'robe', 'Robe femme', '👗', 1, NOW(), NOW()),
('Costume', 'costume', 'Costume complet (veste + pantalon)', '🤵', 1, NOW(), NOW()),
('Veste/Blazer', 'veste-blazer', 'Veste ou blazer', '🧥', 1, NOW(), NOW()),
('Jupe', 'jupe', 'Jupe femme', '👘', 1, NOW(), NOW()),
('T-shirt', 't-shirt', 'T-shirt ou polo', '👕', 1, NOW(), NOW()),
('Pull/Gilet', 'pull-gilet', 'Pull ou gilet', '🧶', 1, NOW(), NOW()),
('Manteau', 'manteau', 'Manteau ou pardessus', '🧥', 1, NOW(), NOW()),
('Jean', 'jean', 'Pantalon en jean', '👖', 1, NOW(), NOW()),
('Couverture', 'couverture', 'Couverture ou plaid', '🛏️', 1, NOW(), NOW()),
('Drap', 'drap', 'Drap de lit', '🛏️', 1, NOW(), NOW()),
('Rideau', 'rideau', 'Rideau', '🪟', 1, NOW(), NOW());

-- 3. INSERTION DES PRIX
-- ----------------------------------------------------------------------------
-- Note: Les IDs des services et clothing_types seront auto-incrémentés
-- Cette requête suppose que les services ont les IDs 1-5 et les clothing_types 1-13

-- Chemise (clothing_type_id = 1)
INSERT INTO `prices` (`service_id`, `clothing_type_id`, `price`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 500, 1, NOW(), NOW()),   -- Lavage simple
(2, 1, 1000, 1, NOW(), NOW()),  -- Lavage + Repassage
(3, 1, 500, 1, NOW(), NOW()),   -- Repassage seul
(4, 1, 1500, 1, NOW(), NOW()),  -- Nettoyage à sec
(5, 1, 1500, 1, NOW(), NOW());  -- Pressing express

-- Pantalon (clothing_type_id = 2)
INSERT INTO `prices` (`service_id`, `clothing_type_id`, `price`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 2, 500, 1, NOW(), NOW()),
(2, 2, 1000, 1, NOW(), NOW()),
(3, 2, 500, 1, NOW(), NOW()),
(4, 2, 1500, 1, NOW(), NOW()),
(5, 2, 1500, 1, NOW(), NOW());

-- Robe (clothing_type_id = 3)
INSERT INTO `prices` (`service_id`, `clothing_type_id`, `price`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 3, 800, 1, NOW(), NOW()),
(2, 3, 1500, 1, NOW(), NOW()),
(3, 3, 700, 1, NOW(), NOW()),
(4, 3, 2000, 1, NOW(), NOW()),
(5, 3, 2500, 1, NOW(), NOW());

-- Costume (clothing_type_id = 4)
INSERT INTO `prices` (`service_id`, `clothing_type_id`, `price`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 4, 1500, 1, NOW(), NOW()),
(2, 4, 2500, 1, NOW(), NOW()),
(3, 4, 1000, 1, NOW(), NOW()),
(4, 4, 3000, 1, NOW(), NOW()),
(5, 4, 3500, 1, NOW(), NOW());

-- Veste/Blazer (clothing_type_id = 5)
INSERT INTO `prices` (`service_id`, `clothing_type_id`, `price`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 5, 1000, 1, NOW(), NOW()),
(2, 5, 1800, 1, NOW(), NOW()),
(3, 5, 800, 1, NOW(), NOW()),
(4, 5, 2500, 1, NOW(), NOW()),
(5, 5, 2800, 1, NOW(), NOW());

-- Jupe (clothing_type_id = 6)
INSERT INTO `prices` (`service_id`, `clothing_type_id`, `price`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 6, 500, 1, NOW(), NOW()),
(2, 6, 1000, 1, NOW(), NOW()),
(3, 6, 500, 1, NOW(), NOW()),
(4, 6, 1500, 1, NOW(), NOW()),
(5, 6, 1500, 1, NOW(), NOW());

-- T-shirt (clothing_type_id = 7)
INSERT INTO `prices` (`service_id`, `clothing_type_id`, `price`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 7, 300, 1, NOW(), NOW()),
(2, 7, 700, 1, NOW(), NOW()),
(3, 7, 400, 1, NOW(), NOW()),
(4, 7, 1000, 1, NOW(), NOW()),
(5, 7, 1000, 1, NOW(), NOW());

-- Pull/Gilet (clothing_type_id = 8)
INSERT INTO `prices` (`service_id`, `clothing_type_id`, `price`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 8, 800, 1, NOW(), NOW()),
(2, 8, 1500, 1, NOW(), NOW()),
(3, 8, 700, 1, NOW(), NOW()),
(4, 8, 2000, 1, NOW(), NOW()),
(5, 8, 2200, 1, NOW(), NOW());

-- Manteau (clothing_type_id = 9)
INSERT INTO `prices` (`service_id`, `clothing_type_id`, `price`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 9, 1500, 1, NOW(), NOW()),
(2, 9, 2500, 1, NOW(), NOW()),
(3, 9, 1000, 1, NOW(), NOW()),
(4, 9, 3500, 1, NOW(), NOW()),
(5, 9, 4000, 1, NOW(), NOW());

-- Jean (clothing_type_id = 10)
INSERT INTO `prices` (`service_id`, `clothing_type_id`, `price`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 10, 500, 1, NOW(), NOW()),
(2, 10, 1000, 1, NOW(), NOW()),
(3, 10, 500, 1, NOW(), NOW()),
(4, 10, 1500, 1, NOW(), NOW()),
(5, 10, 1500, 1, NOW(), NOW());

-- Couverture (clothing_type_id = 11)
INSERT INTO `prices` (`service_id`, `clothing_type_id`, `price`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 11, 2000, 1, NOW(), NOW()),
(2, 11, 3000, 1, NOW(), NOW()),
(3, 11, 1000, 1, NOW(), NOW()),
(4, 11, 4000, 1, NOW(), NOW()),
(5, 11, 4500, 1, NOW(), NOW());

-- Drap (clothing_type_id = 12)
INSERT INTO `prices` (`service_id`, `clothing_type_id`, `price`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 12, 1000, 1, NOW(), NOW()),
(2, 12, 1500, 1, NOW(), NOW()),
(3, 12, 500, 1, NOW(), NOW()),
(4, 12, 2000, 1, NOW(), NOW()),
(5, 12, 2200, 1, NOW(), NOW());

-- Rideau (clothing_type_id = 13)
INSERT INTO `prices` (`service_id`, `clothing_type_id`, `price`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 13, 1500, 1, NOW(), NOW()),
(2, 13, 2500, 1, NOW(), NOW()),
(3, 13, 1000, 1, NOW(), NOW()),
(4, 13, 3000, 1, NOW(), NOW()),
(5, 13, 3500, 1, NOW(), NOW());

-- ============================================================================
-- FIN DU SCRIPT
-- ============================================================================

-- Vérification des insertions
SELECT 'Services insérés:' as Info, COUNT(*) as Total FROM `services`;
SELECT 'Types de vêtements insérés:' as Info, COUNT(*) as Total FROM `clothing_types`;
SELECT 'Prix insérés:' as Info, COUNT(*) as Total FROM `prices`;

-- Afficher les données
SELECT * FROM `services`;
SELECT * FROM `clothing_types`;
SELECT * FROM `prices` LIMIT 20;

