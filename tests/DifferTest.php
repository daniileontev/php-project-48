<?php

namespace Differ\Phpunit\Tests\DifferTest;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

final class DifferTest extends TestCase
{
    public function testGenDiff()
    {
        $jsonFilePath1 = 'tests/fixtures/file1.json';
        $jsonFilePath2 = 'tests/fixtures/file2.json';
        $yamlFilePath1 = 'tests/fixtures/file1.yaml';
        $ymlFilePath2 = 'tests/fixtures/file2.yml';
        $expected = '{
 - follow: false
   host: hexlet.io
 - proxy: 123.234.53.22
 - timeout: 50
 + timeout: 20
 + verbose: true
}';
        $this->assertEquals($expected, genDiff($jsonFilePath1, $jsonFilePath2));
        $this->assertEquals($expected, genDiff($yamlFilePath1, $ymlFilePath2));
    }
}
