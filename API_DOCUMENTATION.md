# 📚 DOCUMENTATION API - Pressing App

**Version:** 1.0.0  
**Base URL:** `http://localhost:8000/api/v1`  
**Format:** JSON  
**Authentification:** Bearer Token (Laravel Sanctum)

---

## 📋 Table des matières

1. [Authentification](#authentification)
2. [Services & Catalogue](#services--catalogue)
3. [Commandes](#commandes)
4. [Paiements & Portefeuille](#paiements--portefeuille)
5. [Livraisons](#livraisons)
6. [Notifications](#notifications)
7. [Statistiques](#statistiques)
8. [Codes d'erreur](#codes-derreur)

---

## 🔐 Authentification

### 1. Demander un code OTP
**Endpoint:** `POST /auth/request-otp`

**Body:**
```json
{
  "phone": "+2250700000000"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Code OTP envoyé avec succès",
  "expires_in": 300
}
```

### 2. Vérifier OTP et S'inscrire/Connecter
**Endpoint:** `POST /auth/verify-otp`

**Body:**
```json
{
  "phone": "+2250700000000",
  "code": "123456",
  "first_name": "Jean",
  "last_name": "Dupont"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Authentification réussie",
  "data": {
    "user": {
      "id": 1,
      "phone": "+2250700000000",
      "first_name": "Jean",
      "last_name": "Dupont",
      "role": "client"
    },
    "token": "1|xxxxxxxxxxxx"
  }
}
```

### 3. Connexion Email/Password (Admin/Employés)
**Endpoint:** `POST /auth/login`

**Body:**
```json
{
  "email": "admin@pressing.com",
  "password": "password"
}
```

### 4. Déconnexion
**Endpoint:** `POST /auth/logout`  
**Auth:** Required

### 5. Profil Utilisateur
**Endpoint:** `GET /auth/me`  
**Auth:** Required

**Response:**
```json
{
  "success": true,
  "data": {
    "user": {...},
    "wallet": {...},
    "loyalty_points": {...}
  }
}
```

### 6. Modifier Profil
**Endpoint:** `PUT /auth/profile`  
**Auth:** Required

**Body:**
```json
{
  "first_name": "Jean",
  "last_name": "Martin",
  "email": "jean@example.com"
}
```

---

## 🧺 Services & Catalogue

### 1. Liste des Services
**Endpoint:** `GET /services`

**Query Parameters:**
- `active` (boolean) - Filtrer par statut actif
- `search` (string) - Recherche par nom

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Lavage simple",
      "duration_hours": 24,
      "is_active": true
    }
  ]
}
```

### 2. Types de Vêtements
**Endpoint:** `GET /clothing-types`

### 3. Liste des Prix
**Endpoint:** `GET /prices`

**Query Parameters:**
- `service_id` (int)
- `clothing_type_id` (int)

### 4. Matrice des Prix
**Endpoint:** `GET /prices/matrix`

**Response:** Tous les prix groupés par service

### 5. Calculer Prix d'une Commande
**Endpoint:** `POST /prices/calculate`

**Body:**
```json
{
  "items": [
    {
      "service_id": 1,
      "clothing_type_id": 1,
      "quantity": 3
    },
    {
      "service_id": 2,
      "clothing_type_id": 5,
      "quantity": 2
    }
  ]
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "items": [
      {
        "service_id": 1,
        "clothing_type_id": 1,
        "quantity": 3,
        "unit_price": 500,
        "subtotal": 1500
      }
    ],
    "subtotal": 3500
  }
}
```

### 6. Promotions Actives
**Endpoint:** `GET /promotions`

**Query Parameters:**
- `available` (boolean) - Seulement les promos valides

### 7. Valider Code Promo
**Endpoint:** `POST /promotions/validate`  
**Auth:** Required

**Body:**
```json
{
  "code": "PROMO2024",
  "order_amount": 5000
}
```

---

## 📦 Commandes

### 1. Créer une Commande
**Endpoint:** `POST /orders`  
**Auth:** Required

**Body:**
```json
{
  "delivery_type": "home_delivery",
  "delivery_address_id": 1,
  "pickup_date": "2024-01-10 10:00:00",
  "delivery_date": "2024-01-12 15:00:00",
  "items": [
    {
      "service_id": 1,
      "clothing_type_id": 1,
      "quantity": 3,
      "notes": "Attention aux boutons"
    }
  ],
  "special_instructions": "Livraison après 14h SVP",
  "promotion_code": "PROMO2024"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Commande créée avec succès",
  "data": {
    "id": 1,
    "order_number": "ORD-2024-0001",
    "status": "pending",
    "subtotal": 1500,
    "discount": 150,
    "delivery_fee": 1000,
    "total": 2350,
    "items": [...]
  }
}
```

### 2. Liste des Commandes
**Endpoint:** `GET /orders`  
**Auth:** Required

**Query Parameters:**
- `status` (string) - Filtrer par statut
- `search` (string) - Rechercher par numéro
- `from_date` (date)
- `to_date` (date)

### 3. Détails d'une Commande
**Endpoint:** `GET /orders/{id}`  
**Auth:** Required

### 4. Annuler une Commande
**Endpoint:** `POST /orders/{id}/cancel`  
**Auth:** Required

**Body:**
```json
{
  "reason": "Je ne suis plus disponible"
}
```

### 5. Statistiques Commandes
**Endpoint:** `GET /orders/statistics`  
**Auth:** Required

---

## 💳 Paiements & Portefeuille

### 1. Payer une Commande
**Endpoint:** `POST /orders/{id}/payment`  
**Auth:** Required

**Body:**
```json
{
  "payment_method": "mobile_money",
  "payment_provider": "orange"
}
```

**Payment Methods:**
- `cash` - Paiement en espèces
- `mobile_money` - Orange Money, MTN, Wave
- `wallet` - Portefeuille interne

### 2. Historique des Paiements
**Endpoint:** `GET /payments/history`  
**Auth:** Required

### 3. Mon Portefeuille
**Endpoint:** `GET /wallet`  
**Auth:** Required

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "balance": 5000,
    "transactions": [...]
  }
}
```

### 4. Transactions du Portefeuille
**Endpoint:** `GET /wallet/transactions`  
**Auth:** Required

**Query Parameters:**
- `type` (string) - credit / debit
- `from_date` (date)
- `to_date` (date)

### 5. Recharger le Portefeuille
**Endpoint:** `POST /wallet/add-funds`  
**Auth:** Required

**Body:**
```json
{
  "amount": 10000,
  "payment_method": "mobile_money",
  "payment_provider": "orange"
}
```

### 6. Statistiques Portefeuille
**Endpoint:** `GET /wallet/statistics`  
**Auth:** Required

---

## 🚚 Livraisons

### 1. Liste des Livraisons
**Endpoint:** `GET /deliveries`  
**Auth:** Required

**Query Parameters:**
- `status` (string)
- `type` (string) - pickup / delivery
- `date` (date)

### 2. Détails Livraison
**Endpoint:** `GET /deliveries/{id}`  
**Auth:** Required

### 3. Position du Livreur
**Endpoint:** `GET /deliveries/{id}/driver-location`  
**Auth:** Required

**Response:**
```json
{
  "success": true,
  "data": {
    "location": {
      "latitude": 5.316667,
      "longitude": -4.033333,
      "accuracy": 10.5,
      "created_at": "2024-01-04 14:30:00"
    },
    "driver": {...},
    "delivery": {...}
  }
}
```

### 4. Historique des Positions
**Endpoint:** `GET /deliveries/{id}/location-history`  
**Auth:** Required

---

## 🚗 Endpoints Livreur

### 1. Démarrer une Livraison
**Endpoint:** `POST /deliveries/{id}/start`  
**Auth:** Required (Driver)

### 2. Compléter une Livraison
**Endpoint:** `POST /deliveries/{id}/complete`  
**Auth:** Required (Driver)

**Body (Multipart):**
```
signature: "base64_encoded_signature"
photo: File
notes: "Client absent, livré au voisin"
```

### 3. Mettre à Jour la Position
**Endpoint:** `POST /deliveries/{id}/update-location`  
**Auth:** Required (Driver)

**Body:**
```json
{
  "latitude": 5.316667,
  "longitude": -4.033333,
  "accuracy": 10.5,
  "speed": 45.5,
  "heading": 180.0
}
```

### 4. Statistiques Livreur
**Endpoint:** `GET /driver/statistics`  
**Auth:** Required (Driver)

---

## 🔔 Notifications

### 1. Mes Notifications
**Endpoint:** `GET /notifications`  
**Auth:** Required

**Query Parameters:**
- `unread` (boolean)
- `type` (string)

### 2. Nombre de Non-lues
**Endpoint:** `GET /notifications/unread-count`  
**Auth:** Required

### 3. Marquer comme Lue
**Endpoint:** `POST /notifications/{id}/mark-as-read`  
**Auth:** Required

### 4. Tout Marquer comme Lu
**Endpoint:** `POST /notifications/mark-all-as-read`  
**Auth:** Required

### 5. Supprimer une Notification
**Endpoint:** `DELETE /notifications/{id}`  
**Auth:** Required

---

## 📊 Statistiques (Admin uniquement)

### 1. Vue d'Ensemble
**Endpoint:** `GET /statistics/overview`  
**Auth:** Required (Admin/Employee)

**Query Parameters:**
- `period` (string) - today, week, month, year

**Response:**
```json
{
  "success": true,
  "data": {
    "orders": {
      "total": 150,
      "pending": 10,
      "in_progress": 25,
      "ready": 5,
      "delivered": 110
    },
    "revenue": {
      "total": 1500000,
      "paid": 1400000,
      "pending": 100000
    },
    "customers": {
      "total": 250,
      "new": 15,
      "active": 80
    },
    "deliveries": {
      "pending": 8,
      "in_progress": 3,
      "completed": 120
    }
  }
}
```

### 2. Statistiques de Revenus
**Endpoint:** `GET /statistics/revenue`  
**Auth:** Required (Admin/Employee)

### 3. Statistiques Commandes
**Endpoint:** `GET /statistics/orders`  
**Auth:** Required (Admin/Employee)

### 4. Statistiques Clients
**Endpoint:** `GET /statistics/customers`  
**Auth:** Required (Admin/Employee)

### 5. Statistiques Livraisons
**Endpoint:** `GET /statistics/deliveries`  
**Auth:** Required (Admin/Employee)

### 6. Export Rapport
**Endpoint:** `GET /statistics/export`  
**Auth:** Required (Admin/Employee)

**Query Parameters:**
- `period` (string)
- `format` (string) - json, csv, pdf

---

## 🎯 Endpoints Admin Uniquement

### Gestion Services
- `POST /services` - Créer service
- `PUT /services/{id}` - Modifier
- `DELETE /services/{id}` - Supprimer

### Gestion Types de Vêtements
- `POST /clothing-types` - Créer
- `PUT /clothing-types/{id}` - Modifier
- `DELETE /clothing-types/{id}` - Supprimer

### Gestion Prix
- `POST /prices` - Créer prix
- `PUT /prices/{id}` - Modifier
- `DELETE /prices/{id}` - Supprimer
- `POST /prices/bulk-update` - Mise à jour en masse

### Gestion Promotions
- `POST /promotions` - Créer promotion
- `GET /promotions/{id}` - Détails
- `PUT /promotions/{id}` - Modifier
- `DELETE /promotions/{id}` - Supprimer
- `GET /promotions/{id}/stats` - Statistiques

### Gestion Commandes
- `POST /orders/{id}/update-status` - Changer statut
- `POST /orders/{id}/assign-employee` - Assigner employé

### Gestion Paiements
- `POST /orders/{id}/confirm-cash-payment` - Confirmer espèces
- `POST /orders/{id}/refund` - Remboursement

### Gestion Livraisons
- `POST /deliveries/{id}/assign-driver` - Assigner livreur
- `GET /drivers/available` - Livreurs disponibles

---

## ⚠️ Codes d'Erreur

### Codes HTTP
- `200` - OK
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

### Format d'Erreur
```json
{
  "success": false,
  "message": "Description de l'erreur",
  "errors": {
    "field": ["Message d'erreur"]
  }
}
```

---

## 🔄 Workflow des Statuts

### Commande
```
pending → received → washing → ironing → ready → in_delivery → delivered
                ↓
            cancelled (à tout moment)
```

### Livraison
```
pending → assigned → in_progress → completed
```

### Paiement
```
pending → completed
    ↓
  failed / refunded
```

---

## 📱 Intégration Mobile

### Headers Requis
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer {token}
```

### Gestion des Tokens
- Les tokens sont générés lors de l'authentification
- Aucune expiration par défaut (peut être configurée)
- Stockez le token de manière sécurisée (Keychain/Keystore)

### Pagination
La plupart des endpoints retournent des résultats paginés :
```json
{
  "data": [...],
  "current_page": 1,
  "last_page": 5,
  "per_page": 15,
  "total": 73
}
```

---

## 🛠️ Environnement de Test

**Base URL:** `http://localhost:8000/api/v1`

**Comptes de test:**
- Admin: admin@pressing.com / password
- Employé: employee@pressing.com / password
- Livreur: driver@pressing.com / password
- Client: +2250700000003 (OTP)

---

## 📞 Support

Pour toute question ou problème :
- Consultez la documentation complète dans `SPECIFICATIONS.md`
- Vérifiez les logs Laravel : `storage/logs/laravel.log`
- Mode debug: Activé par défaut en développement

**Version:** 1.0.0  
**Dernière mise à jour:** 4 Janvier 2026


