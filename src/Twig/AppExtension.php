<?php
// src/Twig/AppExtension.php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{

    public function getFilters(): array
    {
        return [
            new TwigFilter('ago', [$this, 'agoFilter']),
        ];
    }

    public function agoFilter(\DateTime $date)
    {
        $now = new \DateTime();
        $interval = $now->diff($date);

        if ($interval->y > 0) {
            return $interval->y . ' year(s) ago';
        }

        if ($interval->m > 0) {
            return $interval->m . ' month(s) ago';
        }

        if ($interval->d > 0) {
            return $interval->d . ' day(s) ago';
        }

        if ($interval->h > 0) {
            return $interval->h . ' hour(s) ago';
        }

        if ($interval->i > 0) {
            return $interval->i . ' minute(s) ago';
        }

        return 'just now';
    }
}
