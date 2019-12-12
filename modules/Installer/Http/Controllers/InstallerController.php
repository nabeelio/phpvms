<?php

namespace Modules\Installer\Http\Controllers;

use App\Contracts\Controller;
use App\Facades\Utils;
use App\Models\User;
use App\Repositories\AirlineRepository;
use App\Services\AnalyticsService;
use App\Services\Installer\DatabaseService;
use App\Services\Installer\InstallerService;
use App\Services\Installer\MigrationService;
use App\Services\Installer\SeederService;
use App\Services\UserService;
use App\Support\Countries;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Modules\Installer\Services\ConfigService;
use Modules\Installer\Services\RequirementsService;
use Nwidart\Modules\Facades\Module;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class InstallerController extends Controller
{
    private $airlineRepo;
    private $analyticsSvc;
    private $dbSvc;
    private $envSvc;
    private $migrationSvc;
    private $reqSvc;
    private $seederSvc;
    private $userService;

    /**
     * InstallerController constructor.
     *
     * @param AirlineRepository   $airlineRepo
     * @param AnalyticsService    $analyticsSvc
     * @param DatabaseService     $dbService
     * @param ConfigService       $envService
     * @param MigrationService    $migrationSvc
     * @param RequirementsService $reqSvc
     * @param SeederService       $seederSvc
     * @param UserService         $userService
     */
    public function __construct(
        AirlineRepository $airlineRepo,
        AnalyticsService $analyticsSvc,
        DatabaseService $dbService,
        ConfigService $envService,
        MigrationService $migrationSvc,
        RequirementsService $reqSvc,
        SeederService $seederSvc,
        UserService $userService
    ) {
        $this->airlineRepo = $airlineRepo;
        $this->analyticsSvc = $analyticsSvc;
        $this->dbSvc = $dbService;
        $this->envSvc = $envService;
        $this->migrationSvc = $migrationSvc;
        $this->reqSvc = $reqSvc;
        $this->seederSvc = $seederSvc;
        $this->userService = $userService;

        \App\Support\Utils::disableDebugToolbar();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (config('app.key') !== 'base64:zdgcDqu9PM8uGWCtMxd74ZqdGJIrnw812oRMmwDF6KY=') {
            return view('installer::errors/already-installed');
        }

        return view('installer::install/index-start');
    }

    protected function testDb(Request $request)
    {
        $this->dbSvc->checkDbConnection(
            $request->post('db_conn'),
            $request->post('db_host'),
            $request->post('db_port'),
            $request->post('db_name'),
            $request->post('db_user'),
            $request->post('db_pass')
        );
    }

    /**
     * Check the database connection
     */
    public function dbtest(Request $request)
    {
        $status = 'success';  // success|warn|danger
        $message = 'Database connection looks good!';

        try {
            $this->testDb($request);
        } catch (\Exception $e) {
            $status = 'danger';
            $message = 'Failed! '.$e->getMessage();
        }

        return view('installer::install/dbtest', [
            'status'  => $status,
            'message' => $message,
        ]);
    }

    /**
     * Check if any of the items has been marked as failed
     *
     * @param array $arr
     *
     * @return bool
     */
    protected function allPassed(array $arr): bool
    {
        foreach ($arr as $item) {
            if ($item['passed'] === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Step 1. Check the modules and permissions
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function step1(Request $request)
    {
        $php_version = $this->reqSvc->checkPHPVersion();
        $extensions = $this->reqSvc->checkExtensions();
        $directories = $this->reqSvc->checkPermissions();

        // Only pass if all the items in the ext and dirs are passed
        $statuses = [
            $php_version['passed'] === true,
            $this->allPassed($extensions) === true,
            $this->allPassed($directories) === true,
        ];

        // Make sure there are no false values
        $passed = !\in_array(false, $statuses, true);

        return view('installer::install/steps/step1-requirements', [
            'php'         => $php_version,
            'extensions'  => $extensions,
            'directories' => $directories,
            'passed'      => $passed,
        ]);
    }

    /**
     * Step 2. Database Setup
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function step2(Request $request)
    {
        $db_types = ['mysql' => 'mysql', 'sqlite' => 'sqlite'];
        return view('installer::install/steps/step2-db', [
            'db_types' => $db_types,
        ]);
    }

    /**
     * Step 2a. Create the .env
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function envsetup(Request $request)
    {
        $log_str = $request->post();
        $log_str['db_pass'] = '';

        Log::info('ENV setup', $log_str);

        // Before writing out the env file, test the DB credentials
        try {
            $this->testDb($request);
        } catch (\Exception $e) {
            Log::error('Testing db before writing configs failed');
            Log::error($e->getMessage());

            flash()->error($e->getMessage());
            return redirect(route('installer.step2'))->withInput();
        }

        // Now write out the env file
        $attrs = [
            'SITE_NAME' => $request->post('site_name'),
            'SITE_URL'  => $request->post('site_url'),
            'DB_CONN'   => $request->post('db_conn'),
            'DB_HOST'   => $request->post('db_host'),
            'DB_PORT'   => $request->post('db_port'),
            'DB_NAME'   => $request->post('db_name'),
            'DB_USER'   => $request->post('db_user'),
            'DB_PASS'   => $request->post('db_pass'),
            'DB_PREFIX' => $request->post('db_prefix'),
        ];

        /*
         * Create the config files and then redirect so that the
         * framework can pickup all those configs, etc, before we
         * setup the database and stuff
         */
        try {
            $this->envSvc->createConfigFiles($attrs);
        } catch (FileException $e) {
            Log::error('Config files failed to write');
            Log::error($e->getMessage());

            flash()->error($e->getMessage());
            return redirect(route('installer.step2'))->withInput();
        }

        // Needs to redirect so it can load the new .env
        Log::info('Redirecting to database setup');
        return redirect(route('installer.dbsetup'));
    }

    /**
     * Step 2b. Setup the database
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function dbsetup(Request $request)
    {
        $console_out = '';

        try {
            $console_out .= $this->dbSvc->setupDB();
            $console_out .= $this->migrationSvc->runAllMigrations();
            $this->seederSvc->syncAllSeeds();
        } catch (QueryException $e) {
            Log::error('Error on db setup: '.$e->getMessage());

            $this->envSvc->removeConfigFiles();
            flash()->error($e->getMessage());
            return redirect(route('installer.step2'))->withInput();
        }

        $console_out = trim($console_out);

        return view('installer::install/steps/step2a-db_output', [
            'console_output' => $console_out,
        ]);
    }

    /**
     * Step 3. Setup the admin user and initial settings
     */
    public function step3(Request $request)
    {
        return view('installer::install/steps/step3-user', [
            'countries' => Countries::getSelectList(),
        ]);
    }

    /**
     * Step 3 submit
     *
     * @param Request $request
     *
     * @throws \RuntimeException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     * @throws \Exception
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function usersetup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'airline_name'    => 'required',
            'airline_icao'    => 'required|size:3|unique:airlines,icao',
            'airline_country' => 'required',
            'name'            => 'required',
            'email'           => 'required|email|unique:users,email',
            'password'        => 'required|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect('install/step3')
                ->withErrors($validator)
                ->withInput();
        }

        /**
         * Create the first airline
         */
        $attrs = [
            'icao'    => $request->get('airline_icao'),
            'name'    => $request->get('airline_name'),
            'country' => $request->get('airline_country'),
        ];

        $airline = $this->airlineRepo->create($attrs);

        /**
         * Create the user, and associate to the airline
         * Ensure the seed data at least has one airport
         * KAUS, for giggles, though.
         */
        $attrs = [
            'name'       => $request->get('name'),
            'email'      => $request->get('email'),
            'api_key'    => Utils::generateApiKey(),
            'airline_id' => $airline->id,
            'password'   => Hash::make($request->get('password')),
        ];

        $user = User::create($attrs);
        $user = $this->userService->createUser($user, ['admin']);
        Log::info('User registered: ', $user->toArray());

        // Set the initial admin e-mail address
        setting_save('general.admin_email', $user->email);

        // Save telemetry setting
        setting_save('general.telemetry', get_truth_state($request->get('telemetry')));

        // Try sending telemetry info
        $this->analyticsSvc->sendInstall();

        return view('installer::install/steps/step3a-completed', []);
    }

    /**
     * Final step
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function complete(Request $request)
    {
        $installerSvc = app(InstallerService::class);
        $installerSvc->disableInstallerModules();

        return redirect('/login');
    }
}
