<?php

namespace Differ\Differ;


function getArrayDiff($arr1, $arr2)
{
    $result = [];

    foreach ($arr1 as $key => $value) {
        if (!array_key_exists($key, $arr2)) {
            $result[$key] = "- " . $value;
        }
    }

    foreach ($arr2 as $key => $value) {
        if (!array_key_exists($key, $arr1)) {
            $result[$key] = "+ " . $value;
        } elseif ($arr1[$key] !== $value) {
            $result[$key] = "- " . $arr1[$key] . "\n  + " . $value;
        }
    }

    return $result;
}

//$file1 = ['a' => 'pop', 'b' => 'yes', 'c' => 11];
//$file2 = ['b' => 'no', 'c' => 11, 'd' => 'false'];
//print_r(getArrayDiff($file1, $file2));

function getArrayFromJson($fileName)
{
    $file = file_get_contents($fileName);
    return json_decode($file, true);
}
//$file1 = "../files/file1.json";
//var_dump(getArrayFromJson($file1));

function genDiff($filePath1, $filePath2)
{
    $firstArrayFile = getArrayFromJson($filePath1);
    var_dump($firstArrayFile);
    $secondArrayFile = getArrayFromJson($filePath2);
    var_dump($secondArrayFile);
    $result = getArrayDiff($firstArrayFile, $secondArrayFile);
    return '{' . PHP_EOL . implode(PHP_EOL, $result) . PHP_EOL . '}';
}