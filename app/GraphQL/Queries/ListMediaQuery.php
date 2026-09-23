<?php

namespace App\GraphQL\Queries;

use App\Models\Media;

class ListMediaQuery
{
    /**
     * @param array{kind?: string, limit?: int, offset?: int} $args
     * @return array{items: array, total: int}
     */
    public function __invoke(null $_, array $args): array
    {
        $query = Media::query();

        // Filter by media type/kind if provided
        if (!empty($args['kind'])) {
            $query->where('media_type', $args['kind']);
        }

        // Get total count before pagination
        $total = $query->count();

        // Apply limit and offset
        $limit = $args['limit'] ?? 25;
        $offset = $args['offset'] ?? 0;

        $items = $query
            ->offset($offset)
            ->limit($limit)
            ->latest('created_at')
            ->get()
            ->map(fn($media) => [
                'id' => $media->id,
                'kind' => $media->media_type,
                'name' => $media->original_name,
                'url' => $media->url,
                'mimeType' => $media->mime_type,
                'size' => $media->size,
                'altText' => $media->alt_text,
                'createdAt' => $media->created_at,
            ])
            ->toArray();

        return [
            'items' => $items,
            'total' => $total,
        ];
    }
}
