<?php

declare(strict_types=1);

namespace Steins;

use Attribute;
use function array_values;

/**
 * Declares the effects a function or method is permitted to have.
 *
 * An effect label is a hierarchical dot-path string, and checking uses **prefix
 * subsumption**: a declared `io` admits an inferred `io.net.http`. Declarations
 * stay as coarse as the author wants while the catalog stays fine-grained.
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
 * The envelope is an *upper bound* the author asserts. Steins reports an
 * `effect.*` finding when the body does something the envelope does not admit;
 * declaring more than the body does is allowed and is not reported.
 *
 * ## Labels must be plain string literals
 *
 * Steins reads the arguments syntactically, so every argument has to be a plain
 * string literal in the source. A class constant, a concatenation, or a named
 * argument makes the **whole attribute unrecognized** — which means no envelope
 * and *no checking at all*, silently. This is the conservative failure: Steins
 * never imposes checks it cannot read. It is also the reason this package ships
 * no constants class to reference here.
 *
 * ## The core label taxonomy
 *
 * An unrecognized label is reported as `effect.unknown-label` — typo safety is
 * the analyzer's job, not the author's. The core registry:
 *
 * - `output`, `output.header`
 * - `io`, `io.fs`, `io.fs.read`, `io.fs.write`, `io.net`, `io.net.http`,
 *   `io.db`, `io.ipc`, `io.process`, `io.signal`
 * - `global.read`, `global.write`
 * - `nondet`, `nondet.random`, `nondet.time`
 * - `mutate`, `exit`, `ffi`
 * - `failure`, `failure.environment`, `failure.input`, `failure.resource`
 *
 * Ecosystem and private labels (`io.redis`, `email.send`) are not yet
 * registrable and are reported as unknown for now.
 *
 * Steins recognizes this attribute when it is written fully qualified
 * (`#[\Steins\Effect(...)]`), qualified (`#[Steins\Effect(...)]`), or
 * bare/aliased under a `use Steins\Effect;`. Only the first `Effect` attribute
 * on a declaration is read.
 *
 * Runtime behaviour: none. This class is inert; `$labels` is kept so
 * reflection-based tooling can read what was declared.
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
        $this->labels = array_values($labels);
    }
}
