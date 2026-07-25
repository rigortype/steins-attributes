<?php

declare(strict_types=1);

namespace Steins;

use Attribute;

/**
 * Declares that a function or method has no effects at all.
 *
 * The tightest envelope: the empty set. Equivalent to `#[Effect()]` and
 * preferred over it. Where both decorate the same declaration, `Pure` wins.
 *
 * ```php
 * use Steins\Pure;
 *
 * #[Pure]
 * function slugify(string $title): string
 * {
 *     return strtolower(trim($title));
 * }
 * ```
 *
 * Recognized fully qualified, qualified, or bare/aliased under a
 * `use Steins\Pure;`. A bare `#[Pure]` without that import is not recognized —
 * JetBrains' `#[Pure]` is a different attribute.
 *
 * Inert at runtime.
 *
 * @see Effect for a non-empty envelope.
 */
#[Attribute(Attribute::TARGET_FUNCTION | Attribute::TARGET_METHOD)]
final class Pure
{
}
