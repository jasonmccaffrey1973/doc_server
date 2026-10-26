<?php

namespace App\GraphQL\Queries;

use App\GraphQL\AiVideoAccess;
use App\Models\AiProviderConnection;

class MyAiProviderConnectionsQuery
{
    use AiVideoAccess;

    public function __invoke(null $_, array $args)
    {
        return AiProviderConnection::query()
            ->where('user_id', $this->currentUserId())
            ->with('provider')
            ->orderBy('created_at')
            ->get();
    }
}
