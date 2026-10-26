<?php

namespace App\GraphQL\Mutations;

use App\GraphQL\AiVideoAccess;
use App\Models\AiProviderConnection;

class RemoveAiProviderConnectionMutation
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
