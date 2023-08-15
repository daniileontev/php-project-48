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
        $expected = file_get_contents($this->getFixtureFullPath("differJson.txt"));
        $this->assertEquals($expected, genDiff(
            $this->getFixtureFullPath("file1.json"),
            $this->getFixtureFullPath("file2.json"),
            'stylish'
        ));
    }

    public function testGenDiffYaml()
    {
        $expected = file_get_contents($this->getFixtureFullPath("differJson.txt"));
        $this->assertEquals($expected, genDiff(
            $this->getFixtureFullPath("yamlFile1.yaml"),
            $this->getFixtureFullPath("ymlFile2.yml"),
            'stylish'
        ));
    }
}
