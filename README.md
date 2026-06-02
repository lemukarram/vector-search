# Laravel AI RAG 🧠🚀

[![Latest Version on Packagist](https://img.shields.io/packagist/v/lemukarram/laravel-ai-rag.svg?style=flat-square)](https://packagist.org/packages/lemukarram/laravel-ai-rag)
[![Total Downloads](https://img.shields.io/packagist/dt/lemukarram/laravel-ai-rag.svg?style=flat-square)](https://packagist.org/packages/lemukarram/laravel-ai-rag)
[![GitHub Stars](https://img.shields.io/github/stars/lemukarram/vector-search.svg?style=flat-square)](https://github.com/lemukarram/vector-search)

**Laravel AI RAG** is the most powerful, developer-friendly **Retrieval-Augmented Generation (RAG)** package for the Laravel ecosystem. Transform your standard Eloquent models into high-performance, AI-aware data sources in seconds.

Whether you're building an **AI Chatbot**, **Semantic Search Engine**, or **Intelligent Recommendation System**, this package provides the enterprise-grade foundation you need.

---

## 💎 Why Laravel AI RAG?

Stop fighting with complex AI APIs. We provide a clean, driver-based abstraction that lets you swap LLMs and Vector Databases with a single line of code.

### 🌟 Elite Features
- **Next-Gen Model Support**: Native integration with **OpenAI GPT-5.5**, **Google Gemini 2.5**, **Anthropic Claude 4.6**, and **DeepSeek V4**.
- **Advanced RAG Techniques**: 
    - **Hybrid Search**: Combines vector similarity with traditional full-text search.
    - **Recursive Chunking**: Automatically splits large documents for perfect context density.
    - **Multi-Query Expansion**: Uses AI to rephrase user queries, ensuring the most relevant context is found.
- **Enterprise Reliability**:
    - **Metadata Filtering**: Advanced `WHERE` clauses for your vector queries.
    - **Smart Caching**: Drastically reduce API costs with built-in embedding and chat caching.
    - **Batch Processing**: High-efficiency background jobs for syncing millions of records.
- **Perfect Developer Experience**:
    - **Eloquent Trait**: Just use `VectorSearchable` and you're done.
    - **Mockable Testing**: Use `VectorSearch::fake()` to test your AI features without spending a cent.

---

## 📦 Rapid Installation

```bash
composer require lemukarram/laravel-ai-rag
```

Publish and migrate (if applicable):
```bash
php artisan vendor:publish --provider="LeMukarram\VectorSearch\VectorSearchServiceProvider"
```

---

## 🛠️ Configuration & Supported Drivers

### 🧠 LLM Providers (Chat & Embeddings)
| Provider | Supported Models |
|----------|------------------|
| **OpenAI** | GPT-5.5 (Frontier), GPT-4o, o1-preview |
| **Google** | Gemini 2.5 Pro, Gemini 1.5 Flash |
| **Anthropic** | Claude 4.6 Sonnet, Claude 3.5 Opus |
| **DeepSeek** | DeepSeek V4 Flash, DeepSeek Reasoner |

### 📂 Vector Databases
| Driver | Best For |
|--------|----------|
| **Upstash** | Serverless, zero-config, low latency |
| **Pinecone** | Massive scale, production-grade performance |
| **ChromaDB** | Local development and self-hosted privacy |

---

## 💻 Pro Usage

### Step 1: Make Your Model AI-Searchable
```php
use LeMukarram\VectorSearch\Traits\VectorSearchable;

class Documentation extends Model
{
    use VectorSearchable;

    /**
     * Define which columns should be vectorized for AI search.
     */
    public function getVectorColumns(): array
    {
        return ['title', 'content', 'tags', 'version'];
    }
}
```

### Step 2: Advanced Semantic Search
```php
use LeMukarram\VectorSearch\Facades\VectorSearch;

// Perform high-accuracy semantic search
$docs = VectorSearch::similar('How do I scale my Laravel API?', topK: 5);
```

### Step 3: RAG Chat (AI Answers from Data)
```php
// The AI will answer ONLY based on your database content
$answer = VectorSearch::chat('What are our server requirements?');

echo $answer->content();
echo "Tokens used: " . $answer->usage()['total_tokens'];
```

### Step 4: Pro-Level Filtering & Overrides
```php
$answer = VectorSearch::whereMetadata('version', 'v2')
    ->withModel('anthropic') // Swap to Claude on the fly
    ->chat('What is new in v2?');
```

---

## 🧪 Bulletproof Testing
Test your AI features reliably in CI/CD:

```php
public function test_ai_feature()
{
    $fake = VectorSearch::fake();
    $fake->pushChatResponse('Mocked AI Answer');

    $response = VectorSearch::chat('test query');
    
    $this->assertEquals('Mocked AI Answer', $response->content());
}
```

---

## 📈 Search Performance & SEO Tips
Keywords: *Laravel AI, Vector Search Laravel, RAG Laravel, GPT-5 Laravel, Gemini AI Laravel, PHP AI Package, Semantic Search PHP, Pinecone Laravel, Upstash Vector Laravel.*

---

## 🤝 Contributing & Support
If you love this package, please give it a ⭐ on GitHub! 

## 📄 License
The MIT License (MIT). See [LICENSE.md](LICENSE.md).
