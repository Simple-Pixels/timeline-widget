# Simple Pixels Timeline Widget

> **Reusable component** · An Elementor widget that renders a vertical, scroll-animated timeline of dated milestones.

| | |
| --- | --- |
| Type | Reusable component (any Elementor site) |
| Version | 1.0.0 (plugin header; asset versions are hard-coded separately, see [Gotchas](#gotchas)) |
| Main file | `simple-pixels-timeline-widget.php` |
| Text domain | `sp-tl` |
| Prefix | `sp_tl` / `SP_TL_` (PHP), `sp-timeline` (CSS, handles), `sp_timeline` (widget name) |
| Repository | `Simple-Pixels/timeline-widget` |

## What it does

- Adds a **Simple Pixels Timeline** widget to Elementor, in a **Simple Pixels Widgets** panel category.
- Editors add any number of items (date/year, title, description) with a repeater.
- Items alternate left and right of a central line. As the visitor scrolls, a coloured "progress" line draws down the spine, each item's dot grows and changes colour, and the item's text fades and slides in.
- Colours (dot, progress line, base spine) and typography (year, title, description) are set in the widget's **Style** tab.
- On tablet and mobile the layout collapses to a single column with the line on the left.

## Requirements

| Requirement | Version / notes |
| --- | --- |
| WordPress | No minimum declared. Tested on 6.x. |
| PHP | No minimum declared. Uses the `??` operator, so 7.0+. |
| Required plugins | **Elementor** (free is enough). Without it the plugin does nothing. |
| External services | **GSAP 3.12.5** and **ScrollTrigger**, loaded from `cdnjs.cloudflare.com`. |

## Installation

1. Deploy the repository root to `wp-content/plugins/timeline-widget/` (the folder name doesn't matter).
2. Activate **Simple Pixels Timeline Widget**.
3. In Elementor, search the widget panel for "Timeline" or open the **Simple Pixels Widgets** category.

## Configuration

None. There's no settings page. Items, colours and typography are set per widget in Elementor (see Usage).

## Usage

Drag the **Simple Pixels Timeline** widget onto a page.

**Content tab → Timeline Items** (repeater)

| Field | Control | Default |
| --- | --- | --- |
| Date/Year | Text | `2025` |
| Title | Text | `Milestone Title` |
| Description | Textarea (plain text, HTML is escaped) | Short placeholder text |

New widgets start with two example items (2006 "Founded", 2010 "Diversifies"). Replace them.

**Style tab → Styles**

| Control | Default | CSS variable it sets |
| --- | --- | --- |
| Circle (Ball) Color | `#006CFF` | `--sp-circle` |
| Progress Line Color | `#006CFF` | `--sp-line` |
| Default Spine Color | `#bdc3c7` | `--sp-spine` |
| Year / Title / Description Typography | Theme default | Elementor typography group controls |

## How it works

### Data model

None. Each widget's items and colours are stored in Elementor's page data (`_elementor_data`), like any other widget. No options, post types or tables.

### Request flow

1. `wp_enqueue_scripts` **registers** (does not enqueue) GSAP, ScrollTrigger, the stylesheet and `sp-timeline-init.js`. The widget declares them through `get_script_depends()` / `get_style_depends()`, so Elementor only loads them on pages that use the widget.
2. `render()` outputs a `.sp-timeline-wrap` with the three colours as inline CSS variables, then a `<ul>` of items. Even-indexed items get class `left`, odd get `right`.
3. Each widget instance pushes `{ selector: '#sp-timeline-<id>' }` onto `window.SP_TIMELINES` through `wp_add_inline_script()`.

### Endpoints

None.

### Front end

- `sp-timeline-init.js` reads `window.SP_TIMELINES` on `document.ready`, and again on Elementor's `frontend/element_ready/sp_timeline.default` hook (editor preview).
- For each timeline it creates a scrubbed ScrollTrigger that scales `.draw-line` from 0 to 1 between the top of the timeline and the last item. Per item, one trigger scales the `.circle` to 3x in `--sp-circle`, and another fades/slides `.content` and `.year` in. Both reverse when scrolling back up.
- Below 1024px the line moves to the left and items stack in one column.

## File structure

```
simple-pixels-timeline-widget.php      Bootstrap: header, constants, asset registration, widget + category registration
includes/
  class-sp-timeline-widget.php         SP_Timeline_Widget (Elementor\Widget_Base): controls and render()
assets/
  sp-timeline.css                      Layout, line/dot styles, responsive breakpoints (1620, 1400, 1024, 767px)
  sp-timeline-init.js                  GSAP / ScrollTrigger animation, reads window.SP_TIMELINES
```

## Hooks

The plugin defines no custom hooks. It uses:

| Hook | Purpose |
| --- | --- |
| `elementor/widgets/register` | Registers `SP_Timeline_Widget`. |
| `elementor/elements/categories_registered` | Adds the `simple-pixels-category` panel category. Other Simple Pixels widgets can reuse this slug. |
| `frontend/element_ready/sp_timeline.default` (JS) | Re-runs the animation setup in the Elementor editor. |

## Gotchas

- **Generic script handles.** GSAP is registered as `gsap` and `gsap-scrolltrigger`. If a theme or another plugin registers the same handle first (e.g. a different GSAP version), WordPress keeps the first one and this widget uses it.
- **CDN dependency.** GSAP loads from cdnjs. If the CDN is blocked (strict CSP, some ad blockers, offline dev), the init script returns early and items stay hidden or static. Consider bundling GSAP locally if that becomes a problem.
- **Asset versions are hard-coded** to `1.0.0` in `wp_register_style/script`, separate from the plugin header. Bump them when you change CSS or JS, or browsers and page caches keep serving the old files.
- **Global CSS variables.** `sp-timeline.css` sets `--spine-gap`, `--content-width-lg` and `--mobile-left` on `:root`, so they apply site-wide and could clash with theme variables of the same name.
- **Invalid HTML.** The two line `<span>`s sit directly inside the `<ul>`. Browsers tolerate it, but HTML validators flag it.
- **No reduced-motion handling.** Animations run regardless of `prefers-reduced-motion`.
- **Description is plain text.** It's escaped with `esc_html()`, so links and formatting typed into it show as text.
- **Not translation-ready in practice.** Strings use the `sp-tl` text domain but nothing calls `load_plugin_textdomain()`.
- The widget class uses short array syntax and spaces, not the WordPress Coding Standards in [CLAUDE.md](CLAUDE.md). Match the existing file for small changes.

## Development

- Coding standards, git workflow and environment notes: [CLAUDE.md](CLAUDE.md).
- Versioning: bump the plugin header **and** the asset version strings in `simple-pixels-timeline-widget.php` together.
- Testing: add the widget to a test page with 4+ items, check it in the Elementor editor preview, on desktop, and below 1024px and 767px. Scroll down and back up to check the animations reverse.

## Changelog

| Version | Notes |
| --- | --- |
| 1.0.0 | Initial release. |
