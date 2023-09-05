<?php

namespace Differ\Phpunit\Tests\DifferTest;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

final class DifferTest extends TestCase
{
    public function getFixtureFullPath($fixtureName): bool|string
    {
        $parts = [__DIR__, 'fixtures', $fixtureName];
        return realpath(implode('/', $parts));
    }

    public function testGenDiffJson()
    {
        $expected = file_get_contents($this->getFixtureFullPath("expectedStylish"));
        $this->assertEquals($expected, genDiff(
            $this->getFixtureFullPath("file3.json"),
            $this->getFixtureFullPath("file4.json")
        ));
    }

    public function testGenDiffYaml()
    {
        $expected = file_get_contents($this->getFixtureFullPath("expectedStylish"));
        $this->assertEquals($expected, genDiff(
            $this->getFixtureFullPath("file3.yaml"),
            $this->getFixtureFullPath("file4.yml"),
            'stylish'
        ));
    }

    public function testPlainDiff()
    {
        $expected = file_get_contents($this->getFixtureFullPath("expectedPlain"));
        $this->assertEquals($expected, genDiff(
            $this->getFixtureFullPath("file3.json"),
            $this->getFixtureFullPath("file4.json"),
            "plain"
        ));
    }

    public function testJsonDiff()
    {
        $expected = file_get_contents($this->getFixtureFullPath("expectedJson"));
        $this->assertJsonStringEqualsJsonString($expected, genDiff(
            $this->getFixtureFullPath("file3.json"),
            $this->getFixtureFullPath("file4.json"),
            "json"
        ));
    }
}
