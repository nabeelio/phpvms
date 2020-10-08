<?php

namespace App\Support;

use App\Services\VersionService;
use Codedge\Updater\Contracts\GithubRepositoryTypeContract;
use Codedge\Updater\SourceRepositoryTypes\GithubRepositoryType;
use Codedge\Updater\Traits\SupportPrivateAccessToken;
use Codedge\Updater\Traits\UseVersionFile;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;

/**
 * Use SourceRepositoryTypes/GithubRepositoryTypes/GithubTagType.php as a reference
 * Just replace the new update checks, etc, with the VersionService stubs. They're
 * essentially the same except for the current version checks and all that. Adds some
 * additional logging too, but the base update method is from GithubRepositoryType
 */
final class VmsRepositoryType extends GithubRepositoryType implements GithubRepositoryTypeContract
{
    use UseVersionFile;
    use SupportPrivateAccessToken;
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var VersionService
     */
    protected $versionSvc;
    /**
     * @var string
     */
    private $storagePath;

    public function __construct(array $config, Client $client)
    {
        $this->config = $config;
        $this->client = $client;
        $this->storagePath = Str::finish($this->config['download_path'], DIRECTORY_SEPARATOR);
        $this->versionSvc = app(VersionService::class);
    }

    /**
     * Check repository if a newer version than the installed one is available.
     *
     * @param string $currentVersion
     *
     * @throws InvalidArgumentException
     * @throws Exception
     *
     * @return bool
     */
    public function isNewVersionAvailable(string $currentVersion = ''): bool
    {
        return $this->versionSvc->isNewVersionAvailable($currentVersion);
    }

    /**
     * Get the latest version that has been published in a certain repository.
     * Example: 2.6.5 or v2.6.5.
     *
     * @param string $prepend Prepend a string to the latest version
     * @param string $append  Append a string to the latest version
     *
     * @throws Exception
     *
     * @return string
     */
    public function getVersionAvailable(string $prepend = '', string $append = ''): string
    {
        return $this->versionSvc->getLatestVersion();
    }

    /**
     * Fetches the latest version. If you do not want the latest version, specify one and pass it.
     *
     * @param string $version
     *
     * @throws Exception
     *
     * @return void
     */
    public function fetch($version = ''): void
    {
        $response = $this->getRepositoryReleases();
        $releaseCollection = collect(\GuzzleHttp\json_decode($response->getBody()->getContents()));

        if ($releaseCollection->isEmpty()) {
            throw new Exception('Cannot find a release to update. Please check the repository you\'re pulling from');
        }

        if (!File::exists($this->storagePath)) {
            File::makeDirectory($this->storagePath, 493, true, true);
        }

        if (!empty($version)) {
            $release = $releaseCollection->where('name', $version)->first();
        } else {
            $release = $releaseCollection->first();
        }

        Log::info('Found release='.$release->name.', path='.$release->zipball_url);
        $storageFolder = $this->storagePath.$release->name.'-'.now()->timestamp;
        $storageFilename = $storageFolder.'.zip';

        if (!$this->isSourceAlreadyFetched($release->name)) {
            $this->downloadRelease($this->client, $release->zipball_url, $storageFilename);
            $this->unzipArchive($storageFilename, $storageFolder);
            $this->createReleaseFolder($storageFolder, $release->name);
        }
    }

    protected function getRepositoryReleases(): ResponseInterface
    {
        $url = self::GITHUB_API_URL
               .'/repos/'.$this->config['repository_vendor']
               .'/'.$this->config['repository_name']
               .'/tags';

        $headers = [];
        return $this->client->request('GET', $url, ['headers' => $headers]);
    }
}
