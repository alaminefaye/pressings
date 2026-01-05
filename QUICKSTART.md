# 🚀 GUIDE DE DÉMARRAGE RAPIDE - Pressing App

## ✅ État actuel : **50% complété**

**6 modules sur 12 terminés** - L'API backend est prête à 70% !

---

## 📋 Ce qui est PRÊT

### ✅ Authentification complète
- OTP par SMS pour les clients
- Login email/password pour admin/employés
- Gestion de profil
- 4 types d'utilisateurs (client, admin, employé, livreur)

### ✅ Catalogue complet
- 13 types de vêtements
- 5 services (lavage, repassage, nettoyage à sec, etc.)
- 65 combinaisons de prix configurées
- Système de promotions

### ✅ Système de commandes
- Création de commandes avec calcul automatique
- Workflow de 8 statuts
- Historique complet
- Application automatique des promos
- Frais de livraison intelligents

### ✅ Système de paiement
- Mobile Money (structure prête)
- Paiement cash
- Portefeuille interne
- Remboursements
- Programme de fidélité automatique

---

## 🔧 Installation

### 1. Prérequis
```bash
- PHP 8.2+
- Composer
- SQLite ou MySQL
```

### 2. Installation des dépendances
```bash
cd /Users/Zhuanz/Desktop/projets/web/pressing
composer install
```

### 3. Configuration
Le fichier `.env` est déjà configuré avec SQLite.

### 4. Base de données
```bash
php artisan migrate:fresh --seed
```

Cette commande crée :
- 23 tables
- 4 utilisateurs de test
- 13 types de vêtements
- 5 services
- 65 prix configurés
- Paramètres de l'application

### 5. Lancer le serveur
```bash
php artisan serve
```

L'API sera disponible sur : `http://localhost:8000`

---

## 👤 Comptes de test

### Admin
- **Email:** admin@pressing.com
- **Password:** password
- **Phone:** +2250700000000

### Employé
- **Email:** employee@pressing.com
- **Password:** password
- **Phone:** +2250700000001

### Livreur
- **Email:** driver@pressing.com
- **Password:** password
- **Phone:** +2250700000002

### Client
- **Phone:** +2250700000003
- **Auth:** OTP (code sera dans les logs)

---

## 🧪 Tester l'API

### 1. Authentification client (OTP)

**Demander un code OTP :**
```bash
curl -X POST http://localhost:8000/api/v1/auth/request-otp \
  -H "Content-Type: application/json" \
  -d '{"phone": "+2250700000003"}'
```

**Vérifier OTP (le code est dans les logs) :**
```bash
curl -X POST http://localhost:8000/api/v1/auth/verify-otp \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "+2250700000003",
    "code": "123456",
    "first_name": "Jean",
    "last_name": "Dupont"
  }'
```

Vous recevrez un **token** dans la réponse.

### 2. Authentification admin

```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@pressing.com",
    "password": "password"
  }'
```

### 3. Récupérer le catalogue

**Liste des services :**
```bash
curl http://localhost:8000/api/v1/services
```

**Types de vêtements :**
```bash
curl http://localhost:8000/api/v1/clothing-types
```

**Matrice des prix :**
```bash
curl http://localhost:8000/api/v1/prices/matrix
```

### 4. Créer une commande

```bash
curl -X POST http://localhost:8000/api/v1/orders \
  -H "Authorization: Bearer VOTRE_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "delivery_type": "home_delivery",
    "items": [
      {
        "service_id": 1,
        "clothing_type_id": 1,
        "quantity": 3
      }
    ],
    "special_instructions": "Attention aux boutons fragiles"
  }'
```

### 5. Payer une commande

```bash
curl -X POST http://localhost:8000/api/v1/orders/1/payment \
  -H "Authorization: Bearer VOTRE_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "payment_method": "cash"
  }'
```

---

## 📚 Endpoints disponibles (50+)

### Public
- `GET /api/v1/services` - Liste des services
- `GET /api/v1/clothing-types` - Types de vêtements
- `GET /api/v1/prices` - Prix
- `POST /api/v1/prices/calculate` - Calculer prix
- `GET /api/v1/promotions` - Promotions actives

### Authentifié
- `GET/POST /api/v1/orders` - Commandes
- `GET /api/v1/wallet` - Portefeuille
- `GET/POST /api/v1/addresses` - Adresses
- `POST /api/v1/orders/{id}/payment` - Payer
- `POST /api/v1/promotions/validate` - Valider promo

### Admin uniquement
- `POST /api/v1/services` - Créer service
- `PUT /api/v1/prices/{id}` - Modifier prix
- `POST /api/v1/promotions` - Créer promo
- `POST /api/v1/orders/{id}/update-status` - Changer statut
- `POST /api/v1/orders/{id}/assign-employee` - Assigner employé

---

## 📊 Données pré-remplies

### Services disponibles
1. Lavage simple (24h)
2. Lavage + Repassage (48h)
3. Repassage seul (24h)
4. Nettoyage à sec (72h)
5. Pressing express (6h)

### Types de vêtements
Chemise, Pantalon, Robe, Costume, Veste/Blazer, Jupe, T-shirt, Pull/Gilet, Manteau, Jean, Couverture, Drap, Rideau

### Tarifs (exemple)
- Chemise - Lavage simple : 500 FCFA
- Chemise - Lavage + Repassage : 1000 FCFA
- Costume - Nettoyage à sec : 3000 FCFA

---

## 🔜 Ce qui reste à faire

1. **Système de notifications** (SMS/Push)
2. **Dashboard Admin** (interface web)
3. **Système de livraison** (géolocalisation)
4. **API mobile complète** (endpoints livreur)
5. **Rapports et statistiques**
6. **Tests et documentation**

---

## 🐛 Logs et Debugging

**Voir les logs Laravel :**
```bash
tail -f storage/logs/laravel.log
```

**Voir les codes OTP dans les logs :**
Quand vous demandez un OTP, le code apparaît dans les logs (en développement).

---

## 📖 Documentation complète

- `SPECIFICATIONS.md` - Cahier des charges complet
- `DATABASE_SCHEMA.md` - Schéma de base de données détaillé
- `PROGRESS.md` - État d'avancement du projet
- `README.md` - Documentation générale

---

## 💡 Conseils

### Pour le développement mobile
Utilisez Postman ou Insomnia pour tester l'API avant d'intégrer dans l'app mobile.

### Pour l'intégration Mobile Money
Les structures sont prêtes dans `PaymentController`. Il suffit d'ajouter les credentials API dans `.env` et d'implémenter les méthodes.

### Pour les notifications
Le service `OtpService` montre comment envoyer des SMS. Il suffit d'intégrer un vrai provider SMS (Twilio, Vonage, Orange SMS API, etc.).

---

## ⚡ Performance

- Utiliser Redis pour le cache en production
- Activer les queues pour les emails/SMS
- Optimiser les requêtes avec `eager loading`

---

## 🎯 Prochaine étape recommandée

**Option 1 : Dashboard Admin** - Pour gérer visuellement les commandes  
**Option 2 : Notifications** - Pour informer les clients automatiquement  
**Option 3 : Livraison & GPS** - Pour tracker les livreurs

---

**Version actuelle :** 0.3.0 (Alpha)  
**Progression :** 50% ✅  
**Ready for:** Tests API, Développement mobile


