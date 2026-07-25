# PHP;STEINS Attributes

Effect-envelope attributes for [PHP;STEINS](https://github.com/rigortype/steins), a
value-precise static analyzer for PHP.

```
composer require --dev rigortype/steins-attributes
```

Two classes, both inert at runtime:

```php
use Steins\Effect;
use Steins\Pure;

#[Pure]
function slugify(string $title): string
{
    return strtolower(trim($title));
}

#[Effect('io.fs.write', 'nondet.time')]
function appendAudit(string $line): void
{
    file_put_contents('/var/log/audit', date('c') . " {$line}\n", FILE_APPEND);
}
```

An envelope is an upper bound you assert, not a description the analyzer
derives. Steins reports what the envelope does not admit; declaring more than
the body does is fine. Checking uses **prefix subsumption** — a declared `io`
admits an inferred `io.net.http` — so declarations stay as coarse as you like.
`#[Pure]` is the empty envelope.

## Labels must be plain string literals

A class constant, a concatenation, or a named argument makes the whole attribute
**unrecognized** — no envelope, no checking, silently. Which is also why this
package ships no constants class for the labels.

The core taxonomy: `output`, `output.header`; `io`, `io.fs`, `io.fs.read`,
`io.fs.write`, `io.net`, `io.net.http`, `io.db`, `io.ipc`, `io.process`,
`io.signal`; `global.read`, `global.write`; `nondet`, `nondet.random`,
`nondet.time`; `mutate`, `exit`, `ffi`; `failure`, `failure.environment`,
`failure.input`, `failure.resource`. Anything else is `effect.unknown-label`.

## Recognized spellings

Fully qualified (`#[\Steins\Pure]`), qualified (`#[Steins\Pure]`), or
bare/aliased under a `use` import — `use Steins\Pure as P;` makes `#[P]` work.

A bare `#[Pure]` **without** the import is not recognized: JetBrains' `#[Pure]`
is a different attribute, and matching it would impose checks its author never
asked for.

## Why this is its own package

You may not want the analyzer. These are vocabulary — you write them in your own
source, and they mean something to any tool that reads them — so installing this
package alone is a perfectly good end state.

`--dev` is enough as a fact, not a convention: code declaring `#[Pure]` or
`#[Effect(...)]` runs with the classes absent entirely, and even
`getAttributes()` still reports the name. Only `newInstance()` needs the class,
and only a development tool calls it.

Nothing forces the install, either. Steins reads the attributes syntactically
and will not report `Steins\Pure` as undefined when the package is missing. Your
IDE and your other analyzers will, and they are right to.

`composer require --dev rigortype/steins` brings this package along, so most
users never add it explicitly.

## Copyright

This package is licensed under [MIT License](LICENSE).

```
Copyright (c) 2026 TypedDuck, USAMI Kenta <tadsan@zonu.me>

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```
