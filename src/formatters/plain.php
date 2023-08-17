<?php

namespace Differ\Formatters\Plain;

use function Differ\Parser\getNormalValue;

function getPlain($diff, $nextKey = ""): string
{
    $newProperties = array_map(function ($node) use ($nextKey) {
        $key = $node['key'];
        $type = $node['type'];
        $value1 = $node['value1'];
        $value2 = $node['value2'];

        if ($nextKey === "") {
            $newKey = $key;
        } else {
            $newKey = $nextKey . "." . $key;
        }

        switch ($type) {
            case "nested":
                return getPlain($value1, $newKey);
            case "removed":
                return "Property '$newKey' was removed";
            case "added":
                $normalValue = getNormalValue($value2);
                return "Property '$newKey' was added with value: " . $normalValue;
            case "updated":
                $normalValue1 = getNormalValue($value1);
                $normalValue2 = getNormalValue($value2);
                return "Property '$newKey' was updated. From $normalValue1 to $normalValue2";
            case "unchanged":
                break;
            default:
                throw new \Exception("Unknown type - " . $type);
        }
    }, $diff);

    return implode("\n", array_filter($newProperties));
}
