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

An envelope is an **upper bound the author asserts**, not a description the
analyzer derives. Steins checks the body against it and reports what the
envelope does not admit; declaring more than the body does is fine. Nothing is
imposed on code that does not opt in — the attributes are the opt-in.

Checking uses **prefix subsumption**: a declared `io` admits an inferred
`io.net.http`, so declarations stay as coarse as you want them while the
analyzer's catalog stays fine-grained. `#[Pure]` is the empty envelope, and the
tightest one.

## Labels must be plain string literals

Steins reads the arguments syntactically. A class constant, a concatenation, or
a named argument makes the whole attribute **unrecognized** — no envelope, and
no checking at all, silently. That is the conservative failure (the analyzer
never imposes checks it cannot read), and it is why this package deliberately
ships no constants class for the labels.

The core taxonomy: `output`, `output.header`; `io`, `io.fs`, `io.fs.read`,
`io.fs.write`, `io.net`, `io.net.http`, `io.db`, `io.ipc`, `io.process`,
`io.signal`; `global.read`, `global.write`; `nondet`, `nondet.random`,
`nondet.time`; `mutate`, `exit`, `ffi`; `failure`, `failure.environment`,
`failure.input`, `failure.resource`. Anything else is reported as
`effect.unknown-label` — typo safety is the analyzer's job.

## Why this is its own package

Not licence strategy. The analyzer is Apache-2.0 and this is MIT, but both are
permissive and the practical difference to anyone using either is close to
nothing — what actually differs is how long the text is and how widely the name
is recognized.

The reason is that you may not want the analyzer. These two classes are
vocabulary: you write them in your own source, and they mean something to any
tool that reads them. Installing this package **on its own** is a perfectly
sensible thing to do — annotate now, decide about a Rust binary later, or never.

```
composer require --dev rigortype/steins-attributes
```

`--dev` is enough, and not as a matter of convention. The classes are inert, and
measurably so: code declaring `#[Pure]` or `#[Effect(...)]` runs with them
absent from the installation entirely, and even
`ReflectionFunction::getAttributes()` still reports the attribute's name. Only
`newInstance()` — actually constructing the attribute object — needs the class
to exist, and that is something a development tool does, never your application.

Nothing forces the install either. Steins reads the attributes **syntactically**
and will not report `Steins\Pure` as an undefined class when the package is
absent. Your IDE and your other analyzers will, and they are right to: an import
of a class that is not there is worth flagging.

Installing the analyzer with `composer require --dev rigortype/steins` brings
this package along, so most users never add it explicitly.

## Recognized spellings

Fully qualified (`#[\Steins\Pure]`), qualified (`#[Steins\Pure]`), or
bare/aliased under a `use` import — `use Steins\Pure as P;` makes `#[P]` work.

A bare `#[Pure]` **without** the import is deliberately not recognized:
JetBrains' `#[Pure]` is a different attribute with different semantics, and
matching it would impose checks its author never requested.

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
