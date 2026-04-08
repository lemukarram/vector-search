# Laravel Vector Search & RAG (Retrieval-Augmented Generation)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/lemukarram/vector-search.svg?style=flat-square)](https://packagist.org/packages/lemukarram/vector-search)
[![Total Downloads](https://img.shields.io/packagist/dt/lemukarram/vector-search.svg?style=flat-square)](https://packagist.org/packages/lemukarram/vector-search)
[![Build Status](https://img.shields.io/github/actions/workflow/status/lemukarram/laravel-vector-search-RAG/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/lemukarram/laravel-vector-search-RAG/actions)
[![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE)

**Laravel Vector Search** is a powerful, driver-based RAG (Retrieval-Augmented Generation) package designed to give your Eloquent models "long-term memory." It enables seamless semantic search and AI-powered chat capabilities by automatically syncing your database with industry-leading vector stores and LLMs.

---

## 🚀 Key Features

- **🧠 Eloquent Auto-Sync:** Automatically vectorize and sync models (`Post`, `Product`, `User`, etc.) on save and delete.
- **⚡ Async Background Processing:** High-performance vector synchronization using Laravel Queues (never blocks your users).
- **🔎 Semantic Search:** Go beyond keyword matching with high-accuracy similarity search via `VectorSearch::similar()`.
- **🤖 Native RAG Support:** Build context-aware chatbots in seconds using `VectorSearch::chat()`.
- **🚀 Built-in Caching:** Blazing fast performance with configurable caching for embeddings and AI responses.
- **🛠️ Driver-Based Architecture:**
    - **Vector Stores:** Upstash, ChromaDB, and Pinecone.
    - **AI Models:** OpenAI (GPT-5), Google Gemini (3.1 Flash-Lite), and DeepSeek.

---

## 📦 Installation

Install the package via composer:

```bash
composer require lemukarram/vector-search
```

Publish the configuration file:

```bash
php artisan vendor:publish --tag="vector-search-config"
```

---

## ⚙️ Configuration (.env Guide)

This package is highly flexible, supporting 3 LLMs and 3 Vector Databases. Choose your combination below:

### 1. Select Your Vector Store

| Store | Driver | Recommended For |
| :--- | :--- | :--- |
| **Upstash** | `upstash` | Serverless, zero-config, great free tier. |
| **ChromaDB** | `chroma` | Open-source, self-hosted, local development. |
| **Pinecone** | `pinecone` | Best-in-class performance for production. |

```dotenv
VECTOR_STORE=upstash # or chroma, pinecone
```

### 2. Select Your AI Models (LLMs)

| Provider | Driver | Best For |
| :--- | :--- | :--- |
| **OpenAI** | `openai` | Industry standard (GPT-5 support). |
| **Google** | `gemini` | Best price-to-performance (Gemini 3.1 Flash-Lite). |
| **DeepSeek** | `deepseek` | Specialized coding and reasoning tasks. |

```dotenv
VECTOR_EMBEDDING_MODEL=openai # or gemini, deepseek
VECTOR_CHAT_MODEL=openai      # or gemini, deepseek
```

### 3. Connection Details

Depending on your choices, add the relevant keys:

```dotenv
# --- OpenAI Keys ---
OPENAI_API_KEY=sk-...

# --- Gemini Keys ---
GEMINI_API_KEY=AIza...

# --- DeepSeek Keys ---
DEEPSEEK_API_KEY=ds-...

# --- Upstash Vector ---
UPSTASH_VECTOR_URL=https://...
UPSTASH_VECTOR_TOKEN=...

# --- ChromaDB ---
CHROMA_HOST=127.0.0.1
CHROMA_PORT=8000

# --- Pinecone ---
PINECONE_API_KEY=...
PINECONE_HOST=https://...
```

---

## 📖 Usage Guide

### Step 1: Prepare Your Model
Add the `VectorSearchable` trait and define which columns should be "remembered" by the AI.

```php
use LeMukarram\VectorSearch\Traits\VectorSearchable;

class Product extends Model
{
    use VectorSearchable;

    public function getVectorColumns(): array
    {
        return ['name', 'description', 'category', 'features'];
    }
}
```

### Step 2: Semantic Similarity Search
Find products that are conceptually similar to a query, even if keywords don't match.

```php
$products = VectorSearch::similar('a comfortable chair for gaming', 5);
// Returns an Eloquent Collection of Product models.
```

### Step 3: AI Chat with RAG
Ask a question and get an answer based *only* on your database data.

```php
$answer = VectorSearch::chat('Which gaming chair has the best lumbar support?');
echo $answer;
```

---

## 💡 Use Cases

- **E-commerce:** "Help me find a gift for a 10-year-old who loves space."
- **SaaS Docs:** "How do I reset my API key using the CLI?"
- **Support Bots:** "What is your refund policy for international orders?"
- **Knowledge Bases:** "Summarize our internal policy on remote work."

---

## 🧪 Testing & Quality

We maintain high standards through rigorous testing.

Run the test suite:
```bash
vendor/bin/phpunit
```

Our CI/CD pipeline ensures compatibility across:
- **PHP Versions:** 8.1, 8.2, 8.3
- **Laravel Versions:** 9.x, 10.x, 11.x

---

## 🤝 Contribution Guide

We love contributions! Whether it's a bug fix, a new driver, or better docs.

1.  **Fork** the repo and create your branch.
2.  **Test** your changes thoroughly.
3.  **Submit** a PR with a clear description.
4.  Check out our [Contributing Guide](CONTRIBUTING.md) for more details.

---

## 👨‍💻 About the Author

**Mukarram Hussain** is the founder of **Tech with muk**, a platform dedicated to teaching modern web development and AI integration. Mukarram is a passionate Laravel developer and open-source advocate focused on making complex AI technologies accessible to everyone.

- 🌐 **Website:** [techwithmuk.com](https://techwithmuk.com)
- 🐦 **Twitter:** [@lemukarram](https://twitter.com/lemukarram)
- 📺 **YouTube:** [Tech with muk](https://youtube.com/c/techwithmuk)

---

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
