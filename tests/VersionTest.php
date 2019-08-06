<?php

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

    public function testGetLatestVersion()
    {
        setting('general.check_prerelease_version', false);

        $this->mockGuzzleClient('releases.json');
        $versionSvc = app(VersionService::class);

        $str = $versionSvc->getLatestVersion();

        $this->assertEquals('v7.0.0-alpha2', $str);
        $this->assertEquals('v7.0.0-alpha2', $this->kvpRepo->get('latest_version_tag'));
    }

    public function testGetLatestPrereleaseVersion()
    {
        $this->updateSetting('general.check_prerelease_version', true);

        $this->mockGuzzleClient('releases.json');
        $versionSvc = app(VersionService::class);

        $str = $versionSvc->getLatestVersion();

        $this->assertEquals('v7.0.0-beta', $str);
        $this->assertEquals('v7.0.0-beta', $this->kvpRepo->get('latest_version_tag'));
    }

    public function testNewVersionNotAvailable()
    {
        $this->updateSetting('general.check_prerelease_version', false);

        $versions = [
            'v7.0.0',
            '7.0.0',
            '8.0.0',
            '7.0.0-alpha',
            '7.0.0+buildid',
        ];

        foreach ($versions as $v) {
            $this->mockGuzzleClient('releases.json');
            $versionSvc = app(VersionService::class);
            $this->assertFalse($versionSvc->isNewVersionAvailable($v));
        }
    }

    public function testNewVersionIsAvailable()
    {
        $this->updateSetting('general.check_prerelease_version', true);

        $versions = [
            'v6.0.1',
//            '6.0.0',
            '7.0.0-alpha',
        ];

        foreach ($versions as $v) {
            $this->mockGuzzleClient('releases.json');
            $versionSvc = app(VersionService::class);
            $this->assertTrue($versionSvc->isNewVersionAvailable($v));
        }
    }
}
