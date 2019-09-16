<?php

namespace App\Services;

use App\Contracts\Service;

class ModuleService extends Service
{
    protected static $adminLinks = [];

    /**
     * @var array 0 == logged out, 1 == logged in
     */
    protected static $frontendLinks = [
        0 => [],
        1 => [],
    ];

    /**
     * Add a module link in the frontend
     *
     * @param string $title
     * @param string $url
     * @param string $icon
     * @param mixed  $logged_in
     */
    public function addFrontendLink(string $title, string $url, string $icon = '', $logged_in = true)
    {
        self::$frontendLinks[$logged_in][] = [
            'title' => $title,
            'url'   => $url,
            'icon'  => 'pe-7s-users',
        ];
    }

    /**
     * Get all of the frontend links
     *
     * @param mixed $logged_in
     *
     * @return array
     */
    public function getFrontendLinks($logged_in): array
    {
        return self::$frontendLinks[$logged_in];
    }

    /**
     * Add a module link in the admin panel
     *
     * @param string $title
     * @param string $url
     * @param string $icon
     */
    public function addAdminLink(string $title, string $url, string $icon = '')
    {
        self::$adminLinks[] = [
            'title' => $title,
            'url'   => $url,
            'icon'  => 'pe-7s-users',
        ];
    }

    /**
     * Get all of the module links in the admin panel
     *
     * @return array
     */
    public function getAdminLinks(): array
    {
        return self::$adminLinks;
    }
}
