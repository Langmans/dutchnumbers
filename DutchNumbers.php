<?php

class DutchNumbers
{
    const AND_ANS = 2;
    const AND_NEVER = 1;
    const AND_ALWAYS = 3;

    const HUNDREDFOLD_HUNDRED = 4;
    const HUNDREDFOLD_YEARS = 5;
    const HUNDREDFOLD_NONE = 6;

    protected static $and_modes = array(
        self::AND_ANS => 'DutchNumbers::AND_ANS',
        self::AND_NEVER => 'DutchNumbers::AND_NEVER',
        self::AND_ALWAYS => 'DutchNumbers::AND_ALWAYS'
    );
    protected static $hundredfold_modes = array(
        self::HUNDREDFOLD_HUNDRED => 'DutchNumbers::HUNDREDFOLD_HUNDRED',
        self::HUNDREDFOLD_YEARS => 'DutchNumbers::HUNDREDFOLD_YEARS',
        self::HUNDREDFOLD_NONE => 'DutchNumbers::HUNDREDFOLD_NONE',
    );

    static $units = array(
        'nul', 'één', 'twee', 'drie', 'vier', 'vijf', 'zes', 'zeven', 'acht', 'negen', 'tien',
        'elf', 'twaalf', 'dertien', 'veertien', 'vijftien', 'zestien', 'zeventien', 'achttien', 'negentien'
    );
    static $tens = array(
        2 => 'twintig', 'dertig', 'veertig', 'vijftig', 'zestig', 'zeventig', 'tachtig', 'negentig'
    );

    public static function format(
        $number,
        $and_mode = self::AND_ANS,
        $hundredfold_mode = self::HUNDREDFOLD_HUNDRED
    )
    {
        if (!is_numeric($number)) {
            throw new InvalidArgumentException($number . ' is not a number.');
        }

        if (!isset(static::$and_modes[$and_mode])) {
            throw new InvalidArgumentException('Wrong and mode. expected one of ' . implode(' or ', static::$and_modes));
        }

        if (!isset(static::$hundredfold_modes[$hundredfold_mode])) {
            throw new InvalidArgumentException('Wrong hundredfold mode. expected one of ' . implode(' or ', static::$hundredfold_modes));
        }
        return static::_format($number, $and_mode, $hundredfold_mode);
    }

    protected static function _format($i, $and_mode = null, $hundredfold_mode = null)
    {
        $s = null;
        $i = (int)$i;
        // 0..19: enum
        if ($i < 20) return static::$units[$i];
        // 19..99: no units when 0, always 'en' to connect (but 'ën' for 2 and 3)
        if ($i % 10 == 0) {
            $s = "";
        } else {
            $s = static::$units[$i % 10];
            if ($i % 10 == 2 || $i % 10 == 3) {
                $s .= 'ën';
            } else {
                $s .= 'en';
            }
        }
        if ($i < 100) return $s . static::$tens[(int)floor($i / 10)];
        // 100..199: add 'en' as in iEn (for 2 see ANS page 290)
        if (($and_mode == static::AND_NEVER) || (($and_mode == static::AND_ANS) && ($i % 100 < 13))) {
            $s = "en ";
        } else {
            $s = "";
        }
        if ($i % 100 == 0) {
            $s = "";
        } else {
            $s = " " . $s . static::_format($i % 100);
        } // only for 100
        if ($i < 200) return "honderd" . $s;
        // 199..999
        if ($and_mode == static::AND_ALWAYS) {
            $s = "en ";
        } else {
            $s = "";
        }
        if ($i % 100 == 0) {
            $s = "";
        } else {
            $s = " " . $s . static::_format($i % 100);
        }
        if ($i < 1000) return static::$units[(int)floor($i / 100)] . "honderd" . $s;
        // 1000..1099: add 'en' as in iEn (for 2 see ANS page 290)
        if (($and_mode == static::AND_ALWAYS) || (($and_mode == static::AND_ANS) && ($i % 1000 < 13))) {
            $s = "en ";
        } else {
            $s = "";
        }
        if ($i % 1000 == 0) {
            $s = "";
        } else {
            $s = " " . $s . static::_format($i % 100);
        } // only for 1000
        if ($i < 1100) return "duizend" . $s;
        // 1100..9999
        if ($hundredfold_mode == static::HUNDREDFOLD_NONE) {
            // 1100..1999
            if ($i < 2000) return "duizend " . static::_format($i % 1000, $and_mode);
            // 2000..9999
            if ($i % 1000 == 0) {
                $s = "";
            } else {
                $s = " " . static::_format($i % 1000, $and_mode);
            }
            if ($i < 10000) return static::$units[(int)floor($i / 1000)] . "duizend" . $s;
        } else {
            if ($hundredfold_mode == static::HUNDREDFOLD_HUNDRED) {
                $s = "honderd";
            } else {
                $s = "";
            }
            if ($i % 100 == 0) {
                $s = "honderd";
            } else {
                $s = $s . " " . static::_format($i % 100);
            }
            if ($i < 10000) return static::_format(floor($i / 100), $and_mode) . $s;
        }
        // 10 000 .. 999 999: add 'en' as in iEn (for 2 see ANS page 290)
        if ($i % 1000 == 0) {
            $s = "";
        } else {
            $s = " " . static::_format($i % 1000, $and_mode);
        }
        if ($i < 1000000) return static::_format(floor($i / 1000), $and_mode) . "duizend" . $s;
        //
        if ($i % 1000000 == 0) {
            $s = "";
        } else {
            $s = " " . static::_format($i % 1000000, $and_mode);
        }
        if ($i < 1000000000) return static::_format(floor($i / 1000000), $and_mode) . " miljoen" . $s;
        //
        if ($i % 1000000000 == 0) {
            $s = "";
        } else {
            $s = " " . static::_format($i % 1000000000, $and_mode);
        }
        if ($i < 1000000000000) return static::_format(floor($i / 1000000000), $and_mode) . " miljard" . $s;
        throw new Exception('Too large..');
    }
}
