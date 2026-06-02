# Update 4: Advanced RAG Techniques (Part 2 - Search & Retrieval)
**Focus:** Solving "Poor Search Quality" and "Hydration Issues".

## 1. Hybrid Search (Technique #3)
- Combine Vector Similarity with Full-Text Search (using Laravel Scout or database-level full-text).
- Reciprocal Rank Fusion (RRF) algorithm to merge results from both sources.
- Driver support for vector stores that natively support Hybrid Search (e.g., Pinecone, Upstash).

## 2. Re-Ranking (Technique #4)
- Integrate a Cross-Encoder/Re-ranker (e.g., Cohere or Jina AI).
- Retrieve `top_20` via vector search, then re-rank to `top_5` using a more expensive but accurate model before passing to the LLM.

## 3. Query Expansion / Multi-Query (Technique #5)
- Use the LLM to generate 3-5 variations of the user's query.
- Search for all variations to increase the chance of finding the right context.
- Merge and de-duplicate results.

## 4. Optimized Hydration & Ordering
- Fix `hydrateModels` to preserve the score-based ordering returned by the vector store.
- Use `orderByRaw` with `FIELD(id, ...)` for MySQL/PostgreSQL to ensure the Eloquent collection matches search relevance.
