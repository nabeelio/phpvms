<?php

use App\Services\FareService;
use App\Services\FinanceService;
use App\Services\FleetService;
use App\Support\Math;

class FinanceTest extends TestCase
{
    private $fareSvc,
            $financeSvc,
            $fleetSvc;

    public function setUp()
    {
        parent::setUp();
        $this->addData('base');

        $this->fareSvc = app(FareService::class);
        $this->financeSvc = app(FinanceService::class);
        $this->fleetSvc = app(FleetService::class);
    }

    public function testFlightFaresNoOverride()
    {
        $flight = factory(App\Models\Flight::class)->create();
        $fare = factory(App\Models\Fare::class)->create();

        $this->fareSvc->setForFlight($flight, $fare);
        $subfleet_fares = $this->fareSvc->getForFlight($flight);

        $this->assertCount(1, $subfleet_fares);
        $this->assertEquals($fare->price, $subfleet_fares->get(0)->price);
        $this->assertEquals($fare->capacity, $subfleet_fares->get(0)->capacity);

        #
        # set an override now
        #
        $this->fareSvc->setForFlight($flight, $fare, [
            'price' => 50, 'capacity' => 400
        ]);

        # look for them again
        $subfleet_fares = $this->fareSvc->getForFlight($flight);

        $this->assertCount(1, $subfleet_fares);
        $this->assertEquals(50, $subfleet_fares[0]->price);
        $this->assertEquals(400, $subfleet_fares[0]->capacity);

        # delete
        $this->fareSvc->delFareFromFlight($flight, $fare);
        $this->assertCount(0, $this->fareSvc->getForFlight($flight));
    }

    /**
     * Assign percentage values and make sure they're valid
     */
    public function testFlightFareOverrideAsPercent()
    {
        $flight = factory(App\Models\Flight::class)->create();
        $fare = factory(App\Models\Fare::class)->create();

        $percent_incr = '20%';
        $percent_decr = '-20%';
        $percent_200 = '200%';

        $new_price = Math::addPercent($fare->price, $percent_incr);
        $new_cost = Math::addPercent($fare->cost, $percent_decr);
        $new_capacity = Math::addPercent($fare->capacity, $percent_200);

        $this->fareSvc->setForFlight($flight, $fare, [
            'price' => $percent_incr,
            'cost' => $percent_decr,
            'capacity' => $percent_200,
        ]);

        $ac_fares = $this->fareSvc->getForFlight($flight);

        $this->assertCount(1, $ac_fares);
        $this->assertEquals($new_price, $ac_fares[0]->price);
        $this->assertEquals($new_cost, $ac_fares[0]->cost);
        $this->assertEquals($new_capacity, $ac_fares[0]->capacity);
    }

    public function testSubfleetFaresNoOverride()
    {
        $subfleet = factory(App\Models\Subfleet::class)->create();
        $fare = factory(App\Models\Fare::class)->create();

        $this->fareSvc->setForSubfleet($subfleet, $fare);
        $subfleet_fares = $this->fareSvc->getForSubfleet($subfleet);

        $this->assertCount(1, $subfleet_fares);
        $this->assertEquals($fare->price, $subfleet_fares->get(0)->price);
        $this->assertEquals($fare->capacity, $subfleet_fares->get(0)->capacity);

        #
        # set an override now
        #
        $this->fareSvc->setForSubfleet($subfleet, $fare, [
            'price' => 50, 'capacity' => 400
        ]);

        # look for them again
        $subfleet_fares = $this->fareSvc->getForSubfleet($subfleet);

        $this->assertCount(1, $subfleet_fares);
        $this->assertEquals(50, $subfleet_fares[0]->price);
        $this->assertEquals(400, $subfleet_fares[0]->capacity);

        # delete
        $this->fareSvc->delFareFromSubfleet($subfleet, $fare);
        $this->assertCount(0, $this->fareSvc->getForSubfleet($subfleet));
    }

    public function testSubfleetFaresOverride()
    {
        $subfleet = factory(App\Models\Subfleet::class)->create();
        $fare = factory(App\Models\Fare::class)->create();

        $this->fareSvc->setForSubfleet($subfleet, $fare, [
            'price' => 50, 'capacity' => 400
        ]);

        $ac_fares = $this->fareSvc->getForSubfleet($subfleet);

        $this->assertCount(1, $ac_fares);
        $this->assertEquals(50, $ac_fares[0]->price);
        $this->assertEquals(400, $ac_fares[0]->capacity);

        #
        # update the override to a different amount and make sure it updates
        #

        $this->fareSvc->setForSubfleet($subfleet, $fare, [
            'price' => 150, 'capacity' => 50
        ]);

        $ac_fares = $this->fareSvc->getForSubfleet($subfleet);

        $this->assertCount(1, $ac_fares);
        $this->assertEquals(150, $ac_fares[0]->price);
        $this->assertEquals(50, $ac_fares[0]->capacity);

        # delete
        $this->fareSvc->delFareFromSubfleet($subfleet, $fare);
        $this->assertCount(0, $this->fareSvc->getForSubfleet($subfleet));
    }

    /**
     * Assign percentage values and make sure they're valid
     */
    public function testSubfleetFareOverrideAsPercent()
    {
        $subfleet = factory(App\Models\Subfleet::class)->create();
        $fare = factory(App\Models\Fare::class)->create();

        $percent_incr = '20%';
        $percent_decr = '-20%';
        $percent_200 = '200%';

        $new_price = Math::addPercent($fare->price, $percent_incr);
        $new_cost = Math::addPercent($fare->cost, $percent_decr);
        $new_capacity = Math::addPercent($fare->capacity, $percent_200);

        $this->fareSvc->setForSubfleet($subfleet, $fare, [
            'price' => $percent_incr,
            'cost' => $percent_decr,
            'capacity' => $percent_200,
        ]);

        $ac_fares = $this->fareSvc->getForSubfleet($subfleet);

        $this->assertCount(1, $ac_fares);
        $this->assertEquals($new_price, $ac_fares[0]->price);
        $this->assertEquals($new_cost, $ac_fares[0]->cost);
        $this->assertEquals($new_capacity, $ac_fares[0]->capacity);
    }

    /**
     * Test getting the fares from the flight svc. Have a few base fares
     * and then override some of them
     */
    public function testGetFaresWithOverrides()
    {
        $flight = factory(App\Models\Flight::class)->create();
        $subfleet = factory(App\Models\Subfleet::class)->create();
        [$fare1, $fare2, $fare3, $fare4] = factory(App\Models\Fare::class, 4)->create();

        # add to the subfleet, and just override one of them
        $this->fareSvc->setForSubfleet($subfleet, $fare1);
        $this->fareSvc->setForSubfleet($subfleet, $fare2, [
            'price' => 100,
            'cost' => 50,
            'capacity' => 25,
        ]);

        $this->fareSvc->setForSubfleet($subfleet, $fare3);

        # Now set the last one to the flight and then override stuff
        $this->fareSvc->setForFlight($flight, $fare3, [
            'price' => '300%',
            'cost' => 250,
        ]);

        $fare3_price = Math::addPercent($fare3->price, 300);

        # Assign another one to the flight, that's not on the subfleet
        # This one should NOT be returned in the list of fares
        $this->fareSvc->setForFlight($flight, $fare4);

        $fares = $this->fareSvc->getAllFares($flight, $subfleet);
        $this->assertCount(3, $fares);

        foreach($fares as $fare) {
            switch($fare->id) {
                case $fare1->id:
                    $this->assertEquals($fare->price, $fare1->price);
                    $this->assertEquals($fare->cost, $fare1->cost);
                    $this->assertEquals($fare->capacity, $fare1->capacity);
                    break;

                case $fare2->id:
                    $this->assertEquals($fare->price, 100);
                    $this->assertEquals($fare->cost, 50);
                    $this->assertEquals($fare->capacity, 25);
                    break;

                case $fare3->id:
                    $this->assertEquals($fare->price, $fare3_price);
                    $this->assertEquals($fare->cost, 250);
                    $this->assertEquals($fare->capacity, $fare3->capacity);
                    break;
            }
        }
    }

    public function testGetFaresNoFlightOverrides()
    {
        $subfleet = factory(App\Models\Subfleet::class)->create();
        [$fare1, $fare2, $fare3] = factory(App\Models\Fare::class, 3)->create();

        # add to the subfleet, and just override one of them
        $this->fareSvc->setForSubfleet($subfleet, $fare1);
        $this->fareSvc->setForSubfleet($subfleet, $fare2, [
            'price' => 100,
            'cost' => 50,
            'capacity' => 25,
        ]);

        $this->fareSvc->setForSubfleet($subfleet, $fare3);

        $fares = $this->fareSvc->getAllFares(null, $subfleet);
        $this->assertCount(3, $fares);

        foreach ($fares as $fare) {
            switch ($fare->id) {
                case $fare1->id:
                    $this->assertEquals($fare->price, $fare1->price);
                    $this->assertEquals($fare->cost, $fare1->cost);
                    $this->assertEquals($fare->capacity, $fare1->capacity);
                    break;

                case $fare2->id:
                    $this->assertEquals($fare->price, 100);
                    $this->assertEquals($fare->cost, 50);
                    $this->assertEquals($fare->capacity, 25);
                    break;

                case $fare3->id:
                    $this->assertEquals($fare->price, $fare3->price);
                    $this->assertEquals($fare->cost, $fare3->cost);
                    $this->assertEquals($fare->capacity, $fare3->capacity);
                    break;
            }
        }
    }

    /**
     * Get the pilot pay, derived from the rank
     */
    public function testGetPilotPayNoOverride()
    {
        $subfleet = $this->createSubfleetWithAircraft(2);
        $rank = $this->createRank(10, [$subfleet['subfleet']->id]);
        $this->fleetSvc->addSubfleetToRank($subfleet['subfleet'], $rank);

        $this->user = factory(App\Models\User::class)->create([
            'rank_id' => $rank->id,
        ]);

        $pirep = factory(App\Models\Pirep::class)->create([
            'user_id' => $this->user->id,
            'aircraft_id' => $subfleet['aircraft']->random(),
            'source' => PirepSource::ACARS,
        ]);

        $rate = $this->financeSvc->getPayRateForPirep($pirep);
        $this->assertEquals($rank->acars_base_pay_rate, $rate);
    }

    /**
     * Get the pilot pay, but include different overrides
     */
    public function testGetPilotPayWithOverride()
    {
        $acars_pay_rate = 100;

        $subfleet = $this->createSubfleetWithAircraft(2);
        $rank = $this->createRank(10, [$subfleet['subfleet']->id]);
        $this->fleetSvc->addSubfleetToRank($subfleet['subfleet'], $rank, [
            'acars_pay' => $acars_pay_rate,
        ]);

        $this->user = factory(App\Models\User::class)->create([
            'rank_id' => $rank->id,
        ]);

        $pirep_acars = factory(App\Models\Pirep::class)->create([
            'user_id' => $this->user->id,
            'aircraft_id' => $subfleet['aircraft']->random(),
            'source' => PirepSource::ACARS,
        ]);

        $rate = $this->financeSvc->getPayRateForPirep($pirep_acars);
        $this->assertEquals($acars_pay_rate, $rate);

        # Change to a percentage
        $manual_pay_rate = '50%';
        $manual_pay_adjusted = Math::addPercent(
            $rank->manual_base_pay_rate, $manual_pay_rate);

        $this->fleetSvc->addSubfleetToRank($subfleet['subfleet'], $rank, [
            'manual_pay' => $manual_pay_rate,
        ]);

        $pirep_manual = factory(App\Models\Pirep::class)->create([
            'user_id' => $this->user->id,
            'aircraft_id' => $subfleet['aircraft']->random(),
            'source' => PirepSource::MANUAL,
        ]);

        $rate = $this->financeSvc->getPayRateForPirep($pirep_manual);
        $this->assertEquals($manual_pay_adjusted, $rate);

        # And make sure the original acars override still works
        $rate = $this->financeSvc->getPayRateForPirep($pirep_acars);
        $this->assertEquals($acars_pay_rate, $rate);
    }

    /**
     * Get the payment for a pilot
     */
    public function testGetPirepPilotPay()
    {
        $acars_pay_rate = 100;

        $subfleet = $this->createSubfleetWithAircraft(2);
        $rank = $this->createRank(10, [$subfleet['subfleet']->id]);
        $this->fleetSvc->addSubfleetToRank($subfleet['subfleet'], $rank, [
            'acars_pay' => $acars_pay_rate,
        ]);

        $this->user = factory(App\Models\User::class)->create([
            'rank_id' => $rank->id,
        ]);

        $pirep_acars = factory(App\Models\Pirep::class)->create([
            'user_id' => $this->user->id,
            'aircraft_id' => $subfleet['aircraft']->random(),
            'source' => PirepSource::ACARS,
            'flight_time' => 60,
        ]);

        $payment = $this->financeSvc->getPilotPilotPay($pirep_acars);
        $this->assertEquals($payment->getValue(), 100);

        $pirep_acars = factory(App\Models\Pirep::class)->create([
            'user_id' => $this->user->id,
            'aircraft_id' => $subfleet['aircraft']->random(),
            'source' => PirepSource::ACARS,
            'flight_time' => 90,
        ]);

        $payment = $this->financeSvc->getPilotPilotPay($pirep_acars);
        $this->assertEquals($payment->getValue(), 150);
    }
}
