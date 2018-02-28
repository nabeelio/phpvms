<?php
/**
 *
 */

namespace App\Support;

use Akaunting\Money\Currency;
use Akaunting\Money\Money as MoneyBase;

/**
 * Compositional wrapper to MoneyPHP with some helpers
 * @package App\Support
 */
class Money
{
    public $money;
    public $subunit_amount;

    public static $iso_currencies;
    public static $subunit_multiplier;

    /**
     * @param $amount
     * @return MoneyBase
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    public static function create($amount)
    {
        return new MoneyBase($amount, static::currency());
    }

    /**
     * Convert a whole unit into it's subunit, e,g: dollar to cents
     * @param $amount
     * @return float|int
     */
    public static function convertToSubunit($amount)
    {
        $currency = config('phpvms.currency');
        return $amount * config('money.'.$currency.'.subunit');
    }

    /**
     * @return Currency
     * @throws \OutOfBoundsException
     */
    public static function currency()
    {
        return new Currency(config('phpvms.currency'));
    }

    /**
     * Money constructor.
     * @param $amount
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    public function __construct($amount)
    {
        $amount = static::convertToSubunit($amount);
        $this->money = static::create($amount);
    }

    /**
     * @return string
     */
    public function getAmount()
    {
        return $this->money->getValue();
    }

    /**
     * @return MoneyBase
     */
    public function getInstance()
    {
        return $this->money;
    }

    /**
     * @return int
     */
    public function getPrecision()
    {
        return $this->money->getCurrency()->getPrecision();
    }

    /**
     *
     * @return string
     */
    public function __toString()
    {
        return $this->money->format();
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
     * @param $percent
     * @return $this
     * @throws \OutOfBoundsException
     * @throws \InvalidArgumentException
     */
    public function addPercent($percent)
    {
        if (!is_numeric($percent)) {
            $percent = (float)$percent;
        }

        $amount = $this->money->multiply($percent / 100);
        $this->money = $this->money->add($amount);
        return $this;
    }

    /**
     * Subtract an amount
     * @param $amount
     * @return Money
     * @throws \InvalidArgumentException
     */
    public function subtract($amount)
    {
        $this->money = $this->money->subtract($amount);
        return $this;
    }

    /**
     * Multiply by an amount
     * @param $amount
     * @return Money
     * @throws \OutOfBoundsException
     * @throws \InvalidArgumentException
     */
    public function multiply($amount)
    {
        $this->money = $this->money->multiply($amount);
        return $this;
    }

    /**
     * Divide by an amount
     * @param $amount
     * @return Money
     * @throws \OutOfBoundsException
     * @throws \InvalidArgumentException
     */
    public function divide($amount)
    {
        $this->money = $this->money->divide($amount, PHP_ROUND_HALF_EVEN);
        return $this;
    }

    /**
     * @param $money
     * @return bool
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    public function equals($money)
    {
        if($money instanceof self) {
            return $this->money->equals($money->money);
        }

        if($money instanceof MoneyBase) {
            return $this->money->equals($money);
        }

        $money = static::convertToSubunit($money);
        return $this->money->equals(static::create($money));
    }
}
