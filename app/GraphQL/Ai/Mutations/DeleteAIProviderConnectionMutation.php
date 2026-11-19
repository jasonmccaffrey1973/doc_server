<?php

namespace App\GraphQL\Ai\Mutations;

use App\GraphQL\Ai\AiVideoAccess;
use App\Models\AiProviderConnection;

class DeleteAIProviderConnectionMutation
{
    use AiVideoAccess;

    public function __invoke(null $_, array $args): bool
    {
        return (bool) AiProviderConnection::query()
            ->where('user_id', $this->currentUserId())
            ->findOrFail($args['id'])
            ->delete();
    }
}
