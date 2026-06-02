# Update 5: Developer Experience & Operational Stability
**Focus:** Solving "Eloquent Overhead" and "Settings Management".

## 1. Batch Syncing & Throttling
- Replace individual model `saved` jobs with a `BatchSync` job that groups updates.
- Implement rate limiting for embedding providers (e.g., OpenAI/Anthropic RPM limits).

## 2. Setting Snapshots & Overrides
- Allow per-request overrides: `VectorSearch::withModel('claude-3-sonnet')->chat($query)`.
- Support for "Context Windows": Automatically truncate context if it exceeds the model's limit.

## 3. Testing Suite
- Provide a `VectorSearch::fake()` helper.
- Mock responses for both Vector Stores and LLMs.
- Assert that specific models were searched or specific content was sent to the LLM.

## 4. Documentation Rewrite
- New comprehensive guides for Hybrid Search, Metadata Filtering, and Chunking strategies.
- API Reference for the Facade and internal Contracts.
