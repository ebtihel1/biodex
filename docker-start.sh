#!/bin/bash

# Script de démarrage Docker pour Biodex

set -e

echo "🚀 Démarrage de l'infrastructure Docker Biodex..."
echo ""

# Vérifier si .env existe
if [ ! -f .env ]; then
    echo "📝 Création du fichier .env à partir de .env.docker..."
    cp .env.docker .env
    echo "✅ Fichier .env créé. Veuillez l'éditer si nécessaire."
    echo ""
fi

# Construire les images
echo "🔨 Construction des images Docker..."
docker-compose build

# Démarrer les services
echo "🚀 Démarrage des services..."
docker-compose up -d

echo ""
echo "⏳ Attente du démarrage de MySQL..."
sleep 10

# Exécuter les migrations Laravel
echo "🗄️ Exécution des migrations Laravel..."
docker-compose exec backend php artisan key:generate --ansi
docker-compose exec backend php artisan migrate --force
docker-compose exec backend php artisan storage:link

echo ""
echo "✅ Infrastructure Docker Biodex démarrée avec succès !"
echo ""
echo "📋 Services disponibles :"
echo "   - Application principale : http://localhost"
echo "   - Frontend : http://localhost:8080"
echo "   - AI Service Main : http://localhost:5000"
echo "   - AI Service Waste : http://localhost:5001"
echo "   - AI Service Recycling : http://localhost:5002"
echo "   - AI Service Image : http://localhost:5004"
echo ""
echo "📝 Commandes utiles :"
echo "   - Voir les logs : docker-compose logs -f"
echo "   - Arrêter : docker-compose down"
echo "   - Arrêter et supprimer volumes : docker-compose down -v"
echo ""
