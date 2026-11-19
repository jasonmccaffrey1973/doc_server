<?php

namespace App\GraphQL\Ai\Queries;

use App\GraphQL\Ai\AiVideoAccess;
use App\Models\AiProviderConnection;

class AiProviderConnectionsQuery
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
