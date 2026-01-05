# 🗑️ SUPPRESSION - Migration en doublon

## 🔍 Problème identifié

Vous avez **DEUX fichiers de migration** pour `personal_access_tokens` sur votre serveur :

1. ✅ `2026_01_05_193454_create_personal_access_tokens_table.php` (le bon, avec la correction)
2. ❌ `2026_01_05_193622_create_personal_access_tokens_table.php` (le doublon qui cause l'erreur)

---

## 🚀 Solution : Supprimer le doublon sur le serveur

### Via SSH (RECOMMANDÉ)

Connectez-vous en SSH et supprimez le fichier en doublon :

```bash
# Connexion SSH
ssh votre_user@pressings.universaltechnologiesafrica.com

# Aller dans le dossier des migrations
cd /votre-chemin/pressings/database/migrations

# Lister les fichiers pour vérifier
ls -la *personal_access_tokens*

# Supprimer le doublon (le fichier 193622)
rm 2026_01_05_193622_create_personal_access_tokens_table.php

# Vérifier qu'il reste uniquement le bon fichier
ls -la *personal_access_tokens*

# Relancer les migrations
cd /votre-chemin/pressings
php artisan migrate
```

---

### Via FTP/SFTP

1. Connectez-vous à votre serveur via FTP
2. Naviguez vers : `/votre-chemin/pressings/database/migrations/`
3. Cherchez le fichier : `2026_01_05_193622_create_personal_access_tokens_table.php`
4. Supprimez-le
5. Gardez uniquement : `2026_01_05_193454_create_personal_access_tokens_table.php`
6. Reconnectez-vous en SSH et exécutez : `php artisan migrate`

---

## 📋 Commandes complètes (copier-coller)

```bash
# 1. Connexion
ssh votre_user@pressings.universaltechnologiesafrica.com

# 2. Navigation
cd pressings/database/migrations

# 3. Vérification des fichiers
ls -la | grep personal_access_tokens

# Vous devriez voir :
# - 2026_01_05_193454_create_personal_access_tokens_table.php
# - 2026_01_05_193622_create_personal_access_tokens_table.php

# 4. Suppression du doublon (le plus récent)
rm 2026_01_05_193622_create_personal_access_tokens_table.php

# 5. Vérification
ls -la | grep personal_access_tokens

# Maintenant vous ne devez voir que :
# - 2026_01_05_193454_create_personal_access_tokens_table.php

# 6. Retour à la racine du projet
cd ../..

# 7. Relancer les migrations
php artisan migrate
```

---

## ✅ Résultat attendu

Après suppression du doublon et exécution de `php artisan migrate`, vous devriez voir :

```
✅ 2026_01_05_193454_create_personal_access_tokens_table ... Migration réussie
```

Et plus d'erreur !

---

## 🤔 Pourquoi ce doublon ?

Probablement :
1. Vous avez exécuté `php artisan sanctum:install` plusieurs fois
2. Ou il y a eu une interruption lors de la première installation
3. Laravel a créé deux migrations au lieu d'une

---

## 🎯 Pourquoi garder le fichier `193454` ?

C'est celui que j'ai corrigé avec la vérification :

```php
if (!Schema::hasTable('personal_access_tokens')) {
    Schema::create('personal_access_tokens', ...);
}
```

Le fichier `193622` n'a pas cette correction et causera toujours l'erreur.

---

## 🔄 Après la suppression

Une fois le doublon supprimé et les migrations relancées :

1. ✅ Plus d'erreur de migration
2. ✅ L'API fonctionnera correctement
3. ✅ L'authentification Sanctum sera opérationnelle
4. ✅ L'app mobile pourra se connecter

---

## 💡 Pour éviter ce problème à l'avenir

Ne jamais exécuter `php artisan sanctum:install` plusieurs fois. Si vous devez réinstaller Sanctum :

```bash
# 1. Supprimer les anciennes migrations
rm database/migrations/*personal_access_tokens*

# 2. Réinstaller
php artisan sanctum:install

# 3. Migrer
php artisan migrate
```

---

## 📍 Résumé en 3 étapes

```bash
# 1. Supprimer le doublon
ssh votre_user@pressings.universaltechnologiesafrica.com
cd pressings/database/migrations
rm 2026_01_05_193622_create_personal_access_tokens_table.php

# 2. Relancer les migrations
cd ../..
php artisan migrate

# 3. ✅ Terminé !
```

---

**Note** : Si vous n'avez pas accès SSH, utilisez FTP pour supprimer le fichier, puis contactez votre hébergeur pour exécuter `php artisan migrate`.

