Then on another machine:

sudo docker login
sudo docker compose pull
sudo docker compose up -d

That gives you the full workflow:

Dockerfile
   ↓
docker compose build
   ↓
Docker images
   ↓
Docker Hub
   ↓
docker compose pull
   ↓
Another EC2
   ↓
🚀 Running application



Build → Push → Pull → Deploy.
