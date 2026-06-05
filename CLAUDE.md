# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

WP Newsletter Builder is a WordPress plugin that provides an interface to manage and build email newsletters. It creates Custom Post Types (`nb_newsletter` and `nb_template`) with full Gutenberg block editor integration.

- **PHP:** 8.1+
- **Node:** 16-20
- **WordPress:** 6.2+

## Common Commands

### Development
```bash
npm run start          # Dev server (hot reload disabled)
npm run start:hot      # Dev server with hot reload
npm run build          # Production build
```

### Linting & Type Checking
```bash
npm run lint           # ESLint + TypeScript check
npm run lint:fix       # ESLint with auto-fix
npm run stylelint      # SCSS linting
npm run stylelint:fix  # SCSS auto-fix
composer phpcs         # PHP coding standards
composer phpcbf        # PHP auto-fix
composer phpstan       # PHP static analysis (level: max)
```

### Testing
```bash
npm run test                    # Runs all tests (TypeScript, ESLint, Stylelint)
npm run check-types             # TypeScript tests
npm run eslint                  # ESLint tests
npm run stylelint               # Stylelint tests
composer phpunit                # PHPUnit tests
composer test                   # Full PHP suite: phpcs + phpunit + phpstan

### Block Scaffolding
```bash
npm run create-block   # Scaffold new block (prompts for TS/JS, static/dynamic)
```

## Architecture

### PHP (`src/`)
- **`class-wp-newsletter-builder.php`**: Main plugin class, registers CPTs and hooks
- **`email-providers/`**: Email service integrations (SendGrid, Campaign Monitor)
- **`class-rest-api-*.php`**: REST API endpoints, fields, and query modifications
- **`class-settings.php`**: Settings page using Fieldmanager
- **`assets.php`**: Script/style enqueueing
- **`meta.php`**: Post meta registration
- **`utils.php`**: Utility functions

### Blocks (`blocks/`)
20+ custom Gutenberg blocks for newsletter building:
- **Layout**: header, footer, section, divider
- **Content**: post, post-title, post-excerpt, post-featured-image, post-byline
- **UI**: button, heading, list, paragraph, signup-form

Each block follows the structure: `block.json`, `index.ts`, `edit.jsx`, `save.jsx`, `render.php`

### Block Filters (`block-filters/`)
Extends core WordPress blocks (button, heading, image, list, paragraph, separator, latest-posts)

### Plugins (`plugins/`)
Editor enhancements: newsletter-from-post, newsletter-status, newsletter-template-styles, pre-publish-checks

### Hooks (`hooks/`)
Custom React hooks: `useEmailLists`, `useNewsletterMeta`, `useNewsletterStats`

### Entries (`entries/`)
Entry points compiled by webpack. Include `index.php` in an entry directory for auto-loading via `load_scripts()`.

## Key Patterns

### PHP
- Namespace: `WP_Newsletter_Builder`
- Singleton pattern via `Trait_Instance`
- Hooks registered in `__construct()`
- PSR-4 autoloading via Composer

### JavaScript/TypeScript
- Full TypeScript with strict mode
- Block registration via `block.json`
- Dynamic blocks use `render.php` for server-side rendering

### Important Filters
- `wp_newsletter_builder_selected_provider`: Select email provider class
- `wp_newsletter_builder_allowed_post_types`: Filter post types in post picker
- `wp_newsletter_builder_html_url`: Modify newsletter HTML URLs

## Build System

Uses `@wordpress/scripts` with custom webpack config that:
- Discovers blocks from `blocks/` directory automatically
- Copies PHP files with `--webpack-copy-php` flag
- Generates `*.asset.php` files for WordPress dependency management
