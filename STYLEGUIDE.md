# Style Guide

## Paleta de colores

- Primario: #0f172a (dark navy)
- Secundario: #7c3aed (purple)
- Acento: #06b6d4 (cyan)
- Fondo claro: #f8fafc
- Texto: #0f172a

## Tipografías

- Sistema: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial
- Tamaños: 14 / 16 / 20 / 24 / 32 (px)

## Componentes

- Header: usar `resources/views/components/branding.blade.php` con el logo en `public/logo.svg`.
- Tokens CSS: `resources/css/variables.css` para variables reutilizables.
- Tokens JSON: `design/tokens.json` para herramientas de diseño.

## Uso

- Importar `resources/css/variables.css` en `resources/css/app.css` o en tu pipeline de CSS.
- Mantener sincronizados `design/tokens.json` y `resources/css/variables.css`.
