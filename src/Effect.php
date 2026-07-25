<?php

declare(strict_types=1);

namespace Steins;

use Attribute;

use function array_values;

/**
 * Declares the effects a function or method is permitted to have.
 *
 * A label is a hierarchical dot-path, and checking uses prefix subsumption: a
 * declared `io` admits an inferred `io.net.http`.
 *
 * ```php
 * use Steins\Effect;
 *
 * #[Effect('io.fs.write', 'nondet.time')]
 * function appendAudit(string $line): void
 * {
 *     file_put_contents('/var/log/audit', date('c') . " {$line}\n", FILE_APPEND);
 * }
 * ```
 *
 * **Labels must be plain string literals.** A class constant, a concatenation,
 * or a named argument makes the whole attribute unrecognized — no envelope, no
 * checking, silently.
 *
 * Core taxonomy: `output`, `output.header`; `io`, `io.fs`, `io.fs.read`,
 * `io.fs.write`, `io.net`, `io.net.http`, `io.db`, `io.ipc`, `io.process`,
 * `io.signal`; `global.read`, `global.write`; `nondet`, `nondet.random`,
 * `nondet.time`; `mutate`, `exit`, `ffi`; `failure`, `failure.environment`,
 * `failure.input`, `failure.resource`. Anything else is `effect.unknown-label`.
 *
 * Recognized fully qualified, qualified, or bare/aliased under a
 * `use Steins\Effect;`. Only the first `Effect` on a declaration is read.
 *
 * Inert at runtime; `$labels` is kept for reflection-based tooling.
 *
 * @see Pure for the empty envelope.
 */
#[Attribute(Attribute::TARGET_FUNCTION | Attribute::TARGET_METHOD)]
final class Effect
{
    /** @var list<non-empty-string> */
    public readonly array $labels;

    /**
     * @param non-empty-string ...$labels Effect labels, as plain string literals.
     */
    public function __construct(string ...$labels)
    {
        // Named arguments key a variadic by parameter name, so this is what
        // makes the `list<>` above true. Not redundant.
        $this->labels = array_values($labels);
    }
}
