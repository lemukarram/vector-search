<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Vector Store
    |--------------------------------------------------------------------------
    | This is the vector database your package will use by default.
    | It must match one of the connections in the 'stores' array.
    |
    | Options: 'upstash', 'chroma', 'pinecone'
    */
    'default_store' => env('VECTOR_STORE', 'upstash'),

    /*
    |--------------------------------------------------------------------------
    | Default AI Models
    |--------------------------------------------------------------------------
    | Select the default AI models for embedding (vectors) and chat.
    | These must match a key in the 'models' array.
    */
    'default_models' => [
        'embedding' => env('VECTOR_EMBEDDING_MODEL', 'openai'),
        'chat'      => env('VECTOR_CHAT_MODEL', 'openai'),
    ],

    /*
    |--------------------------------------------------------------------------
    | RAG Configuration
    |--------------------------------------------------------------------------
    */
    'rag' => [
        'system_prompt' => "You are a helpful assistant. Answer the user's question based ONLY on the following context:\n\nContext:\n{{context}}",
        'no_context_message' => "No relevant context found.",
    ],

    /*
    |--------------------------------------------------------------------------
    | Caching
    |--------------------------------------------------------------------------
    | Specify how long (in seconds) embeddings and chat results should be cached.
    | Set to 0 or null to disable caching.
    */
    'cache_ttl' => env('VECTOR_CACHE_TTL', 3600), // 1 hour

    /*
    |--------------------------------------------------------------------------
    | Vector Store Connections
    |--------------------------------------------------------------------------
    | Configure your vector database drivers here.
    |
    | - 'upstash': Easiest to start. Serverless, free tier.
    | - 'chroma': Best open-source, self-hosted option.
    | - 'pinecone': Best-in-class managed service for production.
    */
    'stores' => [
        'upstash' => [
            'driver'   => 'upstash',
            'url'      => env('UPSTASH_VECTOR_URL'),     // e.g., https://your-db.upstash.io
            'token'    => env('UPSTASH_VECTOR_TOKEN'),  // e.g., Aaa...
        ],
        'chroma' => [
            'driver'     => 'chroma',
            'host'       => env('CHROMA_HOST', '127.0.0.1'),
            'port'       => env('CHROMA_PORT', 8000),
            'collection' => env('CHROMA_COLLECTION', 'laravel-rag'),
            // 'token'      => env('CHROMA_TOKEN'), // Add if you have auth
        ],
        'pinecone' => [
            'driver'   => 'pinecone',
            'api_key'  => env('PINECONE_API_KEY'),
            'host'     => env('PINECONE_HOST'), // e.g., https://your-index.svc.us-west1-gcp.pinecone.io
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Model Connections
    |--------------------------------------------------------------------------
    | Configure the AI models for embedding and chat.
    */
    'models' => [
        'openai' => [
            'driver'          => 'openai',
            'api_key'         => env('OPENAI_API_KEY'),
            'embedding_model' => 'text-embedding-3-large',
            'chat_model'      => 'gpt-5.5', // Frontier model
        ],
        'gemini' => [
            'driver'          => 'gemini',
            'api_key'         => env('GEMINI_API_KEY'),
            'embedding_model' => 'text-embedding-004',
            'chat_model'      => 'gemini-2.5-flash',
        ],
        'deepseek' => [
            'driver'          => 'deepseek',
            'api_key'         => env('DEEPSEEK_API_KEY'),
            'embedding_model' => 'deepseek-embedder-v1',
            'chat_model'      => 'deepseek-v4-flash',
        ],
        'anthropic' => [
            'driver'     => 'anthropic',
            'api_key'    => env('ANTHROPIC_API_KEY'),
            'chat_model' => 'claude-sonnet-4-6',
            'version'    => '2023-06-01',
        ],
    ],
];
