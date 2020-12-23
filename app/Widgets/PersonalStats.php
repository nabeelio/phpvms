<?php

namespace App\Widgets;

use Carbon;
use App\Models\Pirep;
use App\Contracts\Widget;
use Illuminate\Support\Facades\DB;

/** Show some nice stats of current user **/
class PersonalStats extends Widget
{

	protected $config = ['disp' => null, 'user' => null, 'period' => null, 'type' => 'avglanding',];

	public function user() {
    	return $this->belongsTo(User::class);
	}
	
    public function run() {

		$user = $this->config['user'] ;
		$selection = $this->config['type'] ;
		$period = $this->config['period'] ;

		if ($user) {
			$userid = $this->config['user'] ;
		} else {
			$userid = user()->id ;
		}

		if ($selection == 'avglanding') {
			if ($period) {
				$PersonalStats = Pirep::where('user_id', ".$userid.")->where('state', '2')->where('source', '1')
								->where('submitted_at','>=',Carbon::now()->subdays($this->config['period']))->avg('landing_rate') ;
			} else {
				$PersonalStats = Pirep::where('user_id', ".$userid.")->where('state', '2')->where('source', '1')->avg('landing_rate') ;
			}			
			$PersonalStats = round($PersonalStats) ;
			$PersonalStats = $PersonalStats . ' ft/min' ;

		} elseif ($selection == 'avgscore') {
			if ($period) {
				$PersonalStats = Pirep::where('user_id', ".$userid.")->where('state', '2')->where('source', '1')
								->where('submitted_at','>=',Carbon::now()->subdays($this->config['period']))->avg('score') ;
			} else {
				$PersonalStats = Pirep::where('user_id', ".$userid.")->where('state', '2')->where('source', '1')->avg('score') ;
			}
			$PersonalStats = round($PersonalStats) ;

		} elseif ($selection == 'avgdistance') {
			if ($period) {
				$PersonalStats = Pirep::where('user_id', ".$userid.")->where('state', '2')->where('source', '1')
								->where('submitted_at','>=',Carbon::now()->subdays($this->config['period']))->avg('distance') ;
			} else {
				$PersonalStats = Pirep::where('user_id', ".$userid.")->where('state', '2')->where('source', '1')->avg('distance') ;
			}
			$PersonalStats = number_format(round($PersonalStats)) ;
			$PersonalStats = $PersonalStats . ' Nm' ;

		} elseif ($selection == 'totdistance') {
			if ($period) {
				$PersonalStats = Pirep::where('user_id', ".$userid.")->where('state', '2')->where('source', '1')
								->where('submitted_at','>=',Carbon::now()->subdays($this->config['period']))->sum('distance') ;
			} else {
				$PersonalStats = Pirep::where('user_id', ".$userid.")->where('state', '2')->where('source', '1')->sum('distance') ;
			}
			$PersonalStats = number_format(round($PersonalStats)) ;
			$PersonalStats = $PersonalStats . ' Nm' ;

		} elseif ($selection == 'avgtime') {
			if ($period) {
				$PersonalStats = Pirep::where('user_id', ".$userid.")->where('state', '2')->where('source', '1')
								->where('submitted_at','>=',Carbon::now()->subdays($this->config['period']))->avg('flight_time') ;
			} else {
				$PersonalStats = Pirep::where('user_id', ".$userid.")->where('state', '2')->where('source', '1')->avg('flight_time') ;
			}

		} elseif ($selection == 'tottime') {
			if ($period) {
				$PersonalStats = Pirep::where('user_id', ".$userid.")->where('state', '2')->where('source', '1')
								->where('submitted_at','>=',Carbon::now()->subdays($this->config['period']))->sum('flight_time') ;
			} else {
				$PersonalStats = Pirep::where('user_id', ".$userid.")->where('state', '2')->where('source', '1')->sum('flight_time') ;
			}

		} elseif ($selection == 'avgfuel') {
			if ($period) {
				$PersonalStats = Pirep::where('user_id', ".$userid.")->where('state', '2')->where('source', '1')
								->where('submitted_at','>=',Carbon::now()->subdays($this->config['period']))->avg('fuel_used') ;
			} else {
				$PersonalStats = Pirep::where('user_id', ".$userid.")->where('state', '2')->where('source', '1')->avg('fuel_used') ;
			}
			if(setting('units.weight') === 'kg') {
				$PersonalStats = number_format(round($PersonalStats / 2.205)) ;
				$PersonalStats = $PersonalStats . ' Kgs' ;
			} else {
				$PersonalStats = number_format(round($PersonalStats)) ;
				$PersonalStats = $PersonalStats . ' Lbs' ;
			}
			
		} elseif ($selection == 'totfuel') {
			if ($period) {
				$PersonalStats = Pirep::where('user_id', ".$userid.")->where('state', '2')->where('source', '1')
								->where('submitted_at','>=',Carbon::now()->subdays($this->config['period']))->sum('fuel_used') ;
			} else {
				$PersonalStats = Pirep::where('user_id', ".$userid.")->where('state', '2')->where('source', '1')->sum('fuel_used') ;
			}
			if(setting('units.weight') === 'kg') {
				$PersonalStats = number_format(round($PersonalStats / 2.205)) ;
				$PersonalStats = $PersonalStats . ' Kgs' ;
			} else {
				$PersonalStats = number_format(round($PersonalStats)) ;
				$PersonalStats = $PersonalStats . ' Lbs' ;
			}

		} elseif ($selection == 'totflight') {
			if ($period) {
				$PersonalStats = Pirep::where('user_id', ".$userid.")->where('state', '2')->where('source', '1')
								->where('submitted_at','>=',Carbon::now()->subdays($this->config['period']))->count('score') ;
			} else {
				$PersonalStats = Pirep::where('user_id', ".$userid.")->where('state', '2')->where('source', '1')->count('score') ;
			}
			$PersonalStats = number_format($PersonalStats) ;

		}

		return view('widgets.personalstats', ['pstat'  => $PersonalStats, 'type' => $selection, 'disp' => $this->config['disp'], 'period' => $this->config['period'],]);
    }
}
