<?php
/**
 */

namespace Tests\Traits;

use App\Models\Flight;
use Faker\Factory;

trait FixtureDataLoader {

    public static $airports = ['KAUS', 'KJFK', 'KSFO', 'OPKC', 'OMDB', 'KLGA'];

    public function apiHeaders()
    {
        return [
            'Authorization' => 'testadminapikey'
        ];
    }

    /**
     * Create new ID of integer type
     * @return integer
     */
    protected function create_id_int()
    {
        return random_int(1, 10000);
    }

    /**
     * Create a new ID
     * @return mixed
     */
    protected function create_id_hash()
    {
        $hashids = new Hashids('', 12);
        $mt = str_replace('.', '', microtime(true));
        $id = $hashids->encode($mt);
        return $id;
    }

    /**
     * Dynamically apply options to a model
     * @param $model
     * @param array $options
     * @return mixed
     */
    protected function apply_options($model, array $options)
    {
        foreach ($options as $key => $value) {
            $model->{$key} = $value;
        }

        $model->save();
        return $model;
    }

    /**
     * Add a flight
     * @param array $options
     * @return mixed
     */
    public function addFlight(array $options=[])
    {
        $faker = Factory::create();
        $options = array_merge([
            'id' => $this->create_id_hash(),
            'flight_number' => $faker->numberBetween(),
            'airline_id' => 1,
            'dpt_airport_id' => $faker->randomElement(self::$airports),
            'arr_airport_id' => $faker->randomElement(self::$airports),
        ], $options);

        $flight = new Flight();
        $flight = $this->apply_options($flight, $options);
        $flight->subfleets()->syncWithoutDetaching([1]);

        return $flight;
    }

}
