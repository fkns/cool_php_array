<?php
/**
 * Created by PhpStorm.
 * User: yuta
 * Date: 2016/06/26
 * Time: 22:28
 */

require_once dirname(__FILE__) . '/vendor/autoload.php';

use Underscore\Types\Arrays as _;
use iter\fn;

const LOOP = 100;
const RANGE_MAX = 10000;

function bench($name, callable $fn)
{
    echo "$name\t";
    $start = microtime(true);
    for ($i = 0; $i < LOOP; ++$i) {
        $fn();
    }
    echo microtime(true) - $start, PHP_EOL;
}

bench('foreach', function () {
    $result = 0;
    $array = range(0, RANGE_MAX);
    foreach ($array as $v) {
        if ($v % 2) continue;
        $v *= 2;
        if ($v <= 20) continue;
        $result += $v;
    }

    return $result;
});

bench('for', function () {
    $result = 0;
    $array = range(0, RANGE_MAX);
    for ($i = 0; $i <= count($array); $i++) {
        $v = $i;
        if ($v % 2) continue;
        $v *= 2;
        if ($v <= 20) continue;
        $result += $v;
    }

    return $result;
});

bench('array_hoge', function () {
    $result = array_reduce(array_filter(array_map(function ($value) {
        return $value * 2;
    }, array_filter(range(0, RANGE_MAX), function ($value) {
        return $value % 2 === 0;
    })), function ($value) {
        return $value > 20;
    }), function ($carry, $item) {
        return $carry + $item;
    });

    return $result;
});

bench('underscore', function () {
    $result = (new _(_::range(0, RANGE_MAX)))->filter(function ($value) {
        return $value % 2 === 0;
    })->each(function ($value) {
        return $value * 2;
    })->filter(function ($value) {
        return $value > 20;
    })->reduce(function ($carry, $item) {
        return $carry + $item;
    });

    return $result;
});

bench('nikic/iter', function () {
    $range = iter\range(0, RANGE_MAX);

    $filtered = iter\filter(function ($value) {
        return $value % 2 === 0;
    }, $range);

    $mapped = iter\map(fn\operator('*', 2), $filtered);

    $filtered2 = iter\filter(function ($value) {
        return $value > 20;
    }, $mapped);

    $result = iter\reduce(function ($carry, $item) {
        return $carry + $item;
    }, $filtered2);

    return $result;
});

bench('Ginq', function () {
    $result = Ginq::range(0, RANGE_MAX)
        ->where(function ($value) {
            return $value % 2 === 0;
        })
        ->select(function ($value) {
            return $value * 2;
        })
        ->where(function ($value) {
            return $value > 20;
        })->sum();;

    return $result;
});