# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- **Background Queuing**: Introduced `SyncVectorStoreJob` and `DeleteVectorStoreJob` to handle vector synchronization asynchronously via Laravel Queues.
- **Caching Layer**: Added built-in caching for embeddings and RAG chat responses to improve performance and reduce API costs.
- **Latest AI Models**: Added support for `gpt-5` (OpenAI) and `gemini-3.1-flash-lite` (Gemini).
- **GitHub Actions**: Added CI workflow to automatically test against PHP 8.1-8.3 and Laravel 9-11.
- **Testing Suite**: Initialized unit and feature tests using Orchestra Testbench.
- **Contribution Guidelines**: Added `CONTRIBUTING.md` to facilitate open-source collaboration.

### Changed
- **Gemini Security**: Updated `GeminiDriver` to use `x-goog-api-key` header instead of query parameters for API keys.
- **Prompt Safety**: Refactored `GeminiDriver` to use official `systemInstruction` field to mitigate prompt injection risks.
- **Configuration**: Updated `config/vector-search.php` with new model defaults and cache TTL settings.

### Fixed
- Fixed a potential security leak where Gemini API keys could be exposed in web server logs.
- Fixed performance bottleneck where saving a model would block the request until the AI embedding was generated.
