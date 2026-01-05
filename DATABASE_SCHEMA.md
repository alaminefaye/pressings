# 🗄️ SCHÉMA DE BASE DE DONNÉES - APPLICATION PRESSING

## Vue d'ensemble

Ce document décrit la structure complète de la base de données pour l'application de pressing.

---

## 📊 TABLES PRINCIPALES

### 1. users
Stocke tous les utilisateurs (clients, employés, livreurs, admin)

```sql
- id (bigint, PK)
- phone (varchar, unique) - Téléphone principal
- email (varchar, nullable)
- password (varchar, nullable) - Pour admin/employés
- first_name (varchar)
- last_name (varchar)
- role (enum: 'client', 'admin', 'employee', 'driver')
- profile_photo (varchar, nullable)
- is_active (boolean, default: true)
- phone_verified_at (timestamp, nullable)
- remember_token (varchar, nullable)
- created_at (timestamp)
- updated_at (timestamp)
- deleted_at (timestamp, nullable) - Soft delete
```

**Indexes:**
- phone (unique)
- email (unique)
- role

---

### 2. addresses
Adresses des clients (domicile, travail, etc.)

```sql
- id (bigint, PK)
- user_id (bigint, FK -> users)
- label (varchar) - "Domicile", "Bureau", etc.
- address_line (text)
- city (varchar)
- postal_code (varchar, nullable)
- latitude (decimal, nullable)
- longitude (decimal, nullable)
- is_default (boolean, default: false)
- created_at (timestamp)
- updated_at (timestamp)
```

**Indexes:**
- user_id
- is_default

---

### 3. otp_codes
Codes OTP pour l'authentification

```sql
- id (bigint, PK)
- phone (varchar)
- code (varchar)
- expires_at (timestamp)
- verified_at (timestamp, nullable)
- created_at (timestamp)
```

**Indexes:**
- phone
- code
- expires_at

---

### 4. clothing_types
Types de vêtements

```sql
- id (bigint, PK)
- name (varchar) - "Chemise", "Pantalon", "Robe", etc.
- slug (varchar, unique)
- description (text, nullable)
- icon (varchar, nullable)
- is_active (boolean, default: true)
- created_at (timestamp)
- updated_at (timestamp)
```

---

### 5. services
Types de services offerts

```sql
- id (bigint, PK)
- name (varchar) - "Lavage simple", "Lavage + repassage", etc.
- slug (varchar, unique)
- description (text, nullable)
- duration_hours (integer) - Durée estimée en heures
- is_active (boolean, default: true)
- created_at (timestamp)
- updated_at (timestamp)
```

---

### 6. prices
Tarification par service et type de vêtement

```sql
- id (bigint, PK)
- service_id (bigint, FK -> services)
- clothing_type_id (bigint, FK -> clothing_types)
- price (decimal)
- is_active (boolean, default: true)
- created_at (timestamp)
- updated_at (timestamp)
```

**Indexes:**
- service_id
- clothing_type_id
- Unique: (service_id, clothing_type_id)

---

### 7. orders
Commandes

```sql
- id (bigint, PK)
- order_number (varchar, unique) - Ex: "ORD-2024-0001"
- client_id (bigint, FK -> users)
- employee_id (bigint, FK -> users, nullable)
- delivery_type (enum: 'pickup', 'home_delivery')
-
- pickup_address_id (bigint, FK -> addresses, nullable)
- delivery_address_id (bigint, FK -> addresses, nullable)
- pickup_date (datetime, nullable)
- delivery_date (datetime, nullable)
- 
- status (enum: 'pending', 'received', 'washing', 'ironing', 'ready', 'in_delivery', 'delivered', 'cancelled')
- 
- subtotal (decimal)
- discount (decimal, default: 0)
- delivery_fee (decimal, default: 0)
- total (decimal)
- 
- special_instructions (text, nullable)
-
- created_at (timestamp)
- updated_at (timestamp)
- deleted_at (timestamp, nullable)
```

**Indexes:**
- order_number (unique)
- client_id
- employee_id
- status
- created_at

---

### 8. order_items
Articles d'une commande

```sql
- id (bigint, PK)
- order_id (bigint, FK -> orders)
- service_id (bigint, FK -> services)
- clothing_type_id (bigint, FK -> clothing_types)
- quantity (integer)
- unit_price (decimal)
- subtotal (decimal)
- notes (text, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

**Indexes:**
- order_id
- service_id
- clothing_type_id

---

### 9. order_status_history
Historique des changements de statut

```sql
- id (bigint, PK)
- order_id (bigint, FK -> orders)
- user_id (bigint, FK -> users) - Qui a changé le statut
- old_status (varchar, nullable)
- new_status (varchar)
- comment (text, nullable)
- created_at (timestamp)
```

**Indexes:**
- order_id
- created_at

---

### 10. payments
Paiements

```sql
- id (bigint, PK)
- order_id (bigint, FK -> orders)
- payment_method (enum: 'cash', 'mobile_money', 'wallet')
- payment_provider (varchar, nullable) - "Orange Money", "MTN", "Wave"
- amount (decimal)
- status (enum: 'pending', 'completed', 'failed', 'refunded')
- transaction_id (varchar, nullable) - ID externe
- paid_at (timestamp, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

**Indexes:**
- order_id
- status
- transaction_id

---

### 11. wallets
Portefeuille interne des clients

```sql
- id (bigint, PK)
- user_id (bigint, FK -> users, unique)
- balance (decimal, default: 0)
- created_at (timestamp)
- updated_at (timestamp)
```

---

### 12. wallet_transactions
Transactions du portefeuille

```sql
- id (bigint, PK)
- wallet_id (bigint, FK -> wallets)
- type (enum: 'credit', 'debit')
- amount (decimal)
- description (text)
- reference (varchar, nullable)
- balance_after (decimal)
- created_at (timestamp)
```

**Indexes:**
- wallet_id
- created_at

---

### 13. deliveries
Livraisons

```sql
- id (bigint, PK)
- order_id (bigint, FK -> orders, unique)
- driver_id (bigint, FK -> users, nullable)
- type (enum: 'pickup', 'delivery')
-
- address_id (bigint, FK -> addresses)
- scheduled_at (datetime)
- started_at (datetime, nullable)
- completed_at (datetime, nullable)
-
- status (enum: 'pending', 'assigned', 'in_progress', 'completed', 'failed')
-
- signature (text, nullable) - Base64 de la signature
- photo (varchar, nullable) - Preuve de livraison
- notes (text, nullable)
- 
- created_at (timestamp)
- updated_at (timestamp)
```

**Indexes:**
- order_id
- driver_id
- status

---

### 14. driver_locations
Géolocalisation des livreurs en temps réel

```sql
- id (bigint, PK)
- driver_id (bigint, FK -> users)
- delivery_id (bigint, FK -> deliveries, nullable)
- latitude (decimal)
- longitude (decimal)
- accuracy (decimal, nullable)
- speed (decimal, nullable)
- heading (decimal, nullable)
- created_at (timestamp)
```

**Indexes:**
- driver_id
- delivery_id
- created_at

---

### 15. notifications
Notifications

```sql
- id (bigint, PK)
- user_id (bigint, FK -> users)
- type (varchar) - "order_received", "order_ready", etc.
- title (varchar)
- message (text)
- data (json, nullable) - Données supplémentaires
- read_at (timestamp, nullable)
- sent_at (timestamp, nullable)
- created_at (timestamp)
```

**Indexes:**
- user_id
- type
- read_at

---

### 16. sms_logs
Historique des SMS envoyés

```sql
- id (bigint, PK)
- phone (varchar)
- message (text)
- type (varchar) - "otp", "notification", etc.
- status (enum: 'pending', 'sent', 'failed')
- provider_response (json, nullable)
- sent_at (timestamp, nullable)
- created_at (timestamp)
```

**Indexes:**
- phone
- type
- status
- created_at

---

### 17. loyalty_points
Points de fidélité

```sql
- id (bigint, PK)
- user_id (bigint, FK -> users)
- points (integer, default: 0)
- total_earned (integer, default: 0)
- total_spent (integer, default: 0)
- updated_at (timestamp)
```

---

### 18. loyalty_transactions
Transactions de points

```sql
- id (bigint, PK)
- user_id (bigint, FK -> users)
- order_id (bigint, FK -> orders, nullable)
- type (enum: 'earned', 'spent', 'expired')
- points (integer)
- description (text)
- balance_after (integer)
- created_at (timestamp)
```

**Indexes:**
- user_id
- order_id
- created_at

---

### 19. promotions
Promotions et réductions

```sql
- id (bigint, PK)
- code (varchar, unique, nullable)
- name (varchar)
- description (text, nullable)
- type (enum: 'percentage', 'fixed_amount')
- value (decimal)
- min_order_amount (decimal, nullable)
- max_discount (decimal, nullable)
- usage_limit (integer, nullable)
- usage_count (integer, default: 0)
- starts_at (datetime)
- expires_at (datetime, nullable)
- is_active (boolean, default: true)
- created_at (timestamp)
- updated_at (timestamp)
```

---

### 20. promotion_usage
Utilisation des promotions

```sql
- id (bigint, PK)
- promotion_id (bigint, FK -> promotions)
- user_id (bigint, FK -> users)
- order_id (bigint, FK -> orders)
- discount_amount (decimal)
- created_at (timestamp)
```

---

### 21. settings
Paramètres de l'application

```sql
- id (bigint, PK)
- key (varchar, unique)
- value (text)
- type (varchar) - "string", "number", "boolean", "json"
- description (text, nullable)
- updated_at (timestamp)
```

---

## 🔗 RELATIONS PRINCIPALES

1. **User -> Orders** : Un utilisateur peut avoir plusieurs commandes
2. **User -> Addresses** : Un utilisateur peut avoir plusieurs adresses
3. **Order -> OrderItems** : Une commande contient plusieurs articles
4. **Order -> Payment** : Une commande a un paiement
5. **Order -> Delivery** : Une commande peut avoir une livraison
6. **Service + ClothingType -> Price** : Tarification par combinaison
7. **User -> Wallet** : Un client a un portefeuille
8. **Driver -> Deliveries** : Un livreur gère plusieurs livraisons

---

## 📈 INDEXES RECOMMANDÉS

Pour optimiser les performances, créer des index sur :
- Clés étrangères
- Champs de recherche fréquents (phone, email, order_number)
- Champs de tri (created_at, status)
- Champs de filtrage (role, is_active)

---

## 🔒 SÉCURITÉ

- Utiliser soft deletes pour les données sensibles
- Chiffrer les données de paiement
- Logger toutes les modifications importantes
- Implémenter des policies Laravel pour l'accès aux données

