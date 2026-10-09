# 🐳 Infrastructure Docker Biodex

Configuration Docker complète pour le projet Biodex avec backend Laravel, frontend Vite, base de données et 4 services IA.

## 📦 Structure des fichiers créés

```
biodex/
├── Dockerfile                    # Backend Laravel (PHP-FPM)
├── Dockerfile.frontend           # Frontend Vite (nginx)
├── docker-compose.yml            # Orchestration de tous les services
├── .env.docker                   # Configuration environnement par défaut
├── .dockerignore                 # Fichiers à exclure du build
├── docker-start.sh               # Script de démarrage (Linux/Mac)
├── docker-start.ps1              # Script de démarrage (Windows)
├── DOCKER.md                     # Documentation détaillée
├── docker/
│   └── nginx/
│       ├── nginx.conf           # Configuration nginx principale
│       ├── conf.d/
│       │   └── biodex.conf      # Configuration reverse proxy
│       └── frontend.conf        # Configuration frontend
└── ai-service/
    ├── Dockerfile               # Services IA Python
    └── .dockerignore            # Fichiers à exclure du build IA
```

## 🚀 Démarrage rapide

### Windows (PowerShell)

```powershell
.\docker-start.ps1
```

### Linux/Mac

```bash
chmod +x docker-start.sh
./docker-start.sh
```

### Manuellement

```bash
# 1. Copier la configuration
cp .env.docker .env

# 2. Construire les images
docker-compose build

# 3. Démarrer les services
docker-compose up -d

# 4. Initialiser Laravel
docker-compose exec backend php artisan key:generate
docker-compose exec backend php artisan migrate --force
docker-compose exec backend php artisan storage:link
```

## 🌐 Services disponibles

| Service | URL | Description |
|---------|-----|-------------|
| Application | http://localhost | Application principale (nginx) |
| Frontend | http://localhost:8080 | Frontend Vite (standalone) |
| AI Main | http://localhost:5000 | Service IA principal |
| AI Waste | http://localhost:5001 | Service conseil déchets |
| AI Recycling | http://localhost:5002 | Service recyclage |
| AI Image | http://localhost:5004 | Classification d'images |
| MySQL | localhost:3306 | Base de données |
| Redis | localhost:6379 | Cache et queue |

## 📝 API Routes via Nginx

Le reverse proxy nginx expose les services IA sous `/api/ai/` :

- `POST /api/ai/predict` → AI Service Main (classification déchets)
- `POST /api/ai/collection/train` → AI Service Main (entraînement collecte)
- `POST /api/ai/collection/predict` → AI Service Main (prédiction collecte)
- `POST /api/ai/waste/recycling-advice` → AI Service Waste
- `POST /api/ai/recycling/classify-waste` → AI Service Recycling
- `POST /api/ai/recycling/predict-quality` → AI Service Recycling
- `POST /api/ai/recycling/estimate-price` → AI Service Recycling
- `POST /api/ai/recycling/generate-description` → AI Service Recycling
- `POST /api/ai/recycling/optimize-process` → AI Service Recycling
- `POST /api/ai/image/classify` → AI Service Image

## 🔧 Configuration

### Variables d'environnement clés

Éditez `.env` pour personnaliser :

```env
# Base de données
DB_DATABASE=biodex
DB_USERNAME=biodex_user
DB_PASSWORD=biodex_password

# Services IA
AI_LLM_ENABLED=0              # Activer fallback LLM
OPENAI_API_KEY=              # Clé API OpenAI (optionnel)
OPENAI_MODEL=                # Modèle OpenAI (ex: gpt-4)

# Ports
NGINX_PORT=80
FRONTEND_PORT=8080
```

## 🛠️ Commandes utiles

```bash
# Voir les logs
docker-compose logs -f

# Logs d'un service spécifique
docker-compose logs -f backend
docker-compose logs -f ai-service-main

# Arrêter les services
docker-compose down

# Arrêter et supprimer les volumes
docker-compose down -v

# Reconstruire une image
docker-compose build backend
docker-compose build ai-service-main

# Entrer dans un conteneur
docker-compose exec backend sh
docker-compose exec ai-service-main sh

# Exécuter une commande artisan
docker-compose exec backend php artisan <command>

# Redémarrer un service
docker-compose restart backend
```

## 📊 Volumes persistants

- `mysql_data` : Données MySQL
- `redis_data` : Données Redis
- `ai_models` : Modèles IA entraînés

## 🔍 Dépannage

### Le backend ne se connecte pas à MySQL

Vérifiez que MySQL est prêt :
```bash
docker-compose logs mysql
docker-compose exec backend php artisan migrate
```

### Les services IA ne répondent pas

```bash
docker-compose logs ai-service-main
docker-compose logs ai-service-waste
docker-compose logs ai-service-recycling
docker-compose logs ai-service-image
```

### Erreur de permissions

```bash
docker-compose exec backend chown -R www-data:www-data storage bootstrap/cache
```

## 📚 Documentation détaillée

Voir <ref_file file="C:\Users\ebtih\Biodex\biodex\DOCKER.md" /> pour une documentation complète.

## ⚠️ Notes importantes

1. **Production** : Modifiez les mots de passe par défaut dans `.env` avant le déploiement
2. **SSL** : Configurez nginx avec SSL pour la production
3. **LLM** : Le fallback OpenAI est désactivé par défaut pour éviter les coûts
4. **Ressources** : Les services IA nécessitent suffisamment de RAM (recommandé: 2GB+ par service)
5. **Volumes** : Les données sont persistées dans les volumes Docker
