# Changelog

All notable changes to this package are recorded here. The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and versions follow [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

**The contract is the class names and the argument forms the analyzer accepts** — `Steins\Pure`, `Steins\Effect`, and plain string literals. Docblock wording is not. Adding a class is a minor bump; changing what an existing one accepts is breaking, because the code that stops being understood is the user's own source.

## [Unreleased]

## [0.1.0] - 2026-07-25

The effect vocabulary PHP;STEINS reads: two attribute classes, both inert at runtime, in their own MIT package so they can be adopted without the analyzer.

### Added

- **`#[\Steins\Pure]`** — declares that a function or method has no effects at all, the empty envelope and the tightest one.
- **`#[\Steins\Effect(...)]`** — declares the effects a function or method is permitted to have, as hierarchical dot-path labels checked by prefix, so a declared `io` admits an inferred `io.net.http`.
  - Labels must be plain string literals: a class constant, a concatenation, or a named argument makes the whole attribute unrecognized, silently. This is why no constants class ships for them.
- Recognized fully qualified, qualified, or bare under a `use` import — including an alias, so `use Steins\Pure as P;` makes `#[P]` work. A bare `#[Pure]` without the import is deliberately not recognized: JetBrains' `#[Pure]` is a different attribute with different semantics.

[Unreleased]: https://github.com/rigortype/steins-attributes/compare/v0.1.0...HEAD
[0.1.0]: https://github.com/rigortype/steins-attributes/releases/tag/v0.1.0
