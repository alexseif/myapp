<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Config\RectorConfig;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Symfony\Set\SymfonySetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
      __DIR__.'/app',
      __DIR__.'/config',
      __DIR__.'/src',
      __DIR__.'/tests',
      __DIR__.'/web',
      __DIR__.'/public',
    ]);

    // register a single rule
    $rectorConfig->rule(InlineConstructorDefaultToPropertyRector::class);

    // define sets of rules
    //    $rectorConfig->sets([
    //        LevelSetList::UP_TO_PHP_80
    //    ]);
    $rectorConfig->sets([
      LevelSetList::UP_TO_PHP_82,
      DoctrineSetList::ANNOTATIONS_TO_ATTRIBUTES,
      SymfonySetList::ANNOTATIONS_TO_ATTRIBUTES,
      SymfonySetList::SYMFONY_64,
      SymfonySetList::SYMFONY_CODE_QUALITY,
      SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,
    ]);
};
