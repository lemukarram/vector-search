# Update 1: Core Architecture & Configuration
**Focus:** Solving Tight Coupling, Error Handling, and Developer Control.

## 1. Move to Laravel HTTP Facade
- Replace `GuzzleHttp\Client` with `Illuminate\Support\Facades\Http`.
- Benefits: Easier testing with `Http::fake()`, middleware support, and consistent timeout handling.

## 2. Robust Error Handling
- Implement a custom `VectorSearchException` hierarchy.
- Wrap all API calls in `try-catch` blocks.
- Implement retry logic using Laravel's `Http::retry()`.

## 3. Dynamic Configuration & Prompt Management
- Move the RAG system prompt to the `config/vector-search.php` file.
- Allow template variables in prompts: `{{context}}`, `{{query}}`, `{{history}}`.
- Support for "System Instructions" vs "User Prompts" for models like Gemini and Claude.

## 4. Advanced Driver Interface
- Update `AiChatDriver` and `AiEmbeddingDriver` to handle multi-modal inputs (future-proofing).
- Standardize the `Response` object to include usage statistics (tokens used).
