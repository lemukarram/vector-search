# Update 3: Advanced RAG Techniques (Part 1 - The Fundamentals)
**Focus:** Solving "Inflexible Embedding Logic" and "Basic Context".

## 1. Smart Chunking (Technique #1)
- Implement `RecursiveCharacterTextSplitter`.
- Support for `overlap` to maintain context between chunks.
- Allow models to define `getChunkableContent()` to return multiple chunks per record.

## 2. Parent-Document Retrieval (Technique #2 - Small-to-Large)
- Store small chunks (sentences) for better vector search accuracy.
- Link small chunks to a larger "Parent Document" (e.g., a whole paragraph or section).
- When a small chunk is found, the system retrieves the *larger context* to provide to the LLM.

## 3. Metadata Filtering (Safety & Precision)
- Allow developers to define `getVectorMetadata()` to explicitly control what is sent to the vector store.
- Implement `$query->whereMetadata('key', 'value')` on the `VectorSearch` facade.
