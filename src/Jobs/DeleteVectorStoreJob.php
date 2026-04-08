<?php

namespace LeMukarram\VectorSearch\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use LeMukarram\VectorSearch\Core\VectorStoreManager;

class DeleteVectorStoreJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected string $vectorId
    ) {}

    public function handle(VectorStoreManager $store): void
    {
        $store->store()->delete([$this->vectorId]);
    }
}
