<?php

namespace App\GraphQL\Ai\Queries;

use App\GraphQL\Ai\AiVideoAccess;
use App\Models\AiVideoGenerationJob;

class AiVideoGenerationJobQuery
{
    use AiVideoAccess;

    public function __invoke(null $_, array $args): AiVideoGenerationJob
    {
        return AiVideoGenerationJob::query()
            ->whereHas('scene.project', fn ($query) => $query->ownedBy($this->currentUserId()))
            ->findOrFail($args['id']);
    }
}
