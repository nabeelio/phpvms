<?php

namespace App\Services;

use App\Contracts\Service;
use App\Repositories\KvpRepository;
use App\Support\HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use SemVer\SemVer\Version;
use Symfony\Component\Yaml\Yaml;

class VersionService extends Service
{
    private $httpClient;
    private $kvpRepo;

    public function __construct(
        HttpClient $httpClient,
        KvpRepository $kvpRepo
    ) {
        $this->httpClient = $httpClient;
        $this->kvpRepo = $kvpRepo;
    }

    /**
     * Clean the version string (e.,g strip the v in front)
     *
     * @param string $version
     *
     * @return string
     */
    private function cleanVersionString($version): string
    {
        if ($version[0] === 'v') {
            $version = substr($version, 1);
        }

        return $version;
    }

    /**
     * Set the latest release version/tag into the KVP repo and return the tag
     *
     * @param $version_tag
     * @param $download_url
     *
     * @return string The version string
     */
    private function setLatestRelease($version_tag, $download_url): string
    {
        $version_tag = $this->cleanVersionString($version_tag);

        $this->kvpRepo->save('latest_version_tag', $version_tag);
        $this->kvpRepo->save('latest_version_url', $download_url);

        return $version_tag;
    }

    /**
     * Find and return the Github asset line
     *
     * @param $release
     *
     * @return string
     */
    private function getGithubAsset($release): string
    {
        foreach ($release['assets'] as $asset) {
            if ($asset['content_type'] === 'application/gzip') {
                return $asset['browser_download_url'];
            }
        }

        return '';
    }

    /**
     * Download the latest version from github
     */
    private function getLatestVersionGithub()
    {
        $releases = [];

        try {
            $releases = $this->httpClient->get(config('phpvms.version_file'), [
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);
        } catch (GuzzleException $e) {
            Log::error('Error retrieving new version: '.$e->getMessage());
        }

        $include_prerelease = setting('general.check_prerelease_version', false);
        foreach ($releases as $release) {
            if ($release['prerelease'] === true) {
                if ($include_prerelease) {
                    return $this->setLatestRelease(
                        $release['tag_name'],
                        $this->getGithubAsset($release)
                    );
                } else {
                    continue;
                }
            }

            return $this->setLatestRelease(
                $release['tag_name'],
                $this->getGithubAsset($release)
            );
        }

        return $releases;
    }

    /**
     * Downloads the latest version and saves it into the KVP store
     */
    public function getLatestVersion()
    {
        $latest_version = $this->getLatestVersionGithub();
        return $latest_version;
    }

    /**
     * Get the build ID, which is the date and the git log version
     * @param array $cfg
     *
     * @return string
     */
    public function getBuildId($cfg)
    {
        exec($cfg['git']['git-local'], $version);
        $version = substr($version[0], 0, $cfg['build']['length']);

        // prefix with the date in YYMMDD format
        $date = date('ymd');
        return $date.'.'.$version;
    }

    /**
     * Get the current version
     *
     * @param bool $include_build True will include the build ID
     *
     * @return string
     */
    public function getCurrentVersion($include_build = true)
    {
        $version_file = config_path('version.yml');
        $cfg = Yaml::parse(file_get_contents($version_file));

        $c = $cfg['current'];
        $version = "{$c['major']}.{$c['minor']}.{$c['patch']}";

        if ($include_build) {
            // Get the current build id
            $build_number = $this->getBuildId($cfg);
            $cfg['build']['number'] = $build_number;
            $version = $version.'+'.$build_number;
        }

        return $version;
    }

    /**
     * See if a new version is available. Saves a flag into the KVP store if there is
     *
     * @param null [$current_version]
     *
     * @return bool
     */
    public function isNewVersionAvailable($current_version = null)
    {
        if (!$current_version) {
            $current_version = $this->getCurrentVersion(false);
        } else {
            $current_version = $this->cleanVersionString($current_version);
        }

        $current_version = Version::fromString($current_version);
        $latest_version = Version::fromString($this->getLatestVersion());

        // Convert to semver
        if ($latest_version->isGreaterThan($current_version)) {
            $this->kvpRepo->save('new_version_available', true);
            return true;
        }

        $this->kvpRepo->save('new_version_available', false);
        return false;
    }
}
