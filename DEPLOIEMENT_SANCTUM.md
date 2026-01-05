# 🚀 DÉPLOIEMENT SANCTUM SUR SERVEUR DE PRODUCTION

## ✅ CE QUI A ÉTÉ CORRIGÉ EN LOCAL

1. ✅ Installé Laravel Sanctum
2. ✅ Ajouté le trait `HasApiTokens` au modèle User
3. ✅ Créé la table `personal_access_tokens`
4. ✅ Publié la configuration Sanctum

---

## 📦 FICHIERS MODIFIÉS À DÉPLOYER

### 1. Modèle User
**Fichier :** `app/Models/User.php`

Changements :
```php
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;
    // ...
}
```

### 2. Composer
**Fichier :** `composer.json`

Nouveau package ajouté :
```json
"laravel/sanctum": "^4.2"
```

### 3. Migration
**Nouveau fichier :** `database/migrations/2026_01_05_193454_create_personal_access_tokens_table.php`

### 4. Configuration
**Nouveau fichier :** `config/sanctum.php`

---

## 🚀 ÉTAPES DE DÉPLOIEMENT

### Option 1 : Via Git (RECOMMANDÉ)

```bash
# Sur votre machine locale
cd /Users/Zhuanz/Desktop/projets/web/pressing

# Commit des changements
git add .
git commit -m "Add Laravel Sanctum for API authentication"
git push origin main

# Sur le serveur de production
cd /path/to/your/project
git pull origin main

# Installer Sanctum
composer install --no-dev --optimize-autoloader

# Exécuter les migrations
php artisan migrate --force

# Nettoyer les caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### Option 2 : Via FTP/SFTP

1. **Uploader les fichiers modifiés :**
   - `app/Models/User.php`
   - `composer.json`
   - `composer.lock`
   - `config/sanctum.php`
   - `database/migrations/2026_01_05_193454_create_personal_access_tokens_table.php`

2. **Connectez-vous en SSH au serveur :**

```bash
# Installer les dépendances
composer install --no-dev --optimize-autoloader

# Exécuter les migrations
php artisan migrate --force

# Nettoyer les caches
php artisan config:clear
php artisan cache:clear
```

### Option 3 : Via cPanel ou Hébergeur Web

Si vous n'avez pas accès SSH :

1. Uploader tous les fichiers modifiés via FTP
2. Accéder à phpMyAdmin
3. Exécuter manuellement le SQL de migration :

```sql
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## ✅ VÉRIFICATION POST-DÉPLOIEMENT

### Test 1 : Vérifier Sanctum

```bash
curl -X POST http://pressings.universaltechnologiesafrica.com/api/auth/send-otp \
  -H "Content-Type: application/json" \
  -d '{"phone":"0707070701"}'

# Réponse attendue :
# {"success":true,"message":"Code OTP envoyé avec succès","expires_in":300}
```

### Test 2 : Vérifier la table

```sql
-- Dans phpMyAdmin ou MySQL
SHOW TABLES LIKE 'personal_access_tokens';

-- Doit retourner la table
```

### Test 3 : Test complet avec l'app mobile

1. Ouvrir l'application mobile
2. Entrer le numéro : `0707070701`
3. Cliquer "Recevoir le code OTP"
4. ✅ Pas d'erreur "createToken()"
5. Entrer le code OTP (récupéré de la BDD ou des logs)
6. ✅ Authentification réussie !

---

## 🐛 DÉPANNAGE

### Erreur : "Class 'Laravel\Sanctum\HasApiTokens' not found"

```bash
# Sur le serveur
composer install
composer dump-autoload
```

### Erreur : "Table 'personal_access_tokens' doesn't exist"

```bash
# Sur le serveur
php artisan migrate --force
```

### Erreur de permissions

```bash
# Sur le serveur
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## 📝 NOTES IMPORTANTES

### ⚠️ En Production

1. **Ne pas utiliser APP_DEBUG=true** en production
2. **Configurer le .env** correctement :
```env
APP_ENV=production
APP_DEBUG=false
SANCTUM_STATEFUL_DOMAINS=pressings.universaltechnologiesafrica.com
SESSION_DOMAIN=.universaltechnologiesafrica.com
```

3. **Optimiser pour la production :**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 🔒 Sécurité

1. **HTTPS** : Activez SSL/TLS pour la production
2. **Rate Limiting** : Sanctum inclut du rate limiting par défaut
3. **Token Expiration** : Configurez dans `config/sanctum.php`

---

## 📚 DOCUMENTATION

**Laravel Sanctum :** https://laravel.com/docs/11.x/sanctum

**Configuration :** `config/sanctum.php`

**Migrations :** `database/migrations/2026_01_05_193454_create_personal_access_tokens_table.php`

---

## 🎯 APRÈS LE DÉPLOIEMENT

Une fois Sanctum déployé sur production :

1. ✅ L'erreur `createToken()` sera corrigée
2. ✅ L'authentification OTP fonctionnera
3. ✅ Les tokens API seront gérés correctement
4. ✅ L'application mobile pourra se connecter

---

**Besoin d'aide ?** Contactez votre administrateur système ou hébergeur.

