# Laravel Vector RAG 🚀

[![Latest Version on Packagist](https://img.shields.io/packagist/v/lemukarram/laravel-vector-rag.svg?style=flat-square)](https://packagist.org/packages/lemukarram/laravel-vector-rag)
[![Total Downloads](https://img.shields.io/packagist/dt/lemukarram/laravel-vector-rag.svg?style=flat-square)](https://packagist.org/packages/lemukarram/laravel-vector-rag)
[![License](https://img.shields.io/packagist/l/lemukarram/laravel-vector-rag.svg?style=flat-square)](https://packagist.org/packages/lemukarram/laravel-vector-rag)

**Laravel Vector RAG** is an enterprise-grade Retrieval-Augmented Generation (RAG) package. It allows you to seamlessly synchronize your Eloquent models with cutting-edge vector databases and query them using next-gen LLMs like **GPT-5.5**, **Gemini 2.5**, and **Claude 4.6**.

Build AI-powered search, recommendation engines, and context-aware chatbots in minutes.

---

## 🔥 Key Features

- **Next-Gen LLM Support**: Native drivers for GPT-5.5, Gemini 2.5, Claude 4.6, and DeepSeek V4.
- **Advanced Retrieval**: Hybrid Search, Multi-Query Expansion, and Reciprocal Rank Fusion (RRF).
- **Smart Chunking**: Recursive character text splitting for high-density context.
- **Auto-Sync**: Automatically keep your vector store updated with Eloquent events.
- **Metadata Filtering**: Query precisely using model attributes.
- **Developer First**: Clean Facade API, Mockable Testing Suite (`VectorSearch::fake()`), and extensible drivers.

---

## 📦 Installation

```bash
composer require lemukarram/laravel-vector-rag
```

Publish the configuration file:

```bash
php artisan vendor:publish --provider="LeMukarram\VectorSearch\VectorSearchServiceProvider"
```

---

## ⚙️ Configuration

### 1. Vector Databases
Configure your preferred vector store in `config/vector-search.php`.

| Store | Driver | Features |
|-------|--------|----------|
| **Upstash** | `upstash` | Serverless, Metadata Filtering |
| **Pinecone** | `pinecone` | High Performance, Hybrid Search |
| **ChromaDB** | `chroma` | Open Source, Self-Hosted |

```php
'stores' => [
    'upstash' => [
        'driver' => 'upstash',
        'url'    => env('UPSTASH_VECTOR_URL'),
        'token'  => env('UPSTASH_VECTOR_TOKEN'),
    ],
    // ... other stores
],
```

### 2. Next-Gen AI Models
Support for the frontier of AI.

```php
'models' => [
    'openai' => [
        'driver'          => 'openai',
        'api_key'         => env('OPENAI_API_KEY'),
        'chat_model'      => 'gpt-5.5', // Frontier Model
        'embedding_model' => 'text-embedding-3-large',
    ],
    'gemini' => [
        'driver'          => 'gemini',
        'api_key'         => env('GEMINI_API_KEY'),
        'chat_model'      => 'gemini-2.5-flash',
        'embedding_model' => 'text-embedding-004',
    ],
    'anthropic' => [
        'driver'     => 'anthropic',
        'api_key'    => env('ANTHROPIC_API_KEY'),
        'chat_model' => 'claude-sonnet-4-6',
    ],
],
```

---

## 🚀 Usage

### Prepare Your Model
Add the `VectorSearchable` trait and define which columns to index.

```php
use LeMukarram\VectorSearch\Traits\VectorSearchable;

class Post extends Model
{
    use VectorSearchable;

    public function getVectorColumns(): array
    {
        return ['title', 'content', 'category'];
    }
}
```

### Search & RAG Chat
```php
use LeMukarram\VectorSearch\Facades\VectorSearch;

// 1. Simple Semantic Search
$posts = VectorSearch::similar('How to build AI apps?', topK: 5);

// 2. Filtered Search
$posts = VectorSearch::whereMetadata('category', 'tech')
    ->similar('RAG implementation', 3);

// 3. AI-Powered RAG Chat
$response = VectorSearch::chat('Explain the benefits of this package.');
echo $response->content();
echo $response->usage()['total_tokens']; // Track usage!

// 4. Multi-Query Expansion
$results = VectorSearch::multiQuery('Best vector DB for Laravel');
```

### Per-Request Overrides
Switch models or stores on the fly:
```php
$response = VectorSearch::withModel('anthropic')
    ->withStore('pinecone')
    ->chat('Use Claude for this specific answer.');
```

---

## 🧪 Testing
We make testing easy. Don't waste credits during CI.

```php
public function test_ai_feature()
{
    $fake = VectorSearch::fake();
    $fake->pushChatResponse('This is a fake answer');

    $response = VectorSearch::chat('hello');
    
    $this->assertEquals('This is a fake answer', $response->content());
}
```

---

## 🤝 Contributing
Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## 📄 License
The MIT License (MIT). Please see [LICENSE.md](LICENSE.md) for more information.
