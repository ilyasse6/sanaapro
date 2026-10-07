#!/bin/bash

# Script pour corriger les permissions Laravel sur XAMPP macOS

echo "Correction des permissions pour Laravel..."

# Créer les répertoires s'ils n'existent pas
mkdir -p storage/logs
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p bootstrap/cache

# Créer le fichier de log s'il n'existe pas
touch storage/logs/laravel.log
touch storage/logs/user.log
touch storage/logs/client.log

# Définir les permissions (Apache XAMPP = utilisateur daemon)
chmod -R 777 storage/logs
find storage/logs -type f -name '*.log' -exec chmod 666 {} \;
chmod -R 777 storage/framework/views storage/framework/cache storage/framework/sessions storage/app/public
chmod -R 775 bootstrap/cache

# Pour XAMPP sur macOS, essayer de changer le propriétaire
if [ -d "/Applications/XAMPP" ]; then
    echo "Tentative de changement de propriétaire pour XAMPP..."
    sudo chown -R daemon:daemon storage bootstrap/cache 2>/dev/null || echo "Impossible de changer le propriétaire (nécessite sudo)"
fi

echo "Permissions corrigées !"
echo ""
echo "Si vous avez encore des problèmes, exécutez manuellement :"
echo "sudo chown -R daemon:daemon storage bootstrap/cache"
echo "sudo chmod -R 777 storage/logs"
