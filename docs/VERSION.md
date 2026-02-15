# Verzování (cursor-rules: version-increment.mdc)

Při každé úpravě kódu **vždy** zvýš verzi (PATCH: 1.0.0 → 1.0.1).

## Kde aktualizovat

**Pouze api/version.php** – `define('APP_VERSION', '1.0.2');`

Web (index.php) i admin stránky čtou verzi z version.php automaticky.

## Formát

`vMAJOR.MINOR.PATCH` – při běžné změně zvyš PATCH.

## Závěrečná hláška

Po dokončení úkolů uveď: **Aktuální verze: vX.Y.Z** a zda byl projekt pushnut.
