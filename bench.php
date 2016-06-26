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

const LOOP = 10;
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

bench('nikic/iter', function () {
    $range = iter\range(0, RANGE_MAX);
    $filtered = iter\filter(function ($_) {
        return $_ % 2 === 0;
    }, $range);
    $mapped = iter\map(fn\operator('*', 2), $filtered);
    $filtered2 = iter\filter(function ($_) {
        return $_ > 20;
    }, $mapped);
    $result = iter\reduce(function ($carry, $item) {
        return $carry + $item;
    }, $filtered2);

    echo $result, PHP_EOL;
});

bench('underscore', function () {
    $result = (new _(_::range(0, RANGE_MAX)))->filter(function ($_) {
        return $_ % 2 === 0;
    })->each(function ($_) {
        return $_ * 2;
    })->filter(function ($_) {
        return $_ > 20;
    })->reduce(function ($carry, $item) {
        return $carry + $item;
    });

    echo $result, PHP_EOL;
});

bench('Ginq', function () {
    $result = Ginq::range(0, RANGE_MAX)
        ->where(function ($_) {
            return $_ % 2 === 0;
        })
        ->select(function ($_) {
            return $_ * 2;
        })
        ->where(function ($_) {
            return $_ > 20;
        })->sum();;

    echo $result, PHP_EOL;
});

bench('foreach', function () {
    $result = 0;
    $array = range(0, RANGE_MAX);
    foreach ($array as $v) {
        if ($v % 2) continue;
        $v *= 2;
        if ($v <= 20) continue;
        $result += $v;
    }

    echo $result, PHP_EOL;
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

    echo $result, PHP_EOL;
});