<?php

namespace App\GraphQL\Queries;

use App\GraphQL\AiVideoAccess;
use App\Models\AiVideoScene;
use App\Models\Media;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AiGeneratedAssetsQuery
{
    use AiVideoAccess;

    /**
     * Return media assets referenced by the authenticated user's AI video scenes.
     * Scene outputs are the assets produced by the current generation/import flow;
     * character and location entries are scene-linked library references.
     *
     * @param array{type?: string|null, limit?: int, offset?: int} $args
     * @return array{total: int, items: array<int, array<string, mixed>>}
     */
    public function __invoke(null $_, array $args): array
    {
        $userId = $this->currentUserId();
        $validated = Validator::make($args, [
            'type' => ['nullable', 'string', Rule::in(['character', 'scene', 'equipment', 'location'])],
            'limit' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'offset' => ['sometimes', 'integer', 'min:0'],
        ])->validate();

        $types = isset($validated['type'])
            ? [$validated['type']]
            : ['character', 'scene', 'location', 'equipment'];

        $scenes = AiVideoScene::query()
            ->whereHas('project', fn ($query) => $query->ownedBy($userId))
            ->with('project')
            ->get();

        $references = [];
        foreach ($scenes as $scene) {
            foreach ([
                'character' => $scene->character_asset_id,
                'location' => $scene->location_asset_id,
                'scene' => $scene->output_asset_id,
            ] as $type => $mediaId) {
                if (in_array($type, $types, true) && $mediaId) {
                    $references[$type . ':' . $mediaId] = [
                        'type' => $type,
                        'media_id' => $mediaId,
                        'scene' => $scene,
                    ];
                }
            }
        }

        $mediaById = Media::query()
            ->where('user_id', $userId)
            ->whereIn('id', array_column($references, 'media_id'))
            ->get()
            ->keyBy('id');

        $items = [];
        foreach ($references as $reference) {
            $media = $mediaById->get($reference['media_id']);
            if (! $media) {
                continue;
            }

            $scene = $reference['scene'];
            $items[] = [
                'id' => $media->id,
                'type' => $reference['type'],
                'name' => $media->original_name,
                'url' => $media->url,
                'mimeType' => $media->mime_type,
                'size' => $media->size,
                'altText' => $media->alt_text,
                'createdAt' => $media->created_at,
                'projectId' => $scene->project->id,
                'projectTitle' => $scene->project->title,
                'sceneId' => $scene->id,
                'sceneTitle' => $scene->title,
            ];
        }

        usort($items, fn (array $a, array $b) => strcmp((string) $b['createdAt'], (string) $a['createdAt']));
        $total = count($items);
        $offset = $validated['offset'] ?? 0;
        $limit = $validated['limit'] ?? 25;

        return [
            'total' => $total,
            'items' => array_slice($items, $offset, $limit),
        ];
    }
}
