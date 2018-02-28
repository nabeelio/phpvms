<?php
/**
 *
 */

namespace App\Support;

use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money as MoneyBase;

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
     * @throws \Money\Exception\UnknownCurrencyException
     */
    public static function convertToSubunit($amount)
    {
        if (!self::$subunit_multiplier) {
            self::$iso_currencies = new ISOCurrencies();
            static::$subunit_multiplier = 10 ** self::$iso_currencies->subunitFor(static::currency());
        }

        return $amount * static::$subunit_multiplier;
    }

    /**
     * @return Currency
     */
    public static function currency()
    {
        return new Currency(config('phpvms.currency'));
    }

    /**
     * Money constructor.
     * @param $amount
     * @throws \Money\Exception\UnknownCurrencyException
     * @throws \InvalidArgumentException
     */
    public function __construct($amount)
    {
        $amount = static::convertToSubunit($amount);
        $this->money = static::create($amount);
    }

    /**
     * @return MoneyBase
     */
    public function getInstance()
    {
        return $this->money;
    }

    /**
     * @return string
     */
    public function getAmount()
    {
        $moneyFormatter = new DecimalMoneyFormatter(self::$iso_currencies);
        return $moneyFormatter->format($this->money);
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
     * @param $percent
     * @return $this
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
     */
    public function divide($amount)
    {
        $this->money = $this->money->divide($amount, PHP_ROUND_HALF_EVEN);
        return $this;
    }

    /**
     * @param $money
     * @return bool
     * @throws \Money\Exception\UnknownCurrencyException
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
