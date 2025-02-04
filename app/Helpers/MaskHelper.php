<?php

namespace App\Helpers;

use Filament\Support\RawJs;

class MaskHelper
{
    /**
     * Format currency with thousand separator
     */
    public static function currencyMask(): RawJs
    {
        return RawJs::make(<<<'JS'
            $el.addEventListener('keypress', function(e) { if (!/[0-9]/.test(e.key)) e.preventDefault(); });
            $input.replace(/\D/g, '').replace(/^0+/, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')
        JS);
    }

    /**
     * Allow numbers and single dot
     */
    public static function numericWithDotMask(): RawJs
    {
        return RawJs::make(<<<'JS'
            $el.addEventListener('keypress', function(e) {
                if (!/[0-9.]/.test(e.key) || (e.key === '.' && this.value.includes('.'))) e.preventDefault();
            });
            $input.replace(/[^0-9.]/g, '').replace(/^0+/, '').replace(/(\..*?)\..*/g, '$1')
        JS);
    }

    /**
     * Allow numbers only
     */
    public static function numericOnlyMask(): RawJs
    {
        return RawJs::make(<<<'JS'
            $el.addEventListener('keypress', function(e) { if (!/[0-9]/.test(e.key)) e.preventDefault(); });
            $input.replace(/[^0-9]/g, '').replace(/^0+/, '')
        JS);
    }

    /**
     * Percentage input (0-100) with up to 2 decimal places
     */
    public static function discountMask(): RawJs
    {
        return RawJs::make(<<<'JS'
            $el.addEventListener('keypress', function(e) {
                if (!/[0-9.]/.test(e.key) || (e.key === '.' && this.value.includes('.'))) e.preventDefault();
            });
            let value = $input.replace(/[^0-9.]/g, '').replace(/^0+/, '');
            if (value.includes('.')) {
                let [whole, decimal] = value.split('.');
                value = whole + '.' + (decimal || '').substring(0, 2);
            }
            return parseFloat(value) > 100 ? '100' : value;
        JS);
    }
}