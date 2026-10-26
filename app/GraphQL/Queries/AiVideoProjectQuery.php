<?php

namespace App\GraphQL\Queries;

use App\GraphQL\AiVideoAccess;

class AiVideoProjectQuery
{
    use AiVideoAccess;

    public function __invoke(null $_, array $args)
    {
        return $this->ownedProject($args['id'])->load(['scenes.generationJobs']);
    }
}
