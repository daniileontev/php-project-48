<?php

namespace Differ\Formatters\Plain;

function getNormalValue(mixed $value): string
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    if (is_null($value)) {
        return 'null';
    }

    if (is_string($value)) {
        return "'$value'";
    }

    if (is_array($value)) {
        return "[complex value]";
    }

    return $value;
}

function getPlain(array $diff, string $nextKey = ""): string
{
    $newProperties = array_map(function ($node) use ($nextKey) {
        $key = $node['key'];
        $type = $node['type'];
        $value1 = $node['value1'] ?? null;
        $value2 = $node['value2'] ?? null;

        $newKey = $nextKey === "" ? $key : "$nextKey.$key";

        switch ($type) {
            case "nested":
                return getPlain($value1, $newKey);
            case "removed":
                return sprintf("Property '%s' was removed", $newKey);
            case "added":
                return sprintf("Property '%s' was added with value: %s", $newKey, getNormalValue($value1));
            case "updated":
                return sprintf(
                    "Property '%s' was updated. From %s to %s",
                    $newKey,
                    getNormalValue($value1),
                    getNormalValue($value2)
                );
            case "unchanged":
                break;
            default:
                throw new \Exception("Unknown type - $type");
        }
    }, $diff);

    return implode("\n", array_filter($newProperties));
}
