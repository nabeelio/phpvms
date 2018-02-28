<?php
/**
 *
 */

namespace App\Support;

use Money\Currency;
use Money\Money as MoneyBase;

/**
 * Compositional wrapper to MoneyPHP with some helpers
 * @package App\Support
 */
class Money
{
    public $money;

    /**
     * @param $amount
     * @return MoneyBase
     * @throws \InvalidArgumentException
     */
    public static function create($amount)
    {
        return new MoneyBase(
            $amount,
            new Currency(config('phpvms.currency'))
        );
    }

    /**
     * Money constructor.
     * @param $amount
     * @throws \InvalidArgumentException
     */
    public function __construct($amount)
    {
        $this->money = static::create($amount);
    }

    /**
     * @return string
     */
    public function getAmount()
    {
        return $this->money->getAmount();
    }

    /**
     *
     * @return string
     */
    public function __toString()
    {
        // TODO: Implement __toString() method.
        return $this->money->getAmount();
    }

    /**
     * Add an amount
     * @param $amount
     */
    public function add($amount)
    {
        $this->money = $this->money->add($amount);
    }

    /**
     * Subtract an amount
     * @param $amount
     */
    public function subtract($amount)
    {
        $this->money = $this->money->subtract($amount);
    }

    /**
     * Multiply by an amount
     * @param $amount
     */
    public function multiply($amount)
    {
        $this->money = $this->money->multiply($amount);
    }

    /**
     * Divide by an amount
     * @param $amount
     */
    public function divide($amount)
    {
        $this->money = $this->money->divide($amount);
    }

    /**
     * @param $money
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function equals($money)
    {
        if($money instanceof Money) {
            return $this->money->equals($money->money);
        } elseif($money instanceof MoneyBase) {
            return $this->money->equals($money);
        } else {
            return $this->money->equals(static::create($money));
        }
    }
}
