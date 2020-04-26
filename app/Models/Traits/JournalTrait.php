<?php

namespace App\Models\Traits;

use App\Models\Journal;

trait JournalTrait
{
    /**
     * Initialize a new journal when a new record is created
     */
    public static function bootJournalTrait()
    {
        static::created(function ($model) {
            $model->initJournal(setting('units.currency'));
        });
    }

    /**
     * Morph to Journal.
     *
     * @return mixed
     */
    public function journal()
    {
        return $this->morphOne(Journal::class, 'morphed');
    }

    /**
     * Initialize a journal for a given model object
     *
     * @param string $currency_code
     *
     * @throws \Exception
     *
     * @return Journal
     */
    public function initJournal($currency_code = 'USD')
    {
        if (!$this->journal) {
            $journal = new Journal();
            $journal->type = $this->journal_type;
            $journal->currency = $currency_code;
            $journal->balance = 0;
            $this->journal()->save($journal);

            $journal->refresh();

            return $journal;
        }
    }
}
