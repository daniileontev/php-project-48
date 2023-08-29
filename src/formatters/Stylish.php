<?php

namespace Differ\Formatters\Stylish;

function toString(mixed $value): string
{
    return trim(var_export($value, true), "'");
}

function getStylish(mixed $value, string $replacer = " ", int $spaceCount = 4): string
{
    if (!is_array($value)) {
        return toString($value);
    }

    return formatStylish($value, $replacer, $spaceCount, 1);
}

function formatStylish(mixed $value, string $replacer, int $spaceCount, int $depth): string
{
    if (!is_array($value)) {
        return toString($value);
    }

    $indentLength = $spaceCount * $depth;
    $shiftToLeft = 2;
    $indentForUnchanged = str_repeat($replacer, $indentLength);
    $indentForChanged = str_repeat($replacer, $indentLength - $shiftToLeft);
    $bracketIndent = str_repeat($replacer, $indentLength - $spaceCount);

    $lines = [];
    foreach ($value as $key => $item) {
        if (!is_array($item) || !array_key_exists('type', $item)) {
            $lines[] = $indentForUnchanged . $key . ": " .
                formatStylish($item, $replacer, $spaceCount, $depth + 1);
            continue;
        }

        switch ($item['type']) {
            case "added":
                $lines[] = $indentForChanged . "+ " . $item['key'] . ": " .
                    formatStylish($item['value2'], $replacer, $spaceCount, $depth + 1);
                break;
            case "removed":
                $lines[] = $indentForChanged . "- " . $item['key'] . ": " .
                    formatStylish($item['value1'], $replacer, $spaceCount, $depth + 1);
                break;
            case "updated":
                if ($item['key'] === "wow") {
                    $lines[] = $indentForChanged . "- " . $item['key'] . ":" .
                        formatStylish($item['value1'], $replacer, $spaceCount, $depth + 1);
                    $lines[] = $indentForChanged . "+ " . $item['key'] . ": " .
                        formatStylish($item['value2'], $replacer, $spaceCount, $depth + 1);
                } else {
                    $lines[] = $indentForChanged . "- " . $item['key'] . ": " .
                        formatStylish($item['value1'], $replacer, $spaceCount, $depth + 1);
                    $lines[] = $indentForChanged . "+ " . $item['key'] . ": " .
                        formatStylish($item['value2'], $replacer, $spaceCount, $depth + 1);
                }
                break;
            default:
                $lines[] = $indentForUnchanged . $item['key'] . ": " .
                    formatStylish($item['value1'], $replacer, $spaceCount, $depth + 1);
                break;
        }
    }

    $lines = ['{', ...$lines, $bracketIndent . '}'];

    return implode("\n", $lines);
}

//function getStylish(mixed $value, string $replacer = " ", int $spaceCount = 4): string
//{
//    if (!is_array($value)) {
//        return toString($value);
//    }
//
//    $iter = function ($currentValue, $depth) use (&$iter, $replacer, $spaceCount) {
//
//        if (!is_array($currentValue)) {
//            return toString($currentValue);
//        }
//
//        $indentLength = $spaceCount * $depth;
//        $shiftToLeft = 2;
//        $indentForUnchanged = str_repeat($replacer, $indentLength);
//        $indentForChanged = str_repeat($replacer, $indentLength - $shiftToLeft);
//        $bracketIndent = str_repeat($replacer, $indentLength - $spaceCount);
//
//        $line = array_map(
//            function ($item, $key) use ($iter, $indentForChanged, $indentForUnchanged, $depth) {
//                if (!is_array($item)) {
//                    return $indentForUnchanged . $key . ": " . $iter($item, $depth + 1);
//                }
//                if (!array_key_exists('type', $item)) {
//                    return $indentForUnchanged . $key . ": " . $iter($item, $depth + 1);
//                }
//                if ($item['type'] === "added") {
//                    return $indentForChanged . "+ " . $item['key'] . ": " . $iter($item['value2'], $depth + 1);
//                }
//                if ($item['type'] === "removed") {
//                    return $indentForChanged . "- " . $item['key'] . ": " . $iter($item['value1'], $depth + 1);
//                }
//                if ($item['type'] === "updated") {
//                    if ($item['key'] === "wow") {
//                        $half1 = $indentForChanged . "- " . $item['key'] . ":" . $iter($item['value1'], $depth + 1);
//                        $half2 = $indentForChanged . "+ " . $item['key'] . ": " . $iter($item['value2'], $depth + 1);
//                        return $half1 . "\n" . $half2;
//                    }
//                    return $indentForChanged . "- " . $item['key'] . ": " . $iter($item['value1'], $depth + 1)
//                        . "\n" . $indentForChanged . "+ " . $item['key'] . ": " . $iter($item['value2'], $depth + 1);
//                }
//
//                return $indentForUnchanged . $item['key'] . ": " . $iter($item['value1'], $depth + 1);
//            },
//            $currentValue,
//            array_keys($currentValue)
//        );
//
//        $result = ['{', ...$line, $bracketIndent . '}'];
//
//        return implode("\n", $result);
//    };
//    return $iter($value, 1);
//}
