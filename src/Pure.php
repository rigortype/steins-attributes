<?php

declare(strict_types=1);

namespace Steins;

use Attribute;

/**
 * Declares that a function or method has **no effects at all**.
 *
 * `Pure` is the tightest effect envelope: the empty set. Steins checks the
 * declaration against what it infers, and reports an `effect.*` finding when the
 * body does something the envelope does not admit. It is an *upper bound* the
 * author asserts, not a description Steins derives — nothing here changes
 * behaviour, and nothing is imposed on code that does not opt in.
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
 * Equivalent to `#[Effect()]` with no labels, and preferred over it: the name
 * says what the empty set means. Where both `#[Pure]` and `#[Effect(...)]`
 * decorate the same declaration, `Pure` wins — it is the tighter bound — and
 * the contradiction is not reported.
 *
 * Steins recognizes this attribute when it is written fully qualified
 * (`#[\Steins\Pure]`), qualified (`#[Steins\Pure]`), or bare/aliased under a
 * `use Steins\Pure;` — including `use Steins\Pure as P;`, which makes `#[P]`
 * work. A bare `#[Pure]` **without** that import is deliberately not recognized:
 * JetBrains' `#[Pure]` is a different attribute with different semantics, and
 * matching it would impose checks its author never requested.
 *
 * Runtime behaviour: none. This class is inert.
 *
 * @see Effect for a non-empty envelope.
 */
#[Attribute(Attribute::TARGET_FUNCTION | Attribute::TARGET_METHOD)]
final class Pure
{
}
