<?php

namespace App\Services\ImportExport;

use App\Interfaces\ImportExport;
use App\Models\Aircraft;
use App\Models\Airport;
use App\Models\Enums\ExpenseType;
use App\Models\Expense;
use App\Models\Subfleet;
use Log;

/**
 * Import expenses
 * @package App\Services\Import
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
        'charge_to_user' => 'nullable|boolean',
        'multiplier'     => 'nullable|numeric',
        'active'         => 'nullable|boolean',
        'ref_class'      => 'nullable',
        'ref_class_id'   => 'nullable',
    ];

    /**
     * Import a flight, parse out the different rows
     * @param array $row
     * @param int   $index
     * @return bool
     */
    public function import(array $row, $index): bool
    {
        if($row['airline']) {
            $row['airline_id'] = $this->getAirline($row['airline'])->id;
        }

        # Figure out what this is referring to
        $row = $this->getRefClassInfo($row);

        $row['type'] = ExpenseType::getFromCode($row['type']);
        if(!$row['active']) {
            $row['active'] = true;
        }

        $expense = Expense::firstOrNew([
            'name' => $row['name'],
        ], $row);

        try {
            $expense->save();
        } catch (\Exception $e) {
            $this->errorLog('Error in row '.$index.': '.$e->getMessage());
            return false;
        }

        $this->log('Imported '.$row['name']);
        return true;
    }

    /**
     * See if this expense refers to a ref_class
     * @param array $row
     * @return array
     */
    protected function getRefClassInfo(array $row)
    {
        $row['ref_class'] = trim($row['ref_class']);

        // class from import is being saved as the name of the model only
        // prepend the full class path so we can search it out
        if (\strlen($row['ref_class']) > 0) {
            if (substr_count($row['ref_class'], 'App\Models\\') === 0) {
                $row['ref_class'] = 'App\Models\\'.$row['ref_class'];
            }
        } else {
            $row['ref_class'] = Expense::class;
            return $row;
        }

        $class = $row['ref_class'];
        $id = $row['ref_class_id'];
        $obj = null;

        if ($class === Aircraft::class) {
            Log::info('Trying to import expense on aircraft, registration: ' . $id);
            $obj = Aircraft::where('registration', $id)->first();
        } elseif ($class === Airport::class) {
            Log::info('Trying to import expense on airport, icao: ' . $id);
            $obj = Airport::where('icao', $id)->first();
        } elseif ($class === Subfleet::class) {
            Log::info('Trying to import expense on subfleet, type: ' . $id);
            $obj = Subfleet::where('type', $id)->first();
        } else {
            $this->errorLog('Unknown/unsupported Expense class: '.$class);
        }

        if(!$obj) {
            return $row;
        }

        $row['ref_class_id'] = $obj->id;
        return $row;
    }
}
