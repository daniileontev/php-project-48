<?php

namespace Differ\Differ;

use Exception;

/**
 * @throws Exception
 */
function genDiff(string $filePath1, string $filePath2, string $format = "stylish")
{
    $contentFromFile1 = getArrayFromJson($filePath1);
    $contentFromFile2 = getArrayFromJson($filePath2);
    
    $keys = array_merge((array_keys($contentFromFile1)), array_keys($contentFromFile2));
    sort($keys);
    $result = [];
    foreach ($keys as $key) {
        if (!array_key_exists($key, $contentFromFile1)) {
            $result[$key] = "added";
        } elseif (!array_key_exists($key, $contentFromFile2)) {
            $result[$key] = "deleted";
        } elseif ($contentFromFile1[$key] !== $contentFromFile2[$key]) {
            $result[$key] = "changed";
        } else {
            $result[$key] = "unchanged";
        }
    }
    echo "{" . "\n";
    foreach ($result as $key => $value) {
        switch ($value) {
            case "added":
                echo " + $key: $contentFromFile2[$key]" . "\n";
                break;
            case "deleted":
                echo " - $key: $contentFromFile1[$key]" . "\n";
                break;
            case "changed":
                echo " - $key: $contentFromFile1[$key]" . "\n";
                echo " + $key: $contentFromFile2[$key]" . "\n";
                break;
            case "unchanged":
                echo "   $key: $contentFromFile1[$key]" . "\n";
                break;
            default:
                throw new Exception("Invalid value!");
        }
    }
    echo "}" . "\n";
}

function getArrayFromJson($fileName)
{
    $file = file_get_contents($fileName);
    return json_decode($file, true);
}

//$file1 = "../files/file1.json";
//$file2 = "../files/file2.json";
//genDiff($file1, $file2);
