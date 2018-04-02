<?php
/**
 *
 */

namespace App\Support\Media;

use DateTimeInterface;
use Spatie\MediaLibrary\Exceptions\UrlCannotBeDetermined;
use Spatie\MediaLibrary\UrlGenerator\BaseUrlGenerator;

/**
 * Class PublicUrlGenerator
 * @package App\Support\Media
 */
class PublicUrlGenerator extends BaseUrlGenerator
{
    /**
     * Get the url for the profile of a media item.
     * @return string
     */
    public function getUrl(): string
    {
        return public_asset($this->getPathRelativeToRoot());
    }

    /**
     * Get the temporary url for a media item.
     *
     * @param DateTimeInterface $expiration
     * @param array             $options
     *
     * @return string
     */
    public function getTemporaryUrl(DateTimeInterface $expiration, array $options = []): string
    {
        throw UrlCannotBeDetermined::filesystemDoesNotSupportTemporaryUrls();
    }

    /**
     * Get the url to the directory containing responsive images.
     *
     * @return string
     */
    public function getResponsiveImagesDirectoryUrl(): string
    {
        return public_asset('/').'/'.$this->pathGenerator->getPathForResponsiveImages($this->media);
    }
}
