<?php

namespace App\GraphQL\Mutations;

use App\Models\StorageLocation;
use Illuminate\Support\Facades\Validator;

class UpdateStorageLocationMutation
{
    /**
     * @param array{location: string} $args
     * @return array{success: bool, location: string}
     */
    public function __invoke(null $_, array $args): array
    {
        Validator::make($args, [
            'location' => ['required', 'string', 'exists:storage_locations,id'],
        ])->validate();

        // Set the specified location as default and unset others
        StorageLocation::where('is_default', true)->update(['is_default' => false]);
        
        $storageLocation = StorageLocation::findOrFail($args['location']);
        $storageLocation->update(['is_default' => true]);

        return [
            'success' => true,
            'location' => $storageLocation->id,
        ];
    }
}
