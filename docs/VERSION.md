# Verzování

Při každé úpravě kódu **vždy** povýš verzi. **Používej výhradně skript** – nikdy neupravuj `api/version.php` ani `CHANGELOG.md` ručně.

**Cursor AI agent:** Pravidla v `.cursor/rules/versioning/` vyžadují, aby agent skript spouštěl sám před push a po push.

## Skript (jediný správný způsob)

```bash
php script/bump-version.php patch [--added "popis"] [--changed "popis"] [--fixed "popis"]
```

- **patch** – běžná změna (1.0.0 → 1.0.1)
- **minor** – nová funkce (1.0.1 → 1.1.0)
- **major** – zlomová změna (1.1.0 → 2.0.0)
- **--added, --changed, --fixed** – položky do CHANGELOG (lze opakovat)

Datum a čas se berou ze systému v okamžiku spuštění – stoprocentní správnost (Europe/Prague).

Po `git push` spusť:

```bash
php script/bump-version.php mark-pushed
```

## Kde je verze

**api/version.php** – `define('APP_VERSION', '1.0.2');`

Web (index.php) i admin stránky čtou verzi z version.php automaticky. Cache busting (`?v=`) je dynamický.

## Závěrečná hláška

Po dokončení úkolů uveď: **Aktuální verze: vX.Y.Z** a zda byl projekt pushnut.
