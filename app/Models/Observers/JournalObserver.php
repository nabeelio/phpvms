<?php

namespace App\Models\Observers;

use App\Models\Journal;

/**
 * Class JournalObserver
 */
class JournalObserver
{
    /**
     * @param Journal $journal
     *
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    public function creating(Journal $journal): void
    {
        $journal->balance = 0;
    }
}
