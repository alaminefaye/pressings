#!/bin/bash
# 🚀 Script de déploiement Sanctum sur production
# À exécuter sur le serveur : pressings.universaltechnologiesafrica.com

echo "╔═══════════════════════════════════════════════════════════╗"
echo "║     DÉPLOIEMENT SANCTUM - SERVEUR DE PRODUCTION          ║"
echo "╚═══════════════════════════════════════════════════════════╝"
echo ""

# Arrêter le script en cas d'erreur
set -e

# Couleurs pour le terminal
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}📂 Étape 1: Navigation vers le projet${NC}"
cd /path/to/your/project  # ⚠️ MODIFIER AVEC LE BON CHEMIN
echo -e "${GREEN}✅ Répertoire: $(pwd)${NC}"
echo ""

echo -e "${YELLOW}📦 Étape 2: Mise à jour via Git (optionnel)${NC}"
# Décommenter si vous utilisez Git
# git pull origin main
echo -e "${GREEN}✅ Code mis à jour${NC}"
echo ""

echo -e "${YELLOW}🔧 Étape 3: Installation des dépendances${NC}"
composer install --no-dev --optimize-autoloader
echo -e "${GREEN}✅ Dépendances installées${NC}"
echo ""

echo -e "${YELLOW}🗄️  Étape 4: Migration de la base de données${NC}"
php artisan migrate --force
echo -e "${GREEN}✅ Table personal_access_tokens créée${NC}"
echo ""

echo -e "${YELLOW}🧹 Étape 5: Nettoyage des caches${NC}"
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo -e "${GREEN}✅ Caches nettoyés${NC}"
echo ""

echo -e "${YELLOW}⚙️  Étape 6: Optimisation pour production${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo -e "${GREEN}✅ Optimisations appliquées${NC}"
echo ""

echo -e "${YELLOW}🔐 Étape 7: Permissions des fichiers${NC}"
chmod -R 775 storage bootstrap/cache
# Adapter www-data selon votre serveur (peut être nginx, apache, etc.)
chown -R www-data:www-data storage bootstrap/cache
echo -e "${GREEN}✅ Permissions configurées${NC}"
echo ""

echo -e "${GREEN}╔═══════════════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║           🎉 DÉPLOIEMENT TERMINÉ AVEC SUCCÈS ! 🎉        ║${NC}"
echo -e "${GREEN}╚═══════════════════════════════════════════════════════════╝${NC}"
echo ""

echo -e "${YELLOW}🧪 Test de l'API:${NC}"
echo "curl -X POST http://pressings.universaltechnologiesafrica.com/api/auth/send-otp \\"
echo "  -H \"Content-Type: application/json\" \\"
echo "  -d '{\"phone\":\"0707070701\"}'"
echo ""

echo -e "${YELLOW}📱 Vous pouvez maintenant relancer l'application mobile !${NC}"
echo ""

