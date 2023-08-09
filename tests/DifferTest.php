<?php

namespace Differ\Phpunit\Tests\DifferTest;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

final class DifferTest extends TestCase
{
    public function testGenDiff()
    {
        $filePath1 = 'tests/fixtures/file1.json';
        $filePath2 = 'tests/fixtures/file2.json';
        $expected = '{
 - follow: false
   host: hexlet.io
 - proxy: 123.234.53.22
 - timeout: 50
 + timeout: 20
 + verbose: true
}';
        $this->assertEquals($expected, genDiff($filePath1, $filePath2));
    }
}
