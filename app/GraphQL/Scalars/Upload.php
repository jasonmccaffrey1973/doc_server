<?php

namespace App\GraphQL\Scalars;

use GraphQL\Error\Error;
use GraphQL\Language\AST\Node;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Utils\Utils;
use Illuminate\Http\UploadedFile;

class Upload extends ScalarType
{
    /**
     * Scalar description
     *
     * @var ?string
     */
    public ?string $description = 'The `Upload` scalar type represents a file upload.';

    /**
     * Serialize an internal value to include in a response.
     *
     * @param UploadedFile|mixed $value
     * @return mixed
     */
    public function serialize($value)
    {
        return $value;
    }

    /**
     * Parse a value coming from a request.
     *
     * @param mixed $value
     * @return UploadedFile
     */
    public function parseValue($value)
    {
        if ($value instanceof UploadedFile) {
            return $value;
        }

        throw new Error(
            'Upload value expected to be an UploadedFile, got: ' . Utils::printSafe($value)
        );
    }

    /**
     * Parse a literal coming from GraphQL query string.
     *
     * @param Node $valueNode
     * @param ?array $variables
     * @return null
     */
    public function parseLiteral($valueNode, ?array $variables = null)
    {
        // Uploads are not expected in query literals
        return null;
    }
}
