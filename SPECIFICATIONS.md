# 🧺 APPLICATION DE PRESSING & LAVAGE

## 🎯 Objectif
Digitaliser et automatiser la gestion d'un pressing : prise de commandes, suivi des vêtements, paiements, notifications et gestion interne.

---

## 👥 Types d'utilisateurs

1. **Client** - Utilisateur de l'application mobile
2. **Pressing (Admin / Employé)** - Utilisateur du dashboard web
3. **Livreur** - Utilisateur de l'application livreur (optionnel)

---

## 📱 APPLICATION MOBILE – CLIENT

### 🔐 Authentification
- Inscription / Connexion via téléphone + OTP
- Gestion du profil utilisateur

### 🧾 Commande
**Sélection des services :**
- Lavage simple
- Lavage + repassage
- Repassage seul
- Nettoyage à sec

**Type de vêtements :**
- Chemise
- Pantalon
- Robe
- Costume
- Etc.

**Détails :**
- Quantité par article
- Instructions spéciales (texte / note)

### 📍 Logistique
**Choix :**
- Dépôt au pressing
- Collecte & livraison à domicile
  - Géolocalisation
  - Choix date & heure

### 💳 Paiement
- Mobile Money (Orange Money, MTN Mobile Money, Wave…)
- Espèces (paiement à la livraison)
- Wallet interne (optionnel)

### 📦 Suivi
**Statut en temps réel :**
1. Reçu
2. En lavage
3. Repassage
4. Prêt
5. En livraison
6. Livré

**Fonctionnalités :**
- Historique des commandes
- Détails de chaque commande

### 🔔 Notifications
- SMS / Push notifications :
  - Commande reçue
  - Prêt à récupérer
  - En cours de livraison
  - Livré

---

## 🧑‍💼 APPLICATION / DASHBOARD PRESSING (Web)

### 📊 Tableau de bord
- Commandes du jour
- Commandes en retard
- Revenus (journalier, hebdomadaire, mensuel)
- Clients actifs
- Statistiques visuelles

### 🧾 Gestion des commandes
- Création manuelle de commande
- Attribution à un employé
- Changement de statut
- Impression ticket / reçu
- Recherche et filtres

### 👕 Gestion articles & prix
- Liste des types de vêtements
- Prix par service et par type
- Gestion des promotions
- Tarification dynamique

### 👥 Gestion clients
- Liste des clients
- Historique des commandes par client
- Programme de fidélité (points / réductions)
- Coordonnées et préférences

### 🚚 Gestion livreurs (optionnel)
- Liste des livreurs
- Attribution de livraisons
- Suivi GPS en temps réel
- Statut livraison
- Performance (délais, nombre de livraisons)

### 👨‍💼 Gestion employés
- Liste des employés
- Rôles et permissions
- Attribution des tâches
- Historique d'activité

### 📈 Rapports
- Rapports journalier / hebdomadaire / mensuel
- Revenus par service
- Clients les plus actifs
- Performance des employés
- Export PDF / Excel

### ⚙️ Paramètres
- Configuration générale
- Gestion des tarifs
- Zones de livraison
- Horaires d'ouverture
- Notifications automatiques

---

## 🧑‍🔧 APPLICATION LIVREUR (Optionnel)

### Fonctionnalités
- Voir les courses assignées
- Navigation GPS intégrée
- Validation collecte
- Validation livraison
- Signature client (numérique)
- Historique des livraisons
- Prise de photo (preuve de livraison)

---

## 🗄️ ARCHITECTURE TECHNIQUE

### Backend (Laravel)
- API RESTful
- Authentification JWT / Sanctum
- Gestion des rôles (RBAC)
- Queue jobs (notifications, emails)
- Storage (photos, signatures)

### Base de données
**Tables principales :**
- users (clients, employés, livreurs, admin)
- orders (commandes)
- order_items (articles de la commande)
- services (types de services)
- clothing_types (types de vêtements)
- prices (tarification)
- payments (paiements)
- deliveries (livraisons)
- notifications
- loyalty_points (programme fidélité)

### Frontend
- Dashboard Web : Laravel Blade + JS (ou Vue.js/React)
- Application Mobile : Flutter / React Native (API REST)

### Services externes
- SMS Gateway (pour OTP et notifications)
- Mobile Money API (Orange Money, MTN, Wave)
- Géolocalisation (Google Maps / Mapbox)
- Push notifications (Firebase Cloud Messaging)

---

## 📋 PLAN DE DÉVELOPPEMENT

### Phase 1 : Foundation (Semaine 1-2)
- [x] Configuration base de données
- [ ] Migrations et modèles
- [ ] Authentification (téléphone + OTP)
- [ ] Rôles et permissions

### Phase 2 : Core Features (Semaine 3-4)
- [ ] Gestion des services et articles
- [ ] Système de tarification
- [ ] Création de commandes
- [ ] Workflow de statuts

### Phase 3 : Paiements (Semaine 5)
- [ ] Intégration Mobile Money
- [ ] Wallet interne
- [ ] Historique des transactions

### Phase 4 : Logistique (Semaine 6-7)
- [ ] Géolocalisation
- [ ] Gestion des livraisons
- [ ] Attribution aux livreurs
- [ ] Suivi GPS

### Phase 5 : Notifications (Semaine 8)
- [ ] SMS (OTP, notifications)
- [ ] Push notifications
- [ ] Emails
- [ ] Templates personnalisables

### Phase 6 : Dashboard Admin (Semaine 9-10)
- [ ] Tableau de bord
- [ ] Gestion commandes
- [ ] Gestion clients
- [ ] Rapports et statistiques

### Phase 7 : API Mobile (Semaine 11-12)
- [ ] API REST complète
- [ ] Documentation API
- [ ] Tests API
- [ ] Versions mobile (Flutter/RN)

### Phase 8 : Finalisation (Semaine 13-14)
- [ ] Tests complets
- [ ] Optimisations
- [ ] Déploiement
- [ ] Formation utilisateurs

---

## 🔐 SÉCURITÉ

- Authentification sécurisée (OTP, JWT)
- Validation des données
- Protection CSRF
- Rate limiting
- Chiffrement des données sensibles
- Logs d'activité

---

## 🚀 DÉPLOIEMENT

- Serveur : VPS (DigitalOcean, AWS, etc.)
- CI/CD : GitHub Actions
- Database : MySQL / PostgreSQL
- Cache : Redis
- Queue : Redis / Supervisor
- SSL : Let's Encrypt

---

## 📞 SUPPORT & MAINTENANCE

- Monitoring (uptime, erreurs)
- Backups automatiques
- Mises à jour régulières
- Support client
- Documentation technique

