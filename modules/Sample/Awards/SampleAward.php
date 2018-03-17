<?php

namespace Modules\Sample\Awards;

use App\Interfaces\AwardInterface;

/**
 * Class SampleAward
 * @package Modules\Sample\Awards
 */
class SampleAward extends AwardInterface
{
    public $name = 'Sample Award';

    public function check(): bool
    {
        return false;
    }
}
