# Script de démarrage Docker pour Biodex (Windows PowerShell)

Write-Host "🚀 Démarrage de l'infrastructure Docker Biodex..." -ForegroundColor Green
Write-Host ""

# Vérifier si .env existe
if (-not (Test-Path .env)) {
    Write-Host "📝 Création du fichier .env à partir de .env.docker..." -ForegroundColor Yellow
    Copy-Item .env.docker .env
    Write-Host "✅ Fichier .env créé. Veuillez l'éditer si nécessaire." -ForegroundColor Green
    Write-Host ""
}

# Construire les images
Write-Host "🔨 Construction des images Docker..." -ForegroundColor Yellow
docker-compose build

# Démarrer les services
Write-Host "🚀 Démarrage des services..." -ForegroundColor Yellow
docker-compose up -d

Write-Host ""
Write-Host "⏳ Attente du démarrage de MySQL..." -ForegroundColor Yellow
Start-Sleep -Seconds 10

# Exécuter les migrations Laravel
Write-Host "🗄️ Exécution des migrations Laravel..." -ForegroundColor Yellow
try { docker-compose exec backend php artisan key:generate --ansi } catch { Write-Host "Warning: key generation failed" -ForegroundColor Yellow }
try { docker-compose exec backend php artisan migrate --force } catch { Write-Host "Warning: migration failed" -ForegroundColor Yellow }
try { docker-compose exec backend php artisan storage:link } catch { Write-Host "Warning: storage link failed" -ForegroundColor Yellow }

Write-Host ""
Write-Host "✅ Infrastructure Docker Biodex démarrée avec succès !" -ForegroundColor Green
Write-Host ""
Write-Host "📋 Services disponibles :" -ForegroundColor Cyan
Write-Host "   - Application principale : http://localhost"
Write-Host "   - Frontend : http://localhost:8080"
Write-Host "   - AI Service Main : http://localhost:5000"
Write-Host "   - AI Service Waste : http://localhost:5001"
Write-Host "   - AI Service Recycling : http://localhost:5002"
Write-Host "   - AI Service Image : http://localhost:5004"
Write-Host ""
Write-Host "📝 Commandes utiles :" -ForegroundColor Cyan
Write-Host "   - Voir les logs : docker-compose logs -f"
Write-Host "   - Arrêter : docker-compose down"
Write-Host "   - Arrêter et supprimer volumes : docker-compose down -v"
Write-Host ""
