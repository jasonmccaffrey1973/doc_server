<?php

namespace App\GraphQL\Queries;

use App\GraphQL\AiVideoAccess;
use App\Models\AiProvider;

class AiProvidersQuery
{
    use AiVideoAccess;

    public function __invoke(null $_, array $args)
    {
        $this->currentUserId();

        return AiProvider::query()->where('is_enabled', true)->orderBy('name')->get();
    }
}
