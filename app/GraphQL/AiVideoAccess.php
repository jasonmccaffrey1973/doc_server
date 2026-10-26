<?php

namespace App\GraphQL;

use App\Models\AiVideoProject;
use App\Models\AiVideoScene;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;

trait AiVideoAccess
{
    protected function currentUserId(): string
    {
        $userId = Auth::id();

        if (! $userId) {
            throw new AuthenticationException('Authentication is required.');
        }

        return (string) $userId;
    }

    protected function ownedProject(int|string $id): AiVideoProject
    {
        return AiVideoProject::query()->ownedBy($this->currentUserId())->findOrFail($id);
    }

    protected function ownedScene(int|string $id): AiVideoScene
    {
        return AiVideoScene::query()
            ->whereHas('project', fn ($query) => $query->ownedBy($this->currentUserId()))
            ->findOrFail($id);
    }
}
