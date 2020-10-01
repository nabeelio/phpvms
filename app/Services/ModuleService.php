<?php

namespace App\Services;

use App\Contracts\Service;
use App\Models\Module;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
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
     */
    public function addAdminLink(string $title, string $url)
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

    public function createModule($array)
    {
        $orig_file = $array[0];
        $file_ext = $orig_file->getClientOriginalExtension();
        if ($file_ext !== 'zip' || $file_ext !== 'tar') {
            return false;
        }

        $zipper = null;
        if ($file_ext === 'tar') {
            $zipper = new PharData($orig_file);
        }
        if ($file_ext === 'zip') {
            $madZipper = new Madzipper();

            try {
                $zipper = $madZipper->make($orig_file);
            } catch (Exception $e) {
                Log::emergency('Could not extract zip file.');
            }
        }

        $temp = storage_path('/app/temp_modules');

        try {
            $zipper->extractTo($temp);
        } catch (Exception $e) {
            Log::emergency('Cannot Extract Module!');
        }

        $module = '';

        $root_files = scandir($temp);

        if (!in_array('module.json', $root_files)) {
            $temp .= '/'.$root_files[2];
        }

        foreach (glob($temp.'/*.json') as $file) {
            if (Str::contains($file, 'module.json')) {
                $json = json_decode(file_get_contents($file), true);
                $module = $json['name'];
            }
        }

        if ($module === '') {
            return false;
        }
        $toCopy = base_path().'/modules/'.$module;
        if (File::exists($toCopy)) {
            return false;
        }
        File::moveDirectory($temp, $toCopy);

        Artisan::call('config:cache');

        (new Module())->create([
            'name' => $module,
            'enabled' => $array[1],
        ]);
        return true;
    }
}
