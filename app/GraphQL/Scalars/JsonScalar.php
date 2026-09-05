<?php

namespace App\GraphQL\Scalars;

use GraphQL\Language\AST\BooleanValueNode;
use GraphQL\Language\AST\FloatValueNode;
use GraphQL\Language\AST\IntValueNode;
use GraphQL\Language\AST\ListValueNode;
use GraphQL\Language\AST\Node;
use GraphQL\Language\AST\NullValueNode;
use GraphQL\Language\AST\ObjectFieldNode;
use GraphQL\Language\AST\ObjectValueNode;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Language\AST\VariableNode;
use GraphQL\Type\Definition\ScalarType;

class JsonScalar extends ScalarType
{
    public string $name = 'JSON';

    public ?string $description = 'Arbitrary JSON value represented as scalar.';

    /**
     * @return mixed
     */
    public function serialize(mixed $value): mixed
    {
        return $value;
    }

    /**
     * @return mixed
     */
    public function parseValue(mixed $value): mixed
    {
        return $value;
    }

    /**
     * @param  array<string, mixed>|null  $variables
     * @return mixed
     */
    public function parseLiteral(Node $valueNode, ?array $variables = null): mixed
    {
        if ($valueNode instanceof VariableNode) {
            return $variables[$valueNode->name->value] ?? null;
        }

        if ($valueNode instanceof NullValueNode) {
            return null;
        }

        if ($valueNode instanceof StringValueNode) {
            return $valueNode->value;
        }

        if ($valueNode instanceof BooleanValueNode) {
            return $valueNode->value;
        }

        if ($valueNode instanceof IntValueNode) {
            return (int) $valueNode->value;
        }

        if ($valueNode instanceof FloatValueNode) {
            return (float) $valueNode->value;
        }

        if ($valueNode instanceof ListValueNode) {
            return array_map(
                fn (Node $node): mixed => $this->parseLiteral($node, $variables),
                $valueNode->values
            );
        }

        if ($valueNode instanceof ObjectValueNode) {
            $values = [];

            foreach ($valueNode->fields as $field) {
                if (! $field instanceof ObjectFieldNode) {
                    continue;
                }

                $values[$field->name->value] = $this->parseLiteral($field->value, $variables);
            }

            return $values;
        }

        return null;
    }
}
