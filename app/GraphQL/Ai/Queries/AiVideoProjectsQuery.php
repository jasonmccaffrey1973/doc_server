<?php

namespace App\GraphQL\Ai\Queries;

use App\GraphQL\Ai\AiVideoAccess;
use App\Models\AiVideoProject;

class AiVideoProjectsQuery
{
    use AiVideoAccess;

    public function __invoke(null $_, array $args)
    {
        return AiVideoProject::query()
            ->ownedBy($this->currentUserId())
            ->withCount('scenes')
            ->latest()
            ->get();
    }
}
