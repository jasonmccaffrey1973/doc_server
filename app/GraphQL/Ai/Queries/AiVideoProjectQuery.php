<?php

namespace App\GraphQL\Ai\Queries;

use App\GraphQL\Ai\AiVideoAccess;

class AiVideoProjectQuery
{
    use AiVideoAccess;

    public function __invoke(null $_, array $args)
    {
        return $this->ownedProject($args['id'])->load(['scenes.generationJobs']);
    }
}
