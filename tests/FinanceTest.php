<?php

namespace Tests;

use App\Models\Airline;
use App\Models\Airport;
use App\Models\Enums\ExpenseType;
use App\Models\Enums\FlightType;
use App\Models\Enums\PirepSource;
use App\Models\Expense;
use App\Models\Fare;
use App\Models\Flight;
use App\Models\Journal;
use App\Models\Pirep;
use App\Models\Subfleet;
use App\Models\User;
use App\Repositories\ExpenseRepository;
use App\Repositories\JournalRepository;
use App\Services\FareService;
use App\Services\Finance\PirepFinanceService;
use App\Services\FleetService;
use App\Services\PirepService;
use App\Support\Math;
use App\Support\Money;
use Exception;

class FinanceTest extends TestCase
{
    private $expenseRepo;
    private $fareSvc;
    private $financeSvc;
    private $fleetSvc;
    private $pirepSvc;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->addData('base');
        $this->addData('fleet');

        $this->expenseRepo = app(ExpenseRepository::class);
        $this->fareSvc = app(FareService::class);
        $this->financeSvc = app(PirepFinanceService::class);
        $this->fleetSvc = app(FleetService::class);
        $this->pirepSvc = app(PirepService::class);
    }

    /**
     * Create a user and a PIREP, that has all of the data filled out
     * so that we can test all of the disparate parts of the finances
     *
     * @throws Exception
     *
     * @return array
     */
    public function createFullPirep()
    {
        /**
         * Setup tests
         */
        $subfleet = $this->createSubfleetWithAircraft(2);
        $subfleet['subfleet']->cost_block_hour = 10;
        $subfleet['subfleet']->save();

        $rank = $this->createRank(10, [$subfleet['subfleet']->id]);
        $rank->acars_base_pay_rate = 10;
        $rank->save();

        $this->fleetSvc->addSubfleetToRank($subfleet['subfleet'], $rank);

        $dpt_apt = factory(Airport::class)->create([
            'ground_handling_cost' => 10,
            'fuel_jeta_cost'       => 10,
        ]);

        $arr_apt = factory(Airport::class)->create([
            'ground_handling_cost' => 10,
            'fuel_jeta_cost'       => 10,
        ]);

        /** @var \App\Models\User $user */
        $user = factory(User::class)->create([
            'rank_id' => $rank->id,
        ]);

        /** @var \App\Models\Flight $flight */
        $flight = factory(Flight::class)->create([
            'airline_id'     => $user->airline_id,
            'dpt_airport_id' => $dpt_apt->icao,
            'arr_airport_id' => $arr_apt->icao,
        ]);

        $pirep = factory(Pirep::class)->create([
            'flight_number'  => $flight->flight_number,
            'flight_type'    => FlightType::SCHED_PAX,
            'route_code'     => $flight->route_code,
            'route_leg'      => $flight->route_leg,
            'dpt_airport_id' => $dpt_apt->id,
            'arr_airport_id' => $arr_apt->id,
            'user_id'        => $user->id,
            'airline_id'     => $user->airline_id,
            'aircraft_id'    => $subfleet['aircraft']->random(),
            'flight_id'      => $flight->id,
            'source'         => PirepSource::ACARS,
            'flight_time'    => 120,
            'block_fuel'     => 10,
            'fuel_used'      => 9,
        ]);

        /**
         * Add fares to the subfleet, and then add the fares
         * to the PIREP when it's saved, and set the capacity
         */
        $fares = factory(Fare::class, 3)->create([
            'price'    => 100,
            'cost'     => 50,
            'capacity' => 10,
        ]);

        foreach ($fares as $fare) {
            $this->fareSvc->setForSubfleet($subfleet['subfleet'], $fare);
        }

        // Add an expense
        factory(Expense::class)->create([
            'airline_id' => null,
            'amount'     => 100,
        ]);

        // Add a subfleet expense
        factory(Expense::class)->create([
            'ref_model'    => Subfleet::class,
            'ref_model_id' => $subfleet['subfleet']->id,
            'amount'       => 200,
        ]);

        $pirep = $this->pirepSvc->create($pirep, []);

        return [$user, $pirep, $fares];
    }

    public function testFlightFaresNoOverride()
    {
        $flight = factory(Flight::class)->create();
        $fare = factory(Fare::class)->create();

        $this->fareSvc->setForFlight($flight, $fare);
        $subfleet_fares = $this->fareSvc->getForFlight($flight);

        $this->assertCount(1, $subfleet_fares);
        $this->assertEquals($fare->price, $subfleet_fares->get(0)->price);
        $this->assertEquals($fare->capacity, $subfleet_fares->get(0)->capacity);

        //
        // set an override now
        //
        $this->fareSvc->setForFlight($flight, $fare, [
            'price' => 50, 'capacity' => 400,
        ]);

        // look for them again
        $subfleet_fares = $this->fareSvc->getForFlight($flight);

        $this->assertCount(1, $subfleet_fares);
        $this->assertEquals(50, $subfleet_fares[0]->price);
        $this->assertEquals(400, $subfleet_fares[0]->capacity);

        // delete
        $this->fareSvc->delFareFromFlight($flight, $fare);
        $this->assertCount(0, $this->fareSvc->getForFlight($flight));
    }

    /**
     * Assign percentage values and make sure they're valid
     */
    public function testFlightFareOverrideAsPercent()
    {
        $flight = factory(Flight::class)->create();
        $fare = factory(Fare::class)->create();

        $percent_incr = '20%';
        $percent_decr = '-20%';
        $percent_200 = '200%';

        $new_price = Math::addPercent($fare->price, $percent_incr);
        $new_cost = Math::addPercent($fare->cost, $percent_decr);
        $new_capacity = Math::addPercent($fare->capacity, $percent_200);

        $this->fareSvc->setForFlight($flight, $fare, [
            'price'    => $percent_incr,
            'cost'     => $percent_decr,
            'capacity' => $percent_200,
        ]);

        $ac_fares = $this->fareSvc->getAllFares($flight, null);

        $this->assertCount(1, $ac_fares);
        $this->assertEquals($new_price, $ac_fares[0]->price);
        $this->assertEquals($new_cost, $ac_fares[0]->cost);
        $this->assertEquals($new_capacity, $ac_fares[0]->capacity);
    }

    public function testSubfleetFaresNoOverride()
    {
        $subfleet = factory(Subfleet::class)->create();
        $fare = factory(Fare::class)->create();

        $this->fareSvc->setForSubfleet($subfleet, $fare);
        $subfleet_fares = $this->fareSvc->getForSubfleet($subfleet);

        $this->assertCount(1, $subfleet_fares);
        $this->assertEquals($fare->price, $subfleet_fares->get(0)->price);
        $this->assertEquals($fare->capacity, $subfleet_fares->get(0)->capacity);

        //
        // set an override now
        //
        $this->fareSvc->setForSubfleet($subfleet, $fare, [
            'price' => 50, 'capacity' => 400,
        ]);

        // look for them again
        $subfleet_fares = $this->fareSvc->getForSubfleet($subfleet);

        $this->assertCount(1, $subfleet_fares);
        $this->assertEquals(50, $subfleet_fares[0]->price);
        $this->assertEquals(400, $subfleet_fares[0]->capacity);

        // delete
        $this->fareSvc->delFareFromSubfleet($subfleet, $fare);
        $this->assertCount(0, $this->fareSvc->getForSubfleet($subfleet));
    }

    public function testSubfleetFaresOverride()
    {
        $subfleet = factory(Subfleet::class)->create();
        $fare = factory(Fare::class)->create();

        $this->fareSvc->setForSubfleet($subfleet, $fare, [
            'price' => 50, 'capacity' => 400,
        ]);

        $ac_fares = $this->fareSvc->getForSubfleet($subfleet);

        $this->assertCount(1, $ac_fares);
        $this->assertEquals(50, $ac_fares[0]->price);
        $this->assertEquals(400, $ac_fares[0]->capacity);

        //
        // update the override to a different amount and make sure it updates
        //

        $this->fareSvc->setForSubfleet($subfleet, $fare, [
            'price' => 150, 'capacity' => 50,
        ]);

        $ac_fares = $this->fareSvc->getForSubfleet($subfleet);

        $this->assertCount(1, $ac_fares);
        $this->assertEquals(150, $ac_fares[0]->price);
        $this->assertEquals(50, $ac_fares[0]->capacity);

        // delete
        $this->fareSvc->delFareFromSubfleet($subfleet, $fare);
        $this->assertCount(0, $this->fareSvc->getForSubfleet($subfleet));
    }

    /**
     * Assign percentage values and make sure they're valid
     */
    public function testSubfleetFareOverrideAsPercent()
    {
        $subfleet = factory(Subfleet::class)->create();
        $fare = factory(Fare::class)->create();

        $percent_incr = '20%';
        $percent_decr = '-20%';
        $percent_200 = '200%';

        $new_price = Math::addPercent($fare->price, $percent_incr);
        $new_cost = Math::addPercent($fare->cost, $percent_decr);
        $new_capacity = Math::addPercent($fare->capacity, $percent_200);

        $this->fareSvc->setForSubfleet($subfleet, $fare, [
            'price'    => $percent_incr,
            'cost'     => $percent_decr,
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
        $flight = factory(Flight::class)->create();
        $subfleet = factory(Subfleet::class)->create();
        [$fare1, $fare2, $fare3, $fare4] = factory(Fare::class, 4)->create();

        // add to the subfleet, and just override one of them
        $this->fareSvc->setForSubfleet($subfleet, $fare1);
        $this->fareSvc->setForSubfleet($subfleet, $fare2, [
            'price'    => 100,
            'cost'     => 50,
            'capacity' => 25,
        ]);

        $this->fareSvc->setForSubfleet($subfleet, $fare3);

        // Now set the last one to the flight and then override stuff
        $this->fareSvc->setForFlight($flight, $fare3, [
            'price' => '300%',
            'cost'  => 250,
        ]);

        $fare3_price = Math::addPercent($fare3->price, 300);

        // Assign another one to the flight, that's not on the subfleet
        // This one should NOT be returned in the list of fares
        $this->fareSvc->setForFlight($flight, $fare4);

        $fares = $this->fareSvc->getAllFares($flight, $subfleet);
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
                    $this->assertEquals($fare->price, $fare3_price);
                    $this->assertEquals($fare->cost, 250);
                    $this->assertEquals($fare->capacity, $fare3->capacity);
                    break;
            }
        }
    }

    public function testGetFaresNoFlightOverrides()
    {
        $subfleet = factory(Subfleet::class)->create();
        [$fare1, $fare2, $fare3] = factory(Fare::class, 3)->create();

        // add to the subfleet, and just override one of them
        $this->fareSvc->setForSubfleet($subfleet, $fare1);
        $this->fareSvc->setForSubfleet($subfleet, $fare2, [
            'price'    => 100,
            'cost'     => 50,
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

        $this->user = factory(User::class)->create([
            'rank_id' => $rank->id,
        ]);

        $pirep = factory(Pirep::class)->create([
            'user_id'     => $this->user->id,
            'aircraft_id' => $subfleet['aircraft']->random(),
            'source'      => PirepSource::ACARS,
        ]);

        $rate = $this->financeSvc->getPilotPayRateForPirep($pirep);
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

        $this->user = factory(User::class)->create([
            'rank_id' => $rank->id,
        ]);

        $pirep_acars = factory(Pirep::class)->create([
            'user_id'     => $this->user->id,
            'aircraft_id' => $subfleet['aircraft']->random(),
            'source'      => PirepSource::ACARS,
        ]);

        $rate = $this->financeSvc->getPilotPayRateForPirep($pirep_acars);
        $this->assertEquals($acars_pay_rate, $rate);

        // Change to a percentage
        $manual_pay_rate = '50%';
        $manual_pay_adjusted = Math::addPercent(
            $rank->manual_base_pay_rate,
            $manual_pay_rate
        );

        $this->fleetSvc->addSubfleetToRank($subfleet['subfleet'], $rank, [
            'manual_pay' => $manual_pay_rate,
        ]);

        $pirep_manual = factory(Pirep::class)->create([
            'user_id'     => $this->user->id,
            'aircraft_id' => $subfleet['aircraft']->random(),
            'source'      => PirepSource::MANUAL,
        ]);

        $rate = $this->financeSvc->getPilotPayRateForPirep($pirep_manual);
        $this->assertEquals($manual_pay_adjusted, $rate);

        // And make sure the original acars override still works
        $rate = $this->financeSvc->getPilotPayRateForPirep($pirep_acars);
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

        $this->user = factory(User::class)->create([
            'rank_id' => $rank->id,
        ]);

        $pirep_acars = factory(Pirep::class)->create([
            'user_id'     => $this->user->id,
            'aircraft_id' => $subfleet['aircraft']->random(),
            'source'      => PirepSource::ACARS,
            'flight_time' => 60,
        ]);

        $payment = $this->financeSvc->getPilotPay($pirep_acars);
        $this->assertEquals(100, $payment->getValue());

        $pirep_acars = factory(Pirep::class)->create([
            'user_id'     => $this->user->id,
            'aircraft_id' => $subfleet['aircraft']->random(),
            'source'      => PirepSource::ACARS,
            'flight_time' => 90,
        ]);

        $payment = $this->financeSvc->getPilotPay($pirep_acars);
        $this->assertEquals($payment->getValue(), 150);
    }

    public function testGetPirepPilotPayWithFixedPrice()
    {
        $acars_pay_rate = 100;

        $subfleet = $this->createSubfleetWithAircraft(2);
        $rank = $this->createRank(10, [$subfleet['subfleet']->id]);
        $this->fleetSvc->addSubfleetToRank($subfleet['subfleet'], $rank, [
            'acars_pay' => $acars_pay_rate,
        ]);

        $this->user = factory(User::class)->create([
            'rank_id' => $rank->id,
        ]);

        $flight = factory(Flight::class)->create([
            'airline_id' => $this->user->airline_id,
            'pilot_pay'  => 1000,
        ]);

        $pirep_acars = factory(Pirep::class)->create([
            'user_id'     => $this->user->id,
            'aircraft_id' => $subfleet['aircraft']->random(),
            'source'      => PirepSource::ACARS,
            'flight_id'   => $flight->id,
            'flight_time' => 60,
        ]);

        $payment = $this->financeSvc->getPilotPay($pirep_acars);
        $this->assertEquals(1000, $payment->getValue());

        $pirep_acars = factory(Pirep::class)->create([
            'user_id'     => $this->user->id,
            'aircraft_id' => $subfleet['aircraft']->random(),
            'source'      => PirepSource::ACARS,
            'flight_time' => 90,
        ]);

        $payment = $this->financeSvc->getPilotPay($pirep_acars);
        $this->assertEquals($payment->getValue(), 150);
    }

    /**
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function testJournalOperations(): void
    {
        $journalRepo = app(JournalRepository::class);

        $user = factory(User::class)->create();
        $journal = factory(Journal::class)->create();

        $journalRepo->post(
            $journal,
            Money::createFromAmount(100),
            null,
            $user
        );

        $balance = $journalRepo->getBalance($journal);
        $this->assertEquals(100, $balance->getValue());
        $this->assertEquals(100, $journal->balance->getValue());

        // add another transaction

        $journalRepo->post(
            $journal,
            Money::createFromAmount(25),
            null,
            $user
        );

        $balance = $journalRepo->getBalance($journal);
        $this->assertEquals(125, $balance->getValue());
        $this->assertEquals(125, $journal->balance->getValue());

        // debit an amount
        $journalRepo->post(
            $journal,
            null,
            Money::createFromAmount(25),
            $user
        );

        $balance = $journalRepo->getBalance($journal);
        $this->assertEquals(100, $balance->getValue());
        $this->assertEquals(100, $journal->balance->getValue());

        // find all transactions
        $transactions = $journalRepo->getAllForObject($user);

        $this->assertCount(3, $transactions['transactions']);
        $this->assertEquals(125, $transactions['credits']->getValue());
        $this->assertEquals(25, $transactions['debits']->getValue());
    }

    /**
     * @throws Exception
     */
    public function testPirepFares()
    {
        [$user, $pirep, $fares] = $this->createFullPirep();

        // Override the fares
        $fare_counts = [];
        foreach ($fares as $fare) {
            $fare_counts[] = [
                'fare_id' => $fare->id,
                'price'   => $fare->price,
                'count'   => round($fare->capacity / 2),
            ];
        }

        $this->fareSvc->saveForPirep($pirep, $fare_counts);
        $all_fares = $this->financeSvc->getReconciledFaresForPirep($pirep);

        $fare_counts = collect($fare_counts);
        foreach ($all_fares as $fare) {
            $set_fare = $fare_counts->where('fare_id', $fare->id)->first();
            $this->assertEquals($set_fare['count'], $fare->count);
            $this->assertEquals($set_fare['price'], $fare->price);
        }
    }

    /**
     * Test that all expenses are pulled properly
     */
    public function testPirepExpenses()
    {
        $airline = factory(Airline::class)->create();
        $airline2 = factory(Airline::class)->create();

        factory(Expense::class)->create([
            'airline_id' => $airline->id,
        ]);

        factory(Expense::class)->create([
            'airline_id' => $airline2->id,
        ]);

        factory(Expense::class)->create([
            'airline_id' => null,
        ]);

        $expenses = $this->expenseRepo->getAllForType(
            ExpenseType::FLIGHT,
            $airline->id,
            Expense::class
        );

        $this->assertCount(2, $expenses);

        $found = $expenses->where('airline_id', null);
        $this->assertCount(1, $found);

        $found = $expenses->where('airline_id', $airline->id);
        $this->assertCount(1, $found);

        $found = $expenses->where('airline_id', $airline2->id);
        $this->assertCount(0, $found);

        /*
         * Test the subfleet class
         */

        $subfleet = factory(Subfleet::class)->create();
        factory(Expense::class)->create([
            'airline_id'   => null,
            'ref_model'    => Subfleet::class,
            'ref_model_id' => $subfleet->id,
        ]);

        $expenses = $this->expenseRepo->getAllForType(
            ExpenseType::FLIGHT,
            $airline->id,
            $subfleet
        );

        $this->assertCount(1, $expenses);

        $expense = $expenses->random();
        $this->assertEquals(Subfleet::class, $expense->ref_model);
        $obj = $expense->getReferencedObject();
        $this->assertEquals($obj->id, $expense->ref_model_id);
    }

    /**
     * @throws Exception
     */
    public function testPirepFinances()
    {
        $journalRepo = app(JournalRepository::class);

        [$user, $pirep, $fares] = $this->createFullPirep();
        $user->airline->initJournal(setting('units.currency', 'USD'));

        // Override the fares
        $fare_counts = [];
        foreach ($fares as $fare) {
            $fare_counts[] = [
                'fare_id' => $fare->id,
                'price'   => $fare->price,
                'count'   => 100,
            ];
        }

        $this->fareSvc->saveForPirep($pirep, $fare_counts);

        // This should process all of the
        $pirep = $this->pirepSvc->accept($pirep);

        $transactions = $journalRepo->getAllForObject($pirep);

//        $this->assertCount(9, $transactions['transactions']);
        $this->assertEquals(3020, $transactions['credits']->getValue());
        $this->assertEquals(1960, $transactions['debits']->getValue());

        // Check that all the different transaction types are there
        // test by the different groups that exist
        $transaction_tags = [
            'fuel'            => 1,
            'expense'         => 1,
            'subfleet'        => 2,
            'fare'            => 3,
            'ground_handling' => 1,
            'pilot_pay'       => 2, // debit on the airline, credit to the pilot
        ];

        foreach ($transaction_tags as $type => $count) {
            $find = $transactions['transactions']->where('tags', $type);
            $this->assertEquals($count, $find->count());
        }
    }

    /**
     * @throws Exception
     */
    public function testPirepFinancesSpecificExpense()
    {
        $journalRepo = app(JournalRepository::class);

        // Add an expense that's only for a cargo flight
        factory(Expense::class)->create([
            'airline_id'  => null,
            'amount'      => 100,
            'flight_type' => FlightType::SCHED_CARGO,
        ]);

        [$user, $pirep, $fares] = $this->createFullPirep();
        $user->airline->initJournal(setting('units.currency', 'USD'));

        // Override the fares
        $fare_counts = [];
        foreach ($fares as $fare) {
            $fare_counts[] = [
                'fare_id' => $fare->id,
                'price'   => $fare->price,
                'count'   => 100,
            ];
        }

        $this->fareSvc->saveForPirep($pirep, $fare_counts);

        // This should process all of the
        $pirep = $this->pirepSvc->accept($pirep);

        $transactions = $journalRepo->getAllForObject($pirep);

//        $this->assertCount(9, $transactions['transactions']);
        $this->assertEquals(3020, $transactions['credits']->getValue());
        $this->assertEquals(1960, $transactions['debits']->getValue());

        // Check that all the different transaction types are there
        // test by the different groups that exist
        $transaction_tags = [
            'fuel'            => 1,
            'expense'         => 1,
            'subfleet'        => 2,
            'fare'            => 3,
            'ground_handling' => 1,
            'pilot_pay'       => 2, // debit on the airline, credit to the pilot
        ];

        foreach ($transaction_tags as $type => $count) {
            $find = $transactions['transactions']->where('tags', $type);
            $this->assertEquals($count, $find->count());
        }

        // Add a new PIREP;
        $pirep2 = factory(Pirep::class)->create([
            'flight_number'  => 100,
            'flight_type'    => FlightType::SCHED_CARGO,
            'dpt_airport_id' => $pirep->dpt_airport_id,
            'arr_airport_id' => $pirep->arr_airport_id,
            'user_id'        => $user->id,
            'airline_id'     => $user->airline_id,
            'aircraft_id'    => $pirep->aircraft_id,
            'source'         => PirepSource::ACARS,
            'flight_time'    => 120,
            'block_fuel'     => 10,
            'fuel_used'      => 9,
        ]);

        $this->fareSvc->saveForPirep($pirep2, $fare_counts);
        $pirep2 = $this->pirepSvc->accept($pirep2);

        $transactions = $journalRepo->getAllForObject($pirep2);
        $this->assertEquals(3020, $transactions['credits']->getValue());
        $this->assertEquals(2060, $transactions['debits']->getValue());

        // Check that all the different transaction types are there
        // test by the different groups that exist
        $transaction_tags = [
            'fuel'            => 1,
            'expense'         => 2,
            'subfleet'        => 2,
            'fare'            => 3,
            'ground_handling' => 1,
            'pilot_pay'       => 2, // debit on the airline, credit to the pilot
        ];

        foreach ($transaction_tags as $type => $count) {
            $find = $transactions['transactions']->where('tags', $type);
            $this->assertEquals($count, $find->count());
        }
    }
}
