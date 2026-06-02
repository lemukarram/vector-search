# Update 2: Modern LLM Support (Next-Gen Standards)
**Focus:** Integrating frontier models and Anthropic support with specific API requirements.

## 1. Anthropic Integration
- **Base URL:** `api.anthropic.com/v1/`
- **Primary Model:** `claude-sonnet-4-6`
- **Auth Headers:** `x-api-key` and `anthropic-version: 2023-06-01`
- Support for Claude's unique system message structure.

## 2. Google Gemini Integration
- **Base URL:** `generativelanguage.googleapis.com`
- **Models:** `gemini-2.5-flash`, `gemini-2.5-pro`
- **Auth:** `x-goog-api-key` header.

## 3. OpenAI Integration
- **Base URL:** `api.openai.com/v1/`
- **Models:** `gpt-4.1` (Stable), `gpt-5.5` (Frontier/Latest)
- **Auth:** `Bearer Authorization` header.

## 4. DeepSeek Integration
- **Base URL:** `api.deepseek.com`
- **Models:** `deepseek-v4-flash` (New), `deepseek-reasoner` (Legacy)
- **Auth:** `Bearer Authorization` header.

## 5. Usage & Cost Tracking
- Implement an event listener to track token usage per request.
- Standardize response objects to return "finish_reason" and "usage_stats" (prompt/completion tokens).
