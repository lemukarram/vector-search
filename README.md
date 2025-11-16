
# Laravel Vector Search (RAG)

[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![Author](https://img.shields.io/badge/Author-Tech%20with%20muk-red.svg)](https://github.com/lemukarram)

A powerful, driver-based RAG (Retrieval-Augmented Generation) package for Laravel. Give your Eloquent models a "long-term memory" and build powerful, context-aware AI chatbots in minutes.

Developed by **[Mukarram Hussain](https://github.com/lemukarram)** of **Tech with muk**.

---

## The Concept

This package makes Retrieval-Augmented Generation (RAG) dead simple.

1.  **Sync:** You add a simple `VectorSearchable` trait to your `Post`, `Product`, or `User` models.
2.  **Magic:** The package automatically syncs your model data with a vector database (like Upstash, Chroma, or Pinecone) every time a model is `saved` or `deleted`.
3.  **Search:** Use `VectorSearch::similar()` to find Eloquent models that are semantically similar to a user's query.
4.  **Chat:** Use `VectorSearch::chat()` to get a direct answer from an AI (like Gemini, OpenAI, or DeepSeek) that uses your model data as its *only* context.

## Features

- 🧠 **Eloquent Auto-Sync:** Just add a Trait to your models.
- 🚀 **Driver-Based Architecture:**
    - **Vector Stores:** Upstash, Chroma, and Pinecone (fully extensible).
    - **AI Models:** OpenAI, Gemini, and DeepSeek (fully extensible).
- 📈 **RAG Out-of-the-Box:** A simple `VectorSearch::chat()` method provides full RAG.
- ✨ **Clean Facade:** A powerful, simple API.

## ⚠️ Important: Vector Dimensions

Before you start, you must understand **Dimensions**. Different AI models output vectors of different sizes. Your Vector Database Index **must** match the dimension size of your chosen AI Model.

| AI Provider | Model Name | Dimension Size |
| :--- | :--- | :--- |
| **OpenAI** | `text-embedding-3-small` | **1536** |
| **Gemini** | `text-embedding-004` | **768** |
| **DeepSeek** | `deepseek-embedder` | **1024** (Check docs) |

**Crucial:** If you create an Upstash index with 1536 dimensions for OpenAI, and then switch your `.env` to use Gemini (768), **it will fail**. You must create a separate index for each model type.

## Installation

1.  **Install via Composer**
    ```bash
    composer require lemukarram/vector-search
    ```
    *(Note: If installing from a local path during development, add the repository to your composer.json first).*

2.  **Publish the Config File**
    ```bash
    php artisan vendor:publish --tag="vector-search-config"
    ```

3.  **Add Environment Variables**
    Add your chosen driver keys to your `.env` file.

    **Option A: Using OpenAI (1536 Dimensions)**
    ```dotenv
    VECTOR_STORE=upstash
    UPSTASH_VECTOR_URL=[https://your-openai-index.upstash.io](https://your-openai-index.upstash.io)
    UPSTASH_VECTOR_TOKEN=your_token

    VECTOR_EMBEDDING_MODEL=openai
    VECTOR_CHAT_MODEL=openai
    OPENAI_API_KEY=sk-...
    ```

    **Option B: Using Google Gemini (768 Dimensions)**
    ```dotenv
    VECTOR_STORE=upstash
    # Make sure this Upstash Index was created with 768 dimensions!
    UPSTASH_VECTOR_URL=[https://your-gemini-index.upstash.io](https://your-gemini-index.upstash.io)
    UPSTASH_VECTOR_TOKEN=your_token

    VECTOR_EMBEDDING_MODEL=gemini
    VECTOR_CHAT_MODEL=gemini
    GEMINI_API_KEY=AIza...
    ```

## How to Use

### Step 1: "Teach" Your Models

Add the `VectorSearchable` trait to any Eloquent model and define the `getVectorColumns()` method.

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use LeMukarram\VectorSearch\Traits\VectorSearchable;

class Post extends Model
{
    use VectorSearchable;

    /**
     * Tell the AI which columns to read from this model.
     */
    public function getVectorColumns(): array
    {
        // These columns will be combined, vectorized, and stored
        return ['title', 'slug', 'content', 'category'];
    }
}
````

That's it\! Now, every time you create or update a `Post`, it will be automatically "taught" to your AI.

To sync all your existing posts manually, you can run this in `php artisan tinker`:

```php
\App\Models\Post::all()->each->save();
```

### Step 2: Use the `VectorSearch` Facade

You now have two powerful methods available anywhere in your app.

#### A) Semantic Search (`::similar`)

Find the most relevant *Eloquent models* for a search query.

```php
use LeMukarram\VectorSearch\Facades\VectorSearch;

Route::get('/search', function (Request $request) {
    $query = $request->input('q', 'how to use laravel');
    
    // This returns a real Eloquent Collection of Post models!
    $posts = VectorSearch::similar($query, 5);

    return view('search-results', ['posts' => $posts]);
});
```

#### B) AI Chat (`::chat`)

Get a direct, AI-generated answer based *only* on your database content (RAG).

```php
use LeMukarram\VectorSearch\Facades\VectorSearch;

Route::get('/ask', function (Request $request) {
    $question = $request->input('q', 'How do I install this package?');
    
    // 1. Searches your database for context
    // 2. Sends context + question to LLM
    // 3. Returns the answer
    $answer = VectorSearch::chat($question);

    return view('chat-answer', ['answer' => $answer]);
});
```

## Extending The Package

You can easily add your own drivers (e.g., for `Qdrant` or a custom LLM) in your `AppServiceProvider`.

```php
use LeMukarram\VectorSearch\Facades\VectorSearch;
use App\MyDrivers\QdrantDriver;

public function boot()
{
    // Add a custom vector store
    VectorSearch::store()->extend('qdrant', function ($app, $config) {
        return new QdrantDriver($config);
    });
}
```

## License

The MIT License (MIT).

```
```
