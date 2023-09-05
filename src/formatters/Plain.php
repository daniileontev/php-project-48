<?php

namespace Differ\Formatters\Plain;

function getNormalValue(mixed $value): mixed
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

        $newKey = $nextKey === "" ? $key : $nextKey . "." . $key;

        switch ($type) {
            case "nested":
                return getPlain($value1, $newKey);
            case "removed":
                return "Property '$newKey' was removed";
            case "added":
                return "Property '$newKey' was added with value: " . getNormalValue($value1);
            case "updated":
                return "Property '$newKey' was updated. From " . getNormalValue($value1)
                    . " to " . getNormalValue($value2);
            case "unchanged":
                break;
            default:
                throw new \Exception("Unknown type - " . $type);
        }
    }, $diff);

    return implode("\n", array_filter($newProperties));
}
