# StudyWell AI Platform

Fullstack + AI + RAG + Automation + DevOps starter project for a student-focused portfolio. The product idea is a study-life assistant that tracks focus, sleep, mood, water intake, screen time, and personal reflections, then uses RAG over uploaded study or wellness documents to produce contextual suggestions.

## Architecture

```text
Laravel Blade UI
  -> Laravel backend
  -> Lumen API gateway
  -> n8n workflow engine
  -> OpenAI + Pinecone + MongoDB
```

## Services

- `laravel-app`: Blade, TailwindCSS, Vite, Breeze-ready auth, wellness signal CRUD, dashboard, Chart.js, document upload UI.
- `lumen-api`: API gateway for StudyWell chat, RAG retrieval, document ingestion, and study-life insights.
- `n8n-workflows`: Importable workflow JSON files for study document ingestion, RAG chat, and scheduled study-balance monitoring.
- `mysql`: relational data for users, wellness signals, and reflection reports.
- `mongo`: AI memory, chat history, workflow logs, and context cache.
- `nginx`: reverse proxy in front of Laravel and Lumen.

## Quick Start

Copy environment files:

```powershell
Copy-Item .env.example .env
Copy-Item laravel-app/.env.example laravel-app/.env
Copy-Item lumen-api/.env.example lumen-api/.env
```

Install PHP and frontend dependencies:

```powershell
cd laravel-app
composer install
npm install
php artisan key:generate
php artisan migrate --seed

cd ..\lumen-api
composer install
```

Run without Docker:

```powershell
cd laravel-app
php artisan serve --host=127.0.0.1 --port=8080

cd ..\lumen-api
php -S 127.0.0.1:8081 -t public
```

Run with Docker:

```powershell
docker compose up --build
```

Open:

- Laravel app: http://localhost:8080
- Lumen API: http://localhost:8081/status
- n8n: http://localhost:5678
- Nginx proxy: http://localhost:8088

## AI and RAG

The gateway uses the OpenAI Responses API for assistant output and the Embeddings API for vectors. It defaults to `gpt-5.4-mini` for AI responses and `text-embedding-3-small` for embeddings. If no API keys are configured, the gateway returns deterministic mock responses so the app can be demoed locally.

Set these values in `.env`, `laravel-app/.env`, and `lumen-api/.env` when ready:

```env
OPENAI_API_KEY=sk-...
OPENAI_CHAT_MODEL=gpt-5.4-mini
OPENAI_EMBEDDING_MODEL=text-embedding-3-small
PINECONE_API_KEY=...
PINECONE_HOST=https://your-index-host.svc.region.pinecone.io
PINECONE_INDEX=studywell-ai-platform
```

## CV Bullets

- Built StudyWell AI, a student productivity and wellness platform using Laravel, Lumen, OpenAI, Pinecone, MongoDB, MySQL, Docker, and n8n.
- Implemented Retrieval-Augmented Generation with document ingestion, chunking, embeddings, vector search, and contextual study coaching responses.
- Designed a multi-service system with workflow automation, API gateway boundaries, dashboard analytics, and CI checks.

## Build Phases

1. Laravel, MySQL, auth, and wellness signal CRUD.
2. Dashboard UI, Chart.js analytics, and internal APIs.
3. OpenAI integration and n8n orchestration.
4. MongoDB memory, Pinecone vectors, and RAG.
5. Docker Compose, GitHub Actions, Swagger/OpenAPI, Postman, and deployment docs.
