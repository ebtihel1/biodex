# Dockerfile pour les services IA Python
FROM python:3.12-slim

WORKDIR /app

# Installation des dépendances système
RUN apt-get update && apt-get install -y --no-install-recommends \
    gcc \
    g++ \
    && rm -rf /var/lib/apt/lists/*

# Copie des fichiers requirements
COPY requirements.txt .

# Installation des dépendances Python
RUN pip install --no-cache-dir -r requirements.txt

# Copie des fichiers du service IA
COPY . .

# Création du répertoire pour les modèles
RUN mkdir -p /app/models

# Exposition des ports (chaque service utilisera son propre port)
EXPOSE 5000 5001 5002 5004

# Commande par défaut (sera surchargée dans docker-compose)
CMD ["python", "app.py"]
