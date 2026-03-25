# Guía de Estilo

## Paleta de colores

- Primario: #0f172a (azul oscuro)
- Secundario: #7c3aed (púrpura)
- Acento: #06b6d4 (cian)
- Fondo claro: #f8fafc
- Texto: #0f172a

## Tipografías

- Familia: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial
- Tamaños recomendados: 14 / 16 / 20 / 24 / 32 (px)

## Componentes

- Encabezado: usar `resources/views/components/branding.blade.php` con el logo en `public/logo.svg`.
- Tokens CSS: `resources/css/variables.css` contiene variables reutilizables (colores, espaciado, tipografía).
- Tokens JSON: `design/tokens.json` para sincronizar con herramientas de diseño y/o build tools.

## Uso

- Importar `resources/css/variables.css` en `resources/css/app.css` o en tu pipeline de CSS/Tailwind.
- Mantener `design/tokens.json` sincronizado con `resources/css/variables.css` para coherencia visual.

## Notas

- Mantén las reglas de accesibilidad (contraste mínimo AA) y usa las variables para cambiar temas rápidamente.
- Actualiza esta guía cuando cambies paleta o tipografías.