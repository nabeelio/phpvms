<?php

namespace App\Services;

use App\Contracts\Service;
use App\Models\Module;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Madnest\Madzipper\Madzipper;
use PharData;

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
     * @param bool   $logged_in
     */
    public function addFrontendLink(string $title, string $url, string $icon = 'pe-7s-users', $logged_in = true)
    {
        self::$frontendLinks[$logged_in][] = [
            'title' => $title,
            'url'   => $url,
            'icon'  => $icon,
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
    public function addAdminLink(string $title, string $url, string $icon = 'pe-7s-users')
    {
        self::$adminLinks[] = [
            'title' => $title,
            'url'   => $url,
            'icon'  => $icon,
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

    public function createModule($array): bool
    {
        $orig_file = $array['file'];
        $file_ext = $orig_file->getClientOriginalExtension();
        $allowed_extensions = ['zip', 'tar', 'gz'];

        if (!in_array($file_ext, $allowed_extensions)) {
            return false;
        }

        $module = null;
        $temp = storage_path('app/tmp');
        $zipper = null;

        if ($file_ext === 'tar' || $file_ext === 'gz') {
            $zipper = new PharData($orig_file);
            $zipper->decompress();
        }

        if ($file_ext === 'zip') {
            $madZipper = new Madzipper();

            try {
                $zipper = $madZipper->make($orig_file);
            } catch (Exception $e) {
                Log::emergency('Could not extract zip file.');
            }
        }

        try {
            $zipper->extractTo($temp);
        } catch (Exception $e) {
            Log::emergency('Cannot Extract Module!');
        }

        if (!File::exists($temp.'/module.json')) {
            $directories = Storage::directories('tmp');
            $temp = storage_path('app').'/'.$directories[0];
        }

        $file = $temp.'/module.json';

        if (File::exists($file)) {
            $json = json_decode(file_get_contents($file), true);
            $module = $json['name'];
        } else {
            return false;
        }

        if (!$module) {
            return false;
        }

        $toCopy = base_path().'/modules/'.$module;

        if (File::exists($toCopy)) {
            return false;
        }

        File::moveDirectory($temp, $toCopy);

        $this->addModule($module, $array['enabled']);

        Artisan::call('config:cache');
        return true;
    }

    public function updateModule($id, $status): bool
    {
        $module = (new Module())->find($id);
        $module->update([
            'enabled' => $status,
        ]);
        return true;
    }

    public function deleteModule($id, $data): bool
    {
        $module = (new Module())->find($id);
        if ($data['verify'] === strtoupper($module->name)) {
            try {
                $module->delete();
            } catch (Exception $e) {
                Log::emergency('Cannot Delete Module!');
            }
            $moduleDir = base_path().'/modules/'.$module->name;
            if (File::exists($moduleDir)) {
                File::deleteDirectory($moduleDir);
            }
            return true;
        }
        return false;
    }

    public function enableModule($name): bool
    {
        return $this->addModule($name, true);
    }

    public function addModule($name, $status): bool
    {
        (new Module())->create([
            'name'    => $name,
            'enabled' => $status,
        ]);
        return true;
    }
}
