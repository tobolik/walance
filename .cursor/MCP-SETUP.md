# MCP Sub-Agent Setup – Version & Changelog

This project includes a sub-agent for version bumping and changelog updates. To use it, configure the **sub-agents-mcp** server.

## Prerequisites

- Node.js 20+
- [Cursor CLI](https://cursor.com/docs/cli) with `cursor-agent` (run `cursor-agent login` once)

## Setup

1. **Install sub-agents-mcp** – it runs via `npx`, no global install needed.

2. **Edit your MCP config** – add this to `~/.cursor/mcp.json` (or `%USERPROFILE%\.cursor\mcp.json` on Windows):

```json
{
  "mcpServers": {
    "sub-agents": {
      "command": "npx",
      "args": ["-y", "sub-agents-mcp"],
      "env": {
        "AGENTS_DIR": "C:\\weby\\walance\\.cursor\\agents",
        "AGENT_TYPE": "cursor"
      }
    }
  }
}
```

**Important:** Use an **absolute path** for `AGENTS_DIR`. Adjust the path to match your project location.

3. **Restart Cursor** so it picks up the MCP config.

## Usage

Ask the main AI to delegate version/changelog work to the agent:

- *"Use the version-changelog agent to bump version to 2.2.2 and add changelog entry for the booking fix"*
- *"Use the version-changelog agent to bump PATCH and document the new FAQ section"*

The agent will:
- Bump version in `api/version.php`
- Add a CHANGELOG entry
- Update cache busting
- Commit the changes

## Agents in This Project

| Agent | Purpose |
|-------|---------|
| `version-changelog` | Version bump + CHANGELOG update |

Add more `.md` files to `.cursor/agents/` to define additional sub-agents.
