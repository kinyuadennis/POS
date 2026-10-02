# Vawa page design

Use this file when restyling a screen to match **Users** in User management. Copy the structure; do not invent a new layout.

Canonical examples:

- List: `resources/views/manage_user/index.blade.php`
- List + collapsible filters: `resources/views/sell_return/index.blade.php`
- List + contacts filter icon: `resources/views/contact/index.blade.php`
- List + always-visible filters: `resources/views/report/discounts_allowed.blade.php`
- Form (header + tint only): `resources/views/sell_return/add.blade.php`

Keep existing page behaviour (queries, permissions, columns, actions). Restyle the shell around it.

---

## 1. Theme colour

Every restyled page resolves the business theme from system settings. Never hardcode a brand colour for primary actions.

```php
@php
    $theme_color = session('business.theme_color', 'blue');
    $color_map = [
        'blue' => '#3c8dbc',
        'purple' => '#605ca8',
        'green' => '#00a65a',
        'red' => '#dd4b39',
        'yellow' => '#f39c12',
        'orange' => '#ff851b',
        'sky' => '#00c0ef',
        'primary' => '#3c8dbc'
    ];
    $primary_hex = isset($color_map[$theme_color]) ? $color_map[$theme_color] : '#3c8dbc';
@endphp
```

| Token | Value |
| --- | --- |
| Primary fill | `{{ $primary_hex }}` |
| Page background tint | `{{ $primary_hex }}0d` (about 5% opacity) |
| Card border | `#e2e8f0` |
| Input border | `#cbd5e1` |
| Heading | `#000` / `#1e293b` |
| Body / labels | `#475569` |
| Muted icon | `#94a3b8` |
| Card radius | `12px` |
| Control radius | `8px` |
| Pill add button | `20px` / full round |
| Search / primary button height | `42px` |
| Export button height | `35px` |
| Card shadow | `0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03)` |

Primary buttons (Add, Filters): theme fill, white text, hover `opacity: 0.9` and `translateY(-1px)`. Active Filters: same fill plus `box-shadow: 0 0 0 3px {{ $primary_hex }}40`.

---

## 2. Page shell

Do **not** use AdminLTE `components.filters` / `components.widget` / `box box-primary` for restyled list pages.

```
content-header          white bar, title + one-line subtitle
content.{page}-content  theme-tinted page body, padding 24px
  search-section        white card: search, actions, optional filters
  datatable-section     white card: table heading + DataTable
```

### Header

```blade
<section class="content-header tw-flex tw-justify-between tw-items-center">
    <div>
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black" style="margin: 0;">Page title</h1>
        <p class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold tw-m-0">Short subtitle</p>
    </div>
</section>
```

Header CSS: white background, `padding: 16px 24px`, `border-bottom: 1px solid #cbd5e160`, light shadow.

### Tinted body

```css
.{page}-content {
    background-color: {{ $primary_hex }}0d !important;
    padding: 24px !important;
    min-height: calc(100vh - 100px);
    overflow: visible;
}
.search-section,
.datatable-section {
    background-color: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 12px !important;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03) !important;
}
.search-section { padding: 20px !important; margin-bottom: 20px !important; }
.datatable-section { padding: 24px !important; }
```

Give `.search-section` `position: relative; z-index: 25; overflow: visible` when it contains date pickers or select2.

### Form / detail pages

Same header + tinted body. Put the form in a white card (`.datatable-section` or a `box-solid` inside the tint). No search row unless the page is a list.

---

## 3. Search card (list pages)

Order, top to bottom:

1. **Search** (flex grow) + optional **Filters** button + optional **Add** button (same 42px row)
2. **Collapsible filters** (hidden until Filters is clicked), **above** Show / Export
3. **Show entries** + **Export**

If the page has no extra filters, skip the Filters button and panel (Users). If it has no create action, skip Add (Sale Return List).

### Search input

Left magnifying-glass SVG at `left: 14px`. Input: `padding-left: 42px`, height `42px`, radius `8px`, border `#cbd5e1`, inset shadow. Placeholder names the fields that search actually hits.

Bind: `$('#custom_{page}_search').on('keyup', function () { table.search(this.value).draw(); });`

### Filters button

To the **right of search**. Label `@lang('report.filters')` plus funnel icon. Theme-coloured fill. Toggles a panel with `slideToggle(300)` and class `filter-active`. After open, force `$panel.find('.select2-container').css('width', '100%')`.

Keep filter values when the panel closes. Do not reset on toggle.

### Add button

Pill, theme fill, white text, plus icon, height `42px`. Only if the user can create that resource.

### Filter fields

Location → Date range → other fields (customer, cashier/user) unless the page already has a required order. Prefer defaulting location to **Vawa** when that is the operational default on similar reports.

Filter labels: 13px, weight 500, colour `#475569`. Controls height 42px, radius 8px.

On `md+`, filters sit in one row (`flex`, `flex: 1 1 180px`). On mobile, stack.

### Show entries + Export

Show: 25 (default), 50, 100, 250, All.

Export: CSV and Excel. Hide native DataTables length, buttons, and filter:

```css
#{table}_wrapper > .row:first-child,
#{table}_wrapper .dataTables_length,
#{table}_wrapper .dt-buttons,
#{table}_wrapper .dataTables_filter {
    display: none !important;
}
```

Wire custom length and export:

```javascript
$('#custom_{page}_length').on('change', function () {
    table.page.len($(this).val()).draw();
});
$(document).on('click', '.{page}-export-opt', function (e) {
    e.preventDefault();
    table.button('.buttons-' + $(this).data('format')).trigger();
});
```

DataTable: `dom: 'lBrtip'`, `pageLength: 25`, `buttons` for csv/excel (exclude Action column from export).

---

## 4. Table card

Heading: **All {things}**, 18-ish / `tw-text-lg`, weight 700, colour `#1e293b`, margin-bottom 20px.

Skeleton: 5 rows of `.skeleton-block` in `<tbody>` before DataTables loads. Remove in `initComplete`. Hide `.dataTables_processing`.

Cells: `white-space: nowrap`, `vertical-align: middle`. Wrap the table in `.table-responsive`. Use `scrollX: true` when columns overflow.

### Footer totals + scrollX (required)

`scrollX` clones thead/tfoot. `$('#footer_id')` updates the **hidden** footer, so the visible Total shows **0**.

Always:

```javascript
fnDrawCallback: function () {
    var api = this.api();
    var $table = $(api.table().node());
    var $wrapper = $(api.table().container());
    var total = sum_table_col($table, 'final_total');
    $wrapper.find('.footer_total, [id="footer_total"]').text(total);
    __currency_convert_recursively($wrapper);
}
```

Put **classes** on footer total cells as well as IDs. Sum from `api.table().node()`, not `$('#the_table')`.

---

## 5. Date range picker

`#scrollable-container` clips absolute pickers. Do not leave the picker inside an overflowing parent.

```javascript
var settings = $.extend(true, {}, dateRangeSettings, {
    alwaysShowCalendars: true,
    parentEl: 'body',
    drops: 'down'
});
```

Keep Apply / Clear visible (`alwaysShowCalendars: true`, sticky `.drp-buttons { display: block !important; }`). Close the picker on apply / cancel / hide. Do **not** set `display: flex !important` on `.daterangepicker` — that blocks jQuery `hide()`.

On small screens, treat the picker as a `position: fixed` overlay and lock `#scrollable-container` overflow while it is open (see Discounts Allowed / home).

---

## 6. Mobile

At `max-width: 767px`: header padding `12px 16px`, content/cards padding `12px` / `14px`. Table wrapper `overflow-x: auto`. Do not shrink nowrap columns until they wrap.

---

## 7. Assets and deploy

- Changing `public/js/*.js`: bump `asset_version` in `config/constants.php` and load scripts as `asset('js/file.js?v=' . $asset_v)`.
- Blade-only CSS/JS in the view: no bump needed.
- After production pull: `php artisan optimize:clear && php artisan optimize`.
- Do not change report formulas unless asked.

---

## 8. Restyle checklist

1. Resolve `$primary_hex` from `session('business.theme_color')`.
2. White header (title + subtitle) + tinted body.
3. White `search-section` then white `datatable-section`.
4. Search row: search, Filters (if any), Add (if any).
5. Filters panel above Show entries / Export; hidden until toggled.
6. Hide native DataTables length/buttons/filter; custom search, length 25, CSV/Excel.
7. Skeleton rows; nowrap cells; footer totals via wrapper + classes if `scrollX`.
8. Date pickers: `parentEl: 'body'`, calendars always on, Apply/Clear visible, close after apply.
9. Primary buttons use theme colour, not grey/white.
10. Preserve permissions, routes, and existing data behaviour.
