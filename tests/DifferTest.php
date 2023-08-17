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
        $expected = file_get_contents($this->getFixtureFullPath("json.txt"));
        $this->assertEquals($expected, genDiff(
            $this->getFixtureFullPath("file1.json"),
            $this->getFixtureFullPath("file2.json")
        ));
    }

    public function testGenDiffYaml()
    {
        $expected = file_get_contents($this->getFixtureFullPath("json.txt"));
        $this->assertEquals($expected, genDiff(
            $this->getFixtureFullPath("yamlFile1.yaml"),
            $this->getFixtureFullPath("ymlFile2.yml"),
            'stylish'
        ));
    }

    public function testPlainDiff()
    {
        $expected = file_get_contents($this -> getFixtureFullPath("plain.txt"));
        $this-> assertEquals($expected, genDiff(
            $this->getFixtureFullPath("file1.json"),
            $this->getFixtureFullPath("file2.json"),
            "plain"
        ));
    }
}
