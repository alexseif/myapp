<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace App\Logic;

/**
 * Description of CostOfLifeLogic.
 *
 * @author Alex Seif <me@alexseif.com>
 */
class CostOfLifeLogic
{
    public $col;
    public $cost;
    public $currencies;
    public $units = [
        'cost' => ['factor' => 1, 'precision' => 0],
        'profit' => ['factor' => 0.2, 'precision' => 0],
        'monthly' => ['factor' => 1.2, 'precision' => 0],
        'hour' => ['factor' => 1.2 / 122, 'precision' => 0],
        'day' => ['factor' => 1.2 / 122 * 6, 'precision' => 0],
        'week' => ['factor' => 1.2 / 122 * 31, 'precision' => 0],
        'month' => ['factor' => 1.2, 'precision' => 0],
        'annually' => ['factor' => 1.2 * 12, 'precision' => 0],
    ];

    public function __construct($cost, $currencies)
    {
        $this->cost = $cost;
        $this->currencies = $currencies;
        $this->calc();
    }

    public function calc(): void
    {
        foreach ($this->currencies as $currency) {
            foreach ($this->units as $unit => $factor) {
                $raw = $this->cost * $factor['factor'] * ($currency->getEgp() / 100);
                $round = (round($raw, $factor['precision'])) ? round($raw, $factor['precision']) : round($raw, $factor['precision'] + 1);
                $this->col[$unit][$currency->getCode()] = ($round) ? $round : $raw;
            }
        }
    }

    public function getMonthly()
    {
        return $this->col['monthly']['EGP'];
    }

    public function getWeekly()
    {
        return $this->col['week']['EGP'];
    }

    public function getDaily()
    {
        return $this->col['day']['EGP'];
    }

    public function getHourly()
    {
        return $this->col['hour']['EGP'];
    }
}
