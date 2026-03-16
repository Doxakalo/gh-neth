<?php

namespace common\utils\formatters;

use yii\i18n\Formatter;
use NumberFormatter;
use Yii;

class SbcFormatter extends Formatter {

    /**
     * Formats a value as a currency string, hide decimals for integer values
     */
    public function asCurrencyWithSymbol($value) {
        $currencyLabel = $value > 1 ?
             \Yii::$app->params['currency.labelPlural'] ?? '' :
             \Yii::$app->params['currency.label'] ?? '';
        return sprintf('%s %s', $this->asCurrencyValue($value), $currencyLabel);
    }


    /**
     * Formats a value as a currency string, hide decimals for integer values, without currency symbol
     */
    public function asCurrencyValue($value, $forceFractional = false) {
        $options = [];
        if (is_numeric($value)) {
            if (fmod($value, 1) == 0.0 && !$forceFractional) {
                // Integer value: no decimals
                $options[NumberFormatter::MIN_FRACTION_DIGITS] = 0;
                $options[NumberFormatter::MAX_FRACTION_DIGITS] = 0;
            } else {
                // Decimal value: always 2 decimals
                $options[NumberFormatter::MIN_FRACTION_DIGITS] = 2;
                $options[NumberFormatter::MAX_FRACTION_DIGITS] = 2;
            }
        }
        return parent::asDecimal($value, null, $options);
    }


    /**
     * Sets the default locale for the formatter based on the user's browser/request
     */
    public static function setDefaultUserLocale() {
        Yii::$app->formatter->locale = Yii::$app->request->getAcceptableLanguages()[0] ?? 'en-US';
    }
    
}
