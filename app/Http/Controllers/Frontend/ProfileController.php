<?php

namespace App\Http\Controllers\Frontend;

use App\Models\User;
use App\Repositories\AirportRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Auth;


class ProfileController extends AppBaseController
{
    private $airportRepository;

    public function __construct(AirportRepository $airportRepo)
    {
        $this->airportRepository = $airportRepo;
    }

    public function index()
    {
        $airports = $this->airportRepository->all();

        return $this->view('profile.index', [
            'user' => Auth::user(),
            'airports' => $airports,
        ]);
    }

    public function show($id)
    {
        $user = User::where('id', $id)->first();
        if (empty($user)) {
            Flash::error('User not found!');
            return redirect(route('frontend.dashboard.index'));
        }

        $airports = $this->airportRepository->all();

        return $this->view('profile.index', [
            'user' => $user,
            'airports' => $airports,
        ]);
    }

    public function update()
    {

    }
}
