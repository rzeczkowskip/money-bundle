<?php

namespace JK\MoneyBundle\Form\DataTransformer;

use Money\Currencies;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Exception\ParserException;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Parser\DecimalMoneyParser;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\DataTransformer\NumberToLocalizedStringTransformer;

/**
 * Transforms between a normalized format and a localized money string.
 * @author Jakub Kucharovic <jakub@kucharovic.cz>
 */
class MoneyToLocalizedStringTransformer extends NumberToLocalizedStringTransformer
{
    /** @var \Money\Currency * */
    private $currency;
    /** @var \Money\Currencies|null */
    private $currencies;

    /**
     * @param \Money\Currency|string $currency
     * @param int $scale
     * @param bool $grouping
     * @param \Money\Currencies|null $currencies
     */
    public function __construct($currency, $scale, $grouping, Currencies $currencies = null)
    {
        if (!$currency instanceof Currency) {
            @trigger_error('Passing a currency as string is deprecated since 1.1 and will be removed in 2.0. Please pass a ' . Currency::class . ' instance instead.', E_USER_DEPRECATED);
            $currency = new Currency($currency);
        }
        $this->currency   = $currency;
        $this->currencies = $currencies ? : new ISOCurrencies();

        parent::__construct($scale, $grouping);
    }

    /**
     * Transforms a normalized format into a localized money string.
     *
     * @param \Money\Money $value Money object
     *
     * @return string Localized money string
     * @throws TransformationFailedException If the given value is not numeric or
     *                                       if the value can not be transformed.
     */
    public function transform($value)
    {
        if (null === $value) {
            return '';
        }

        $moneyFormatter = new DecimalMoneyFormatter($this->currencies);

        return parent::transform($moneyFormatter->format($value));
    }

    /**
     * Transforms a localized money string into a normalized format.
     *
     * @param string $value Localized money string
     *
     * @return \Money\Money Money object
     * @throws TransformationFailedException If the given value is not a string
     *                                       or if the value can not be transformed.
     */
    public function reverseTransform($value)
    {
        $value = parent::reverseTransform($value);

        $moneyParser = new DecimalMoneyParser($this->currencies);

        try {
            $money = $moneyParser->parse(sprintf('%.53f', $value), $this->currency);

            return $money;
        } catch (ParserException $e) {
            throw new TransformationFailedException($e->getMessage());
        }
    }
}
