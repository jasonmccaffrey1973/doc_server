<?php

namespace App\GraphQL\Mutations;

use App\Models\StorageLocation;
use Illuminate\Support\Facades\Validator;

class ConfigureStorageLocationMutation
{
    /**
     * @param array{input: array{name: string, type: string, configuration?: array, isDefault?: bool}} $args
     */
    public function __invoke(null $_, array $args): StorageLocation
    {
        Validator::make($args['input'], [
            'name' => ['required', 'string', 'max:255', 'unique:storage_locations,name'],
            'type' => ['required', 'in:local,s3,gcs'],
            'configuration' => ['nullable', 'array'],
            'isDefault' => ['nullable', 'boolean'],
        ])->validate();

        $input = $args['input'];

        // If setting as default, unset all other defaults
        if ($input['isDefault'] ?? false) {
            StorageLocation::where('is_default', true)->update(['is_default' => false]);
        }

        return StorageLocation::create([
            'name' => $input['name'],
            'type' => $input['type'],
            'configuration' => $input['configuration'] ?? null,
            'is_default' => $input['isDefault'] ?? false,
        ]);
    }
}
