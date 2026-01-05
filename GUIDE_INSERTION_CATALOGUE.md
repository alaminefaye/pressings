# 🗄️ GUIDE - Insertion du catalogue dans la base de données

## 🎯 Objectif

Insérer les **services**, **types de vêtements** et **prix** dans votre base de données de production pour que l'application mobile puisse les afficher.

---

## 📋 Méthode 1 : Via phpMyAdmin (RECOMMANDÉ)

### Étape 1 : Connexion à phpMyAdmin
1. Connectez-vous à votre hébergeur
2. Accédez à **phpMyAdmin**
3. Sélectionnez votre base de données (celle du pressing)

### Étape 2 : Vérifier si les tables sont vides
1. Cliquez sur l'onglet **SQL**
2. Exécutez ces requêtes pour vérifier :
```sql
SELECT COUNT(*) as total FROM services;
SELECT COUNT(*) as total FROM clothing_types;
SELECT COUNT(*) as total FROM prices;
```

**Si les totaux sont à 0**, passez à l'étape 3.  
**Si les totaux sont > 0**, les données existent déjà ! Passez directement à l'étape 4 (test).

### Étape 3 : Exécuter le script SQL
1. Ouvrez le fichier : `/Users/Zhuanz/Desktop/projets/web/pressing/SQL_INSERT_CATALOGUE.sql`
2. **Copiez tout le contenu du fichier**
3. Retournez dans phpMyAdmin
4. Cliquez sur l'onglet **SQL**
5. **Collez le contenu** dans la zone de texte
6. Cliquez sur **Exécuter**

### Étape 4 : Vérification
Vous devriez voir :
- ✅ **5 services** insérés
- ✅ **13 types de vêtements** insérés
- ✅ **65 prix** insérés (5 services × 13 types de vêtements)

Vérifiez en cliquant sur les tables dans le menu de gauche :
- `services` → 5 lignes
- `clothing_types` → 13 lignes
- `prices` → 65 lignes

---

## 📋 Méthode 2 : Via SSH (si vous avez accès)

### Connexion SSH
```bash
ssh votre_utilisateur@pressings.universaltechnologiesafrica.com
cd chemin/vers/votre/projet
```

### Exécuter les seeders
```bash
# Seeder des services
php artisan db:seed --class=ServiceSeeder

# Seeder des types de vêtements
php artisan db:seed --class=ClothingTypeSeeder

# Seeder des prix
php artisan db:seed --class=PriceSeeder
```

### Ou tous en une fois
```bash
php artisan db:seed
```

---

## 🧪 Test dans l'application mobile

### Après l'insertion des données :

1. **Redémarrez l'application Flutter** (appuyez sur `R` dans le terminal)

2. **Naviguez vers "Nouvelle commande"**

3. **Cliquez sur "Ajouter"**

4. **Vous devriez maintenant voir :**

   **Type de vêtement** (liste horizontale avec icônes) :
   - 👔 Chemise
   - 👖 Pantalon
   - 👗 Robe
   - 🤵 Costume
   - 🧥 Veste/Blazer
   - 👘 Jupe
   - 👕 T-shirt
   - 🧶 Pull/Gilet
   - 🧥 Manteau
   - 👖 Jean
   - 🛏️ Couverture
   - 🛏️ Drap
   - 🪟 Rideau

   **Service** (liste verticale) :
   - Lavage simple (24h)
   - Lavage + Repassage (48h)
   - Repassage seul (24h)
   - Nettoyage à sec (72h)
   - Pressing express (6h)

5. **Sélectionnez un type, un service et une quantité**

6. **Cliquez sur "Ajouter"**

7. ✅ **L'article devrait être ajouté à votre commande !**

---

## 🔍 Vérification des données via API

Vous pouvez aussi tester directement les endpoints de l'API :

### Dans votre navigateur :

**Services :**
```
http://pressings.universaltechnologiesafrica.com/api/services
```

**Types de vêtements :**
```
http://pressings.universaltechnologiesafrica.com/api/clothing-types
```

**Prix :**
```
http://pressings.universaltechnologiesafrica.com/api/prices
```

Vous devriez voir une réponse JSON avec :
```json
{
  "success": true,
  "data": [
    { ... },
    { ... }
  ]
}
```

---

## ❌ En cas de problème

### Problème 1 : Erreur "Duplicate entry"
**Cause** : Les données existent déjà dans la base.  
**Solution** : Les données sont déjà là ! Testez simplement l'application.

### Problème 2 : Modal toujours vide après insertion
**Causes possibles** :
1. Le script SQL n'a pas été exécuté correctement
2. L'application Flutter n'a pas été redémarrée
3. Il y a une erreur de connexion API

**Solutions** :
1. Vérifiez que les données sont bien dans les tables (voir Étape 4)
2. Redémarrez l'app Flutter (appuyez sur `R`)
3. Vérifiez les logs de l'API dans le backend
4. Testez les endpoints dans le navigateur (voir ci-dessus)

### Problème 3 : Erreur lors de l'exécution SQL
**Cause** : Possible conflit avec des données existantes ou des IDs différents.  
**Solution** : 
1. Supprimez d'abord les données existantes :
```sql
DELETE FROM prices;
DELETE FROM services;
DELETE FROM clothing_types;
```
2. Réexécutez le script `SQL_INSERT_CATALOGUE.sql`

---

## 📊 Détails des données insérées

### Services (5)
| ID | Nom | Durée |
|----|-----|-------|
| 1 | Lavage simple | 24h |
| 2 | Lavage + Repassage | 48h |
| 3 | Repassage seul | 24h |
| 4 | Nettoyage à sec | 72h |
| 5 | Pressing express | 6h |

### Types de vêtements (13)
| ID | Nom | Icône |
|----|-----|-------|
| 1 | Chemise | 👔 |
| 2 | Pantalon | 👖 |
| 3 | Robe | 👗 |
| 4 | Costume | 🤵 |
| 5 | Veste/Blazer | 🧥 |
| 6 | Jupe | 👘 |
| 7 | T-shirt | 👕 |
| 8 | Pull/Gilet | 🧶 |
| 9 | Manteau | 🧥 |
| 10 | Jean | 👖 |
| 11 | Couverture | 🛏️ |
| 12 | Drap | 🛏️ |
| 13 | Rideau | 🪟 |

### Prix (65 = 5 services × 13 types)
Exemples de prix (en FCFA) :
- Chemise + Lavage simple : **500 FCFA**
- Chemise + Lavage + Repassage : **1000 FCFA**
- Costume + Nettoyage à sec : **3000 FCFA**
- T-shirt + Lavage simple : **300 FCFA**

*Vous pouvez modifier ces prix directement dans la base de données après insertion.*

---

## 📍 Emplacements des fichiers

**Script SQL** :  
`/Users/Zhuanz/Desktop/projets/web/pressing/SQL_INSERT_CATALOGUE.sql`

**Seeders Laravel** :  
`/Users/Zhuanz/Desktop/projets/web/pressing/database/seeders/`
- `ServiceSeeder.php`
- `ClothingTypeSeeder.php`
- `PriceSeeder.php`

---

## 🎯 Résumé des étapes

1. ✅ Ouvrir phpMyAdmin
2. ✅ Sélectionner votre base de données
3. ✅ Aller dans l'onglet SQL
4. ✅ Copier-coller le contenu de `SQL_INSERT_CATALOGUE.sql`
5. ✅ Exécuter
6. ✅ Vérifier les données (5 services, 13 types, 65 prix)
7. ✅ Redémarrer l'app Flutter
8. ✅ Tester l'ajout d'article

---

## ✨ Après l'insertion

Votre application sera **totalement fonctionnelle** pour :
- ✅ Afficher les services disponibles
- ✅ Afficher les types de vêtements
- ✅ Calculer automatiquement les prix
- ✅ Créer des commandes complètes
- ✅ Gérer le catalogue depuis le dashboard admin

🎉 **Votre pressing est opérationnel !**

