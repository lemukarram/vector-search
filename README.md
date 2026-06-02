# Laravel AI RAG 🧠🚀

[![Latest Version on Packagist](https://img.shields.io/packagist/v/lemukarram/laravel-ai-rag.svg?style=flat-square)](https://packagist.org/packages/lemukarram/laravel-ai-rag)
[![Total Downloads](https://img.shields.io/packagist/dt/lemukarram/laravel-ai-rag.svg?style=flat-square)](https://packagist.org/packages/lemukarram/laravel-ai-rag)
[![License](https://img.shields.io/packagist/l/lemukarram/laravel-ai-rag.svg?style=flat-square)](https://packagist.org/packages/lemukarram/laravel-ai-rag)

**Laravel AI RAG** is the ultimate developer toolkit for building production-ready **Retrieval-Augmented Generation (RAG)** applications. It seamlessly bridges your Eloquent models with cutting-edge Vector Databases and Frontier LLMs like **GPT-5.5**, **Gemini 2.5**, and **Claude 4.6**.

---

## 🛠️ Detailed Installation Guide

### 1. Requirements
- PHP 8.2 or higher
- Laravel 10.x, 11.x, or 12.x
- A vector store account (Upstash, Pinecone, or a local Chroma instance)

### 2. Install via Composer
```bash
composer require lemukarram/laravel-ai-rag
```

### 3. Publish Configuration
Publish the `vector-search.php` config file to your application:
```bash
php artisan vendor:publish --provider="LeMukarram\VectorSearch\VectorSearchServiceProvider"
```

---

## ⚙️ Exhaustive Configuration Reference

Open `config/vector-search.php` to manage your environment.

### 📂 Vector Store Settings
| Driver | Env Variable | Description |
|--------|--------------|-------------|
| **Global** | `VECTOR_STORE` | Default store to use (`upstash`, `pinecone`, `chroma`) |
| **Upstash** | `UPSTASH_VECTOR_URL` | Your Upstash Vector REST URL |
| **Upstash** | `UPSTASH_VECTOR_TOKEN` | Your Upstash REST Token |
| **Pinecone** | `PINECONE_API_KEY` | Your Pinecone API Key |
| **Pinecone** | `PINECONE_HOST` | The index host (e.g., `https://index-xyz.svc.pinecone.io`) |
| **Chroma** | `CHROMA_HOST` | Hostname (Default: `127.0.0.1`) |
| **Chroma** | `CHROMA_PORT` | Port (Default: `8000`) |
| **Chroma** | `CHROMA_COLLECTION` | Collection name (Default: `laravel-rag`) |

### 🧠 AI Model Settings
| Provider | Env Variable | Description |
|----------|--------------|-------------|
| **OpenAI** | `OPENAI_API_KEY` | Your OpenAI Secret Key |
| **Gemini** | `GEMINI_API_KEY` | Your Google AI (Gemini) API Key |
| **Anthropic** | `ANTHROPIC_API_KEY` | Your Anthropic API Key |
| **DeepSeek** | `DEEPSEEK_API_KEY` | Your DeepSeek API Key |

### ⚡ RAG Logic & Chunking
Configure these in the `rag` array of your config file:
- `chunk_size`: Maximum characters per vector (Default: `1000`)
- `chunk_overlap`: Character overlap between chunks (Default: `200`)
- `system_prompt`: The core instruction for the LLM. Supports `{{context}}` and `{{query}}` placeholders.

---

## 🚀 Pro Use Cases & Examples

### Use Case 1: Intelligent Documentation Search
Index your technical docs and allow users to ask questions.

```php
// In your Model
public function getVectorColumns(): array {
    return ['title', 'body', 'version_tag'];
}

// In your Controller
$answer = VectorSearch::whereMetadata('version_tag', 'v3.0')
    ->chat('How do I configure the new hybrid search?');
```

### Use Case 2: AI-Powered E-commerce Product Recommendations
Find products not just by name, but by "vibe" or semantic description.

```php
// Search for "summer vibe outdoor clothes"
$products = VectorSearch::withStore('pinecone') // Use high-performance index
    ->similar('Lightweight breathable clothing for hiking', topK: 10);
```

### Use Case 3: Legal/Medical Document Analysis
Use **Claude 4.6** for high-precision reasoning over complex text.

```php
$analysis = VectorSearch::withModel('anthropic')
    ->chat('Summarize the liability clauses in the retrieved contracts.');
```

### Use Case 4: Global Support Bot (Multi-Query Expansion)
When users ask vague questions, use `multiQuery` to find better answers.

```php
// Automatically generates 3 variations of the user's query
$results = VectorSearch::multiQuery('Payment failed'); 
```

---

## 🧪 Testing
```php
use LeMukarram\VectorSearch\Facades\VectorSearch;

public function test_it_works()
{
    $fake = VectorSearch::fake();
    $fake->pushChatResponse('Laravel AI RAG is awesome!');

    $response = VectorSearch::chat('What is this package?');
    
    $this->assertEquals('Laravel AI RAG is awesome!', $response->content());
}
```

---

## 📈 Search Performance & SEO Tips
Keywords: *Laravel AI, Vector Search Laravel, RAG Laravel, GPT-5 Laravel, Gemini AI Laravel, PHP AI Package, Semantic Search PHP, Pinecone Laravel, Upstash Vector Laravel, AI Embedding Laravel.*

---

## 🤝 Support & License
If you find this package useful, please **star the repository** on GitHub!
Licensed under the MIT License.
