# Verzování (cursor-rules: version-increment.mdc)

Při každé úpravě kódu **vždy** zvýš verzi (PATCH: 1.0.0 → 1.0.1).

## Kde aktualizovat

**Pouze api/version.php** – `define('APP_VERSION', '1.0.2');`

Web (index.php) i admin stránky čtou verzi z version.php automaticky.

## Formát

`vMAJOR.MINOR.PATCH` – při běžné změně zvyš PATCH.

## CHANGELOG – datum a čas

Při každém povýšení verze přidej záznam do `CHANGELOG.md`:

```markdown
## [1.0.25] – 15.02.2026 22:30
```

**Povinně:** datum **a** čas ve formátu `DD.MM.YYYY HH:MM`. Nikdy jen datum.

Čas získáš např. z `git log --format="%ad" --date=format:"%d.%m.%Y %H:%M" -1`.

## Závěrečná hláška

Po dokončení úkolů uveď: **Aktuální verze: vX.Y.Z** a zda byl projekt pushnut.
