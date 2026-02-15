# Verzování (cursor-rules: version-increment.mdc)

Při každé úpravě kódu **vždy** zvýš verzi (PATCH: 1.0.0 → 1.0.1).

## Kde aktualizovat

1. **api/version.php** – `define('APP_VERSION', '1.0.1');`
2. **index.html** – komentář `<!-- VERSION: 1.0.1 -->`, `?v=1.0.1` u CSS/JS, patička `v1.0.1`

Admin stránky čtou verzi z version.php a zobrazují ji v patičce.

## Formát

`vMAJOR.MINOR.PATCH` – při běžné změně zvyš PATCH.

## Závěrečná hláška

Po dokončení úkolů uveď: **Aktuální verze: vX.Y.Z** a zda byl projekt pushnut.
