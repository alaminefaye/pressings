# 🚀 CHOISIR VOTRE MÉTHODE DE DÉPLOIEMENT

## ❌ Problème actuel

L'application mobile affiche : **"Call to undefined method createToken()"**

Cause : Laravel Sanctum n'est pas installé sur le serveur de production.

---

## ✅ 3 MÉTHODES DE DÉPLOIEMENT

### 📊 Méthode 1 : Accès SSH + Git (⭐ RECOMMANDÉ)

**Prérequis :**
- Accès SSH au serveur
- Git installé sur le serveur
- Projet sous contrôle de version

**Étapes :**

```bash
# 1. Sur votre machine locale
cd /Users/Zhuanz/Desktop/projets/web/pressing
git add .
git commit -m "Add Laravel Sanctum"
git push origin main

# 2. Sur le serveur de production (via SSH)
cd /path/to/your/project
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:clear
php artisan cache:clear
```

**Fichier à utiliser :** `COMMANDES_PRODUCTION.sh`

**Durée :** ~5 minutes

---

### 🗂️ Méthode 2 : FTP + SSH

**Prérequis :**
- Accès FTP (FileZilla, Cyberduck, etc.)
- Accès SSH au serveur

**Étapes :**

1. **Via FTP, uploader ces fichiers :**
   - `app/Models/User.php`
   - `composer.json`
   - `composer.lock`  
   - `config/sanctum.php` (nouveau)
   - `database/migrations/2026_01_05_193454_create_personal_access_tokens_table.php` (nouveau)

2. **Via SSH, exécuter :**
```bash
cd /path/to/your/project
composer install --no-dev
php artisan migrate --force
php artisan config:clear
php artisan cache:clear
```

**Fichiers à utiliser :**
- `DEPLOIEMENT_SANCTUM.md` (guide complet)
- `COMMANDES_PRODUCTION.sh` (commandes SSH)

**Durée :** ~10 minutes

---

### 🖥️ Méthode 3 : FTP + phpMyAdmin (Sans SSH)

**Prérequis :**
- Accès FTP
- Accès phpMyAdmin

**Étapes :**

1. **Via FTP, uploader tous les fichiers du dossier `vendor/laravel/sanctum/`**

2. **Via phpMyAdmin :**
   - Ouvrir la base de données
   - Onglet "SQL"
   - Copier/coller le contenu de `SQL_SANCTUM_PRODUCTION.sql`
   - Cliquer "Exécuter"

3. **Via FTP, uploader :**
   - `app/Models/User.php`
   - `composer.json`
   - `composer.lock`
   - `config/sanctum.php`

4. **Via votre hébergeur :**
   - Chercher "Clear Cache" ou "Optimize"
   - Ou créer un fichier PHP temporaire :

```php
<?php
// clear-cache.php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->call('config:clear');
$kernel->call('cache:clear');
echo "Caches cleared!";
```

**Fichiers à utiliser :**
- `SQL_SANCTUM_PRODUCTION.sql`
- `DEPLOIEMENT_SANCTUM.md`

**Durée :** ~15 minutes

---

## 📁 FICHIERS À VOTRE DISPOSITION

| Fichier | Description | Pour qui ? |
|---------|-------------|------------|
| `DEPLOIEMENT_SANCTUM.md` | Guide complet | Tous |
| `COMMANDES_PRODUCTION.sh` | Script automatique | Méthode 1 & 2 |
| `SQL_SANCTUM_PRODUCTION.sql` | Migration SQL manuelle | Méthode 3 |
| `CHOISIR_METHODE_DEPLOIEMENT.md` | Ce fichier | Tous |

---

## 🎯 QUELLE MÉTHODE CHOISIR ?

| Critère | Méthode 1 | Méthode 2 | Méthode 3 |
|---------|-----------|-----------|-----------|
| **Simplicité** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐ |
| **Rapidité** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐ |
| **Sécurité** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐ |
| **Accès SSH requis** | ✅ | ✅ | ❌ |
| **Git requis** | ✅ | ❌ | ❌ |
| **Risque d'erreur** | Faible | Moyen | Élevé |

**Recommandation : Méthode 1** si vous avez Git, sinon **Méthode 2**

---

## ✅ APRÈS LE DÉPLOIEMENT

### Test 1 : Vérifier l'API

```bash
curl -X POST http://pressings.universaltechnologiesafrica.com/api/auth/send-otp \
  -H "Content-Type: application/json" \
  -d '{"phone":"0707070701"}'

# ✅ Doit retourner : {"success":true,...}
```

### Test 2 : Vérifier la base de données

Dans phpMyAdmin :
```sql
SHOW TABLES LIKE 'personal_access_tokens';
```

✅ Doit afficher la table

### Test 3 : Tester l'app mobile

1. Ouvrir l'application mobile
2. Entrer : `0707070701`
3. Cliquer "Recevoir le code OTP"
4. ✅ Plus d'erreur "createToken()"
5. Entrer le code OTP
6. ✅ Connexion réussie !

---

## 🆘 BESOIN D'AIDE ?

### Option A : Vous avez Git + SSH
→ Utilisez `COMMANDES_PRODUCTION.sh`

### Option B : Vous avez seulement SSH
→ Suivez `DEPLOIEMENT_SANCTUM.md` (Méthode 2)

### Option C : Pas d'accès SSH
→ Utilisez `SQL_SANCTUM_PRODUCTION.sql` + FTP

### Option D : Totalement bloqué
→ Contactez votre hébergeur ou administrateur système avec ce message :

```
Bonjour,

Je dois installer Laravel Sanctum sur mon application.
Pouvez-vous exécuter ces commandes sur le serveur ?

cd /path/to/my/project
composer require laravel/sanctum
php artisan migrate
php artisan config:clear

Merci !
```

---

## 📞 SUPPORT

**Fichiers de référence :**
- Tous les fichiers sont dans `/Users/Zhuanz/Desktop/projets/web/pressing/`
- Documentation Laravel Sanctum : https://laravel.com/docs/sanctum

---

## ✨ BON DÉPLOIEMENT !

Une fois déployé, votre application mobile fonctionnera parfaitement ! 🚀

