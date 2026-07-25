#!/usr/bin/env php
<?php

// The classes exist, are inert, and target what the analyzer reads them on.
//
// There is very little to test in two attribute classes, and exactly one thing
// worth testing: that PHP agrees with the analyzer about where they may appear.
// Steins reads `#[Pure]` / `#[Effect]` on function and method declarations; if
// the #[Attribute] targets here said something else, PHP would reject at
// reflection time code the analyzer accepts, and the two would disagree about
// the same file.
//
//     php tests/attributes.php

declare(strict_types=1);

use Steins\Effect;
use Steins\Pure;

require __DIR__ . '/../src/Pure.php';
require __DIR__ . '/../src/Effect.php';

$failures = 0;
$checks = 0;

$ok = static function (string $label, bool $passed) use (&$failures, &$checks): void {
    $checks++;
    if ($passed) {
        printf("  ok    %s\n", $label);

        return;
    }

    $failures++;
    printf("  FAIL  %s\n", $label);
};

// The names the analyzer compares against, case-folded, are `steins\pure` and
// `steins\effect`. Nothing else resolves.
$ok('Steins\Pure exists', class_exists(Pure::class));
$ok('Steins\Effect exists', class_exists(Effect::class));
$ok('Pure is named Steins\Pure', Pure::class === 'Steins\Pure');
$ok('Effect is named Steins\Effect', Effect::class === 'Steins\Effect');

$pure = new ReflectionClass(Pure::class);
$effect = new ReflectionClass(Effect::class);

$ok('Pure is final', $pure->isFinal());
$ok('Effect is final', $effect->isFinal());

// Inert means inert: no state on Pure, no behaviour on either.
$ok('Pure has no constructor', $pure->getConstructor() === null);
$ok('Pure declares no methods', $pure->getMethods() === []);
$ok('Effect declares only a constructor', array_map(
    static fn(ReflectionMethod $m): string => $m->getName(),
    $effect->getMethods(),
) === ['__construct']);

$targetOf = static function (ReflectionClass $class): int {
    $attributes = $class->getAttributes(Attribute::class);
    if ($attributes === []) {
        return 0;
    }

    return $attributes[0]->newInstance()->flags;
};

$expected = Attribute::TARGET_FUNCTION | Attribute::TARGET_METHOD;
$ok('Pure targets functions and methods, and only those', $targetOf($pure) === $expected);
$ok('Effect targets functions and methods, and only those', $targetOf($effect) === $expected);

// Labels arrive as a variadic list because that is the only argument form the
// analyzer reads — a named argument makes the whole attribute unrecognized.
$e = new Effect('io.fs.write', 'nondet.time');
$ok('Effect keeps its labels in order', $e->labels === ['io.fs.write', 'nondet.time']);
$ok('Effect with no labels is the empty list', (new Effect())->labels === []);

// Named arguments into a variadic collect with STRING KEYS, so a raw `$labels`
// would not be a list and the `list<non-empty-string>` annotation would be a
// lie. `array_values()` in the constructor is what makes it true — this pins
// that, so nobody removes it as redundant.
$ok(
    'named arguments still produce a list',
    array_is_list((new Effect(first: 'io', second: 'nondet.time'))->labels),
);

// The round trip an IDE or a reflection-based tool would make.
$fn = new ReflectionFunction(
    #[Effect('io.fs.read')]
    static function (): void {},
);
$attributes = $fn->getAttributes(Effect::class);
$ok('Effect survives reflection on a closure', $attributes !== []);
$ok(
    'the reflected instance carries the declared label',
    $attributes !== [] && $attributes[0]->newInstance()->labels === ['io.fs.read'],
);

printf("\n%d checks, %d failed\n", $checks, $failures);

exit($failures === 0 ? 0 : 1);
