<?php
namespace
use PHPUnit\Framework\TestCase;
use function Differ\Differ\isBool;
use function Differ\Differ\genDiff;
use function Differ\Differ\getArrayFromJson;
use function Differ\Differ\getFixtureFullPath;

final class DifferTest extends TestCase
{
    public function testIsBool(): void
    {
        $data = [
            'name' => 'John',
            'age' => 30,
            'isAdmin' => true,
            'gender' => null,
        ];

        $expected = [
            'name' => 'John',
            'age' => 30,
            'isAdmin' => 'true',
            'gender' => 'null',
        ];
        $result = isBool($data);

        $this->assertEquals($expected, $result);
    }


//    public function testGenDiff(): void
//    {
//        $filePath1 = getFixtureFullPath('file1.json');
//        $filePath2 = getFixtureFullPath('file1.json');
//        $format = 'stylish';
//
//
//        $expected = getFixtureFullPath('DifferTest.txt');
//        $result = genDiff($filePath1, $filePath2, $format);
//
//        $this->assertEquals($expected, $result);
//    }
}
