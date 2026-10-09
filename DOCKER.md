# Infrastructure Docker Biodex

Ce document décrit l'infrastructure Docker pour le projet Biodex, incluant le backend Laravel, le frontend Vite, la base de données et les 4 services IA.

## 📋 Architecture

### Services déployés

1. **MySQL** - Base de données (port 3306)
2. **Redis** - Cache et queue (port 6379)
3. **Backend Laravel** - PHP-FPM (port 9000 interne)
4. **Frontend Vite** - Nginx (port 8080)
5. **AI Service Main** - Service IA principal (port 5000)
6. **AI Service Waste** - Service de conseil déchets (port 5001)
7. **AI Service Recycling** - Service de recyclage (port 5002)
8. **AI Service Image** - Classification d'images (port 5004)
9. **Nginx** - Reverse proxy (port 80)

## 🚀 Démarrage rapide

### 1. Configuration de l'environnement

Copiez le fichier d'environnement Docker :

```bash
cp .env.docker .env
```

Éditez `.env` selon vos besoins (notamment les mots de passe et clés API).

### 2. Construction des images

```bash
docker-compose build
```

### 3. Démarrage des services

```bash
docker-compose up -d
```

### 4. Initialisation de Laravel

Exécutez les migrations et générez la clé :

```bash
docker-compose exec backend php artisan key:generate
docker-compose exec backend php artisan migrate --force
docker-compose exec backend php artisan storage:link
```

### 5. Accès aux services

- **Application principale** : http://localhost
- **Frontend** : http://localhost:8080
- **AI Service Main** : http://localhost:5000
- **AI Service Waste** : http://localhost:5001
- **AI Service Recycling** : http://localhost:5002
- **AI Service Image** : http://localhost:5004

## 📝 Commandes utiles

### Voir les logs

```bash
# Tous les services
docker-compose logs -f

# Service spécifique
docker-compose logs -f backend
docker-compose logs -f ai-service-main
```

### Arrêter les services

```bash
docker-compose down
```

### Arrêter et supprimer les volumes

```bash
docker-compose down -v
```

### Reconstruire une image spécifique

```bash
docker-compose build backend
docker-compose build ai-service-main
```

### Entrer dans un conteneur

```bash
docker-compose exec backend sh
docker-compose exec ai-service-main sh
```

### Exécuter une commande artisan

```bash
docker-compose exec backend php artisan <command>
```

## 🔧 Configuration

### Variables d'environnement principales

| Variable | Description | Défaut |
|----------|-------------|--------|
| `APP_ENV` | Environnement (production/local) | production |
| `APP_DEBUG` | Mode debug | false |
| `DB_DATABASE` | Nom de la base de données | biodex |
| `DB_USERNAME` | Utilisateur MySQL | biodex_user |
| `DB_PASSWORD` | Mot de passe MySQL | biodex_password |
| `AI_LLM_ENABLED` | Activation fallback LLM | 0 |
| `OPENAI_API_KEY` | Clé API OpenAI | - |
| `OPENAI_MODEL` | Modèle OpenAI | - |

### Ports

| Service | Port externe | Port interne |
|---------|--------------|--------------|
| Nginx | 80 | 80 |
| Frontend | 8080 | 80 |
| MySQL | 3306 | 3306 |
| Redis | 6379 | 6379 |
| AI Main | 5000 | 5000 |
| AI Waste | 5001 | 5001 |
| AI Recycling | 5002 | 5002 |
| AI Image | 5004 | 5004 |

## 🌐 Configuration Nginx

Le reverse proxy Nginx configure les routes suivantes :

- `/` → Frontend (SPA)
- `*.php` → Backend Laravel
- `/storage` → Fichiers stockés
- `/api/ai/` → AI Service Main
- `/api/ai/waste/` → AI Service Waste
- `/api/ai/recycling/` → AI Service Recycling
- `/api/ai/image/` → AI Service Image

## 🤖 Services IA

Les services IA sont configurés avec les variables suivantes :

- `FLASK_HOST` : Hôte d'écoute (0.0.0.0 pour Docker)
- `FLASK_PORT` / `WASTE_AI_PORT` / `RECYCLING_AI_PORT` / `WASTE_IMG_PORT` : Ports spécifiques
- `AI_SERVICE_THREADS` : Nombre de threads (défaut: 4)
- `BIODEX_AI_MODEL_DIR` : Répertoire des modèles

### Activation du fallback LLM

Pour activer le fallback OpenAI, configurez dans `.env` :

```env
AI_LLM_ENABLED=1
OPENAI_API_KEY=sk-...
OPENAI_MODEL=gpt-4
```

## 📦 Volumes Docker

Les volumes persistants suivants sont créés :

- `mysql_data` : Données MySQL
- `redis_data` : Données Redis
- `ai_models` : Modèles IA entraînés

## 🔍 Dépannage

### Le backend ne démarre pas

Vérifiez que MySQL est prêt :

```bash
docker-compose logs mysql
```

### Les services IA ne répondent pas

Vérifiez les logs :

```bash
docker-compose logs ai-service-main
docker-compose logs ai-service-waste
docker-compose logs ai-service-recycling
docker-compose logs ai-service-image
```

### Erreur de connexion à la base de données

Vérifiez que les variables d'environnement DB_ sont correctes dans `.env`.

### Problèmes de permissions

```bash
docker-compose exec backend chown -R www-data:www-data storage bootstrap/cache
```

## 🛠️ Développement

Pour le développement, vous pouvez utiliser des volumes montés pour modifier le code sans reconstruire les images :

```bash
docker-compose up -d backend
# Modifier le code localement
# Les changements sont reflétés immédiatement
```

## 📚 Références

- [Docker Compose Documentation](https://docs.docker.com/compose/)
- [Laravel Documentation](https://laravel.com/docs)
- [Nginx Documentation](https://nginx.org/en/docs/)
