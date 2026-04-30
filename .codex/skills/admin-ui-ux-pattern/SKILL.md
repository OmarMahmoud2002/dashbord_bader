---
name: admin-ui-ux-pattern
description: Apply this project's admin products-dashboard UI/UX pattern to CodeIgniter admin views. Use when styling admin pages, tables, search/action bars, Bootstrap Icons, forms, popups, shelves, employees, admins, low-stock products, product add/edit pages, or related admin UI without changing backend logic.
---

# Admin UI UX Pattern

## Purpose

Use the visual language of `admin/dashboard` as the reference for admin pages. Keep controller/model logic, form actions, field names, AJAX endpoints, and route behavior unchanged; limit edits to markup classes, icons, CSS, and presentational JS needed for popups.

## Reference Pattern

- Use `public/css/dashboard.css` and `application/views/view_admin_dashboard.php` as the visual source.
- Use `public/css/admin-ui-pattern.css` for the extracted reusable layer.
- Use Bootstrap Icons already loaded by `view_header.php`.
- Prefer light surfaces, soft shadows, 12-16px radii, and the primary gradient `#667eea -> #764ba2`.
- Keep operational pages dense and scannable: search/actions at top, table/list card below, modals centered and clear.

## Page Structure

1. Add the stylesheet after the sidebar/header includes:

```php
<link rel="stylesheet" href="<?= base_url('public/css/admin-ui-pattern.css') ?>">
```

2. Add a contextual class to the page root:

```html
<div class="app-content admin-ui-page admin-ui-shelves">
```

3. Use these page modifiers when relevant:

- `admin-ui-low` for low-stock products.
- `admin-ui-shelves` for shelves and shelf product pages.
- `admin-ui-people` for employees and employee details.
- `admin-ui-admins` for admins.
- `admin-ui-product-form` and `admin-ui-form-shell` for product add/edit form pages.

## Tables And Lists

- Preserve the existing `.products-area-wrapper.tableView`, `.products-header`, `.products-row`, `.product-cell`, `.cell-label`, and `.actions` structure.
- Put search inputs inside `.app-content-actions`; keep `id="Search"` and `onkeyup="search()"` when used.
- Add icons through CSS pseudo-elements or Bootstrap Icon `<i>` tags only when that does not change data or event targets.
- Empty states should use `.empty-admin-state` with a Bootstrap Icon.

## Buttons

- Keep original button classes and JS hooks such as `.app-content-headerButton`, `.orderShelf`, `.del-btn`, `.edit-btn`, `.show-btn`, `.add-ohda`.
- Add icons with CSS pseudo-elements when possible so click handlers still receive the same button/link.
- Use destructive styling only for delete/remove actions.

## Popups

- Keep existing `.popup` and `.popup-content` structure.
- Keep close controls with `.close`.
- Keep form actions, hidden inputs, names, and submit buttons unchanged.
- For delete confirmations, prefer showing an existing popup instead of browser `confirm()` only when the same endpoint and submitted values are preserved.

## Guardrails

- Do not edit models, controllers, database queries, business calculations, permissions, or redirects for UI-only tasks.
- Do not rename inputs or IDs consumed by JavaScript.
- Do not remove existing script includes unless a duplicate causes a visible UI bug.
- After edits, run `php -l` on changed PHP views and inspect the git diff for accidental backend changes.
