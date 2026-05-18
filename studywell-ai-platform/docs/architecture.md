# Architecture

## Component Boundaries

```text
Browser
  -> Laravel Blade UI
  -> Laravel controllers and MySQL models
  -> Lumen API gateway
  -> OpenAI Responses API and Embeddings API
  -> Pinecone vector index
  -> MongoDB memory and workflow logs
  -> n8n workflow orchestration
```

## Product Concept

StudyWell AI is a student study-life assistant. Instead of a generic tracker dashboard, it records the signals a student can explain in a demo:

- Focus minutes.
- Sleep hours.
- Mood score.
- Water cups.
- Screen time.
- Energy level.
- Daily reflection.

The AI layer uses those signals plus uploaded notes, study plans, or wellness documents to generate grounded suggestions.

## Laravel App

Laravel owns user-facing screens, relational data, dashboards, and upload forms:

- Wellness signals in MySQL.
- Blade dashboard with Chart.js.
- PDF, DOCX, and TXT document upload.
- Gateway client for AI calls.
- Breeze-ready auth dependency.

## Lumen API Gateway

Lumen owns integration logic:

- `POST /api/documents/ingest`: chunk text, embed chunks, upsert Pinecone, log to MongoDB.
- `POST /api/ai/chat`: embed question, retrieve Pinecone context, call OpenAI, save chat memory.
- `POST /api/wellness/insights`: summarize study-life signals.

This makes the AI layer independently deployable and easy to test with Postman or n8n.

## RAG Flow

1. User uploads PDF, DOCX, or TXT in Laravel.
2. Laravel extracts text and calls Lumen.
3. Lumen chunks the text and requests embeddings.
4. Lumen upserts chunk vectors into Pinecone.
5. User asks a study or lifestyle question.
6. Lumen embeds the question and queries Pinecone.
7. Lumen sends retrieved context plus question to OpenAI.
8. Lumen stores chat history and context metadata in MongoDB.

## Deployment Shape

Docker Compose runs Laravel, Lumen, MySQL, MongoDB, n8n, and Nginx locally. Production can use the same service split on a VPS, DigitalOcean, AWS ECS, Railway, or Render.
