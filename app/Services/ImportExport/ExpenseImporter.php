<?php

namespace App\Services\ImportExport;

use App\Contracts\ImportExport;
use App\Models\Aircraft;
use App\Models\Airport;
use App\Models\Expense;
use App\Models\Subfleet;
use Illuminate\Support\Facades\Log;

/**
 * Import expenses
 */
class ExpenseImporter extends ImportExport
{
    public $assetType = 'expense';

    /**
     * All of the columns that are in the CSV import
     * Should match the database fields, for the most part
     */
    public static $columns = [
        'airline'        => 'nullable',
        'name'           => 'required',
        'amount'         => 'required|numeric',
        'type'           => 'required',
        'flight_type'    => 'nullable',
        'charge_to_user' => 'nullable|boolean',
        'multiplier'     => 'nullable|numeric',
        'active'         => 'nullable|boolean',
        'ref_model'      => 'nullable',
        'ref_model_id'   => 'nullable',
    ];

    /**
     * Import a flight, parse out the different rows
     *
     * @param array $row
     * @param int   $index
     *
     * @return bool
     */
    public function import(array $row, $index): bool
    {
        if ($row['airline']) {
            $row['airline_id'] = $this->getAirline($row['airline'])->id;
        }

        // Figure out what this is referring to
        $row = $this->getRefClassInfo($row);

        if (!$row['active']) {
            $row['active'] = true;
        }

        try {
            $expense = Expense::updateOrCreate([
                'name' => $row['name'],
            ], $row);
        } catch (\Exception $e) {
            $this->errorLog('Error in row '.$index.': '.$e->getMessage());
            return false;
        }

        $this->log('Imported '.$row['name']);
        return true;
    }

    /**
     * See if this expense refers to a ref_model
     *
     * @param array $row
     *
     * @return array
     */
    protected function getRefClassInfo(array $row)
    {
        $row['ref_model'] = trim($row['ref_model']);

        // class from import is being saved as the name of the model only
        // prepend the full class path so we can search it out
        if (\strlen($row['ref_model']) > 0) {
            if (substr_count($row['ref_model'], 'App\Models\\') === 0) {
                $row['ref_model'] = 'App\Models\\'.$row['ref_model'];
            }
        } else {
            $row['ref_model'] = Expense::class;
            return $row;
        }

        $class = $row['ref_model'];
        $id = $row['ref_model_id'];
        $obj = null;

        if ($class === Aircraft::class) {
            Log::info('Trying to import expense on aircraft, registration: '.$id);
            $obj = Aircraft::where('registration', $id)->first();
        } elseif ($class === Airport::class) {
            Log::info('Trying to import expense on airport, icao: '.$id);
            $obj = Airport::where('icao', $id)->first();
        } elseif ($class === Subfleet::class) {
            Log::info('Trying to import expense on subfleet, type: '.$id);
            $obj = Subfleet::where('type', $id)->first();
        } else {
            $this->errorLog('Unknown/unsupported Expense class: '.$class);
        }

        if (!$obj) {
            return $row;
        }

        $row['ref_model_id'] = $obj->id;
        return $row;
    }
}
