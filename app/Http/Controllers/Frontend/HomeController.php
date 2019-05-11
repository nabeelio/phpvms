<?php

namespace App\Http\Controllers\Frontend;

use App\Interfaces\Controller;
use App\Models\User;
use Illuminate\Database\QueryException;
use Log;

/**
 * Class HomeController
 */
class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     */
    public function index()
    {
        try {
            $users = User::orderBy('created_at', 'desc')->take(4)->get();
            debug($users);
        } catch (\PDOException $e) {
            Log::emergency($e);
            return view('system/errors/database_error', [
                'error' => $e->getMessage(),
            ]);
        } catch (QueryException $e) {
            return view('system/errors/not_installed');
        }

        // No users
        if (!$users) {
            return view('system/errors/not_installed');
        }

        return view('home', [
            'users' => $users,
        ]);
    }
}
