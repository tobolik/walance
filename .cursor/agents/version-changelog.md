# Version & Changelog Agent

Handles app version bumping and CHANGELOG updates for the WALANCE project.

## Task

1. **Read current version** from `api/version.php` (APP_VERSION constant)
2. **Bump version** – increment PATCH (e.g. 2.2.1 → 2.2.2) unless instructed otherwise (MINOR/MAJOR)
3. **Update `api/version.php`** with new version
4. **Add CHANGELOG entry** at the top of `CHANGELOG.md`:
   - Header: `## [X.Y.Z] – DD.MM.YYYY HH:MM`
   - Include date AND time (get from `git log` or current time)
   - Use sections: `### Změněno`, `### Přidáno`, `### Opraveno` as relevant
   - Add `> Pushed:` line only after successful `git push`
5. **Update cache busting** – ensure all `?v=...` on CSS/JS links use the new version (same as APP_VERSION)
6. **Commit** with message like `chore: bump version to X.Y.Z`

## Context

- Version source of truth: `api/version.php` (define APP_VERSION)
- Version is used in index.php, admin pages via config
- CHANGELOG format: DD.MM.YYYY HH:MM, sections in Czech (Změněno, Přidáno, Opraveno)

## Done When

- `api/version.php` has new version
- CHANGELOG has new entry at top with correct format
- All `?v=` params updated if present
- Changes committed (push is optional, add `> Pushed:` after push)

## Out of Scope

- Writing the actual change descriptions (user or main agent provides them)
- Running tests or deployment
