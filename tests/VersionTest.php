<?php

namespace Tests;

use App\Repositories\KvpRepository;
use App\Services\VersionService;

class VersionTest extends TestCase
{
    private $kvpRepo;

    public function setUp(): void
    {
        parent::setUp();

        $this->kvpRepo = app(KvpRepository::class);
    }

    /**
     * Test that the new versions (keys) are properly regarded as new versions
     */
    public function testGreaterThanVersionStrings(): void
    {
        $test = [
            ['7.0.0' => '6.0.0'],
            ['7.0.0+1231s' => '6.0.0'],
            // ['7.0.0-beta' => '7.0.0-dev'],
            ['7.0.0-beta' => '7.0.0-alpha'],
            ['7.0.0-beta.1'        => '7.0.0-beta'],
            ['7.0.0-beta.2'        => '7.0.0-beta.1'],
            ['7.0.0-beta.2+a34sdf' => '7.0.0-beta.1'],
        ];

        $versionSvc = app(VersionService::class);
        foreach ($test as $set) {
            $newVersion = array_key_first($set);
            $currentVersion = $set[$newVersion];

            $this->assertTrue(
                $versionSvc->isGreaterThan($newVersion, $currentVersion),
                "$newVersion not greater than $currentVersion"
            );
        }
    }

    public function testGetLatestVersion(): void
    {
        setting('general.check_prerelease_version', false);

        $this->mockGuzzleClient('releases.json');
        $versionSvc = app(VersionService::class);

        $str = $versionSvc->getLatestVersion();

        $this->assertEquals('7.0.0-alpha2', $str);
        $this->assertEquals('7.0.0-alpha2', $this->kvpRepo->get('latest_version_tag'));
    }

    public function testGetLatestPrereleaseVersion(): void
    {
        $this->updateSetting('general.check_prerelease_version', true);

        $this->mockGuzzleClient('releases.json');
        $versionSvc = app(VersionService::class);

        $str = $versionSvc->getLatestVersion();

        $this->assertEquals('7.0.0-beta', $str);
        $this->assertEquals('7.0.0-beta', $this->kvpRepo->get('latest_version_tag'));
    }

    public function testNewVersionNotAvailable(): void
    {
        $this->updateSetting('general.check_prerelease_version', false);

        $versions = [
            'v7.0.0',
            '7.0.0',
            '8.0.0',
            '7.0.0-beta',
            '7.0.0+buildid',
        ];

        foreach ($versions as $v) {
            $this->mockGuzzleClient('releases.json');
            $versionSvc = app(VersionService::class);
            $this->assertFalse($versionSvc->isNewVersionAvailable($v));
        }
    }

    /**
     * Version in the prerelease releases.json is v7.0.0-beta
     */
    public function testNewVersionIsAvailable(): void
    {
        $this->updateSetting('general.check_prerelease_version', true);

        $versions = [
            'v6.0.1',
            '6.0.0',
            '7.0.0-alpha',
        ];

        foreach ($versions as $v) {
            $this->mockGuzzleClient('releases.json');
            $versionSvc = app(VersionService::class);
            $this->assertTrue($versionSvc->isNewVersionAvailable($v));
        }
    }
}
