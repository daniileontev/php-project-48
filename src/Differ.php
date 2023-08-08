<?php

namespace Differ\Differ;

use Exception;

function isBool(array $array): array
{
    return array_map(function ($value) {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        } elseif (is_null($value)) {
            return 'null';
        } else {
            return $value;
        }
    }, $array);
}

/**
 * @throws Exception
 */
function genDiff(string $filePath1, string $filePath2, string $format = "stylish")
{
    $contentFromFile1 = getArrayFromJson($filePath1);
    $contentFromFile2 = getArrayFromJson($filePath2);
    $contentFromFile1 = isBool($contentFromFile1);
    $contentFromFile2 = isBool($contentFromFile2);

    $keys = array_merge((array_keys($contentFromFile1)), array_keys($contentFromFile2));
    sort($keys);
    $tags = [];
    $result = [];

    foreach ($keys as $key) {
        if (!array_key_exists($key, $contentFromFile1)) {
            $tags[$key] = "added";
        } elseif (!array_key_exists($key, $contentFromFile2)) {
            $tags[$key] = "deleted";
        } elseif ($contentFromFile1[$key] !== $contentFromFile2[$key]) {
            $tags[$key] = "changed";
        } else {
            $tags[$key] = "unchanged";
        }
    }
    $result = ["{"];
    foreach ($tags as $key => $value) {
        switch ($value) {
            case "added":
                $result[] = " + $key: $contentFromFile2[$key]";
                break;
            case "deleted":
                $result[] = " - $key: $contentFromFile1[$key]";
                break;
            case "changed":
                $result[] = " - $key: $contentFromFile1[$key]";
                $result[] = " + $key: $contentFromFile2[$key]";
                break;
            case "unchanged":
                $result[] = "   $key: $contentFromFile1[$key]";
                break;
            default:
                throw new Exception("Invalid value!");
        }
    }
    $result[] = "}";

    return implode("\n", $result);
}

function getArrayFromJson($fileName)
{
    $file = file_get_contents($fileName);
    return json_decode($file, true);
}

