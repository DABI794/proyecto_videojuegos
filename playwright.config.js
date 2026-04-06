import { defineConfig } from '@playwright/test';

export default defineConfig({
  testDir: './tests/e2e',
  timeout: 120000,

  // Graba video y screenshots automáticamente cuando falla
  use: {
    baseURL: 'http://localhost:8000',
    screenshot: 'only-on-failure',
    video: 'retain-on-failure',
    headless: false, // false = ves el navegador en tiempo real
    locale: 'es-ES',
  },

  // Carpeta donde se guardan evidencias
  outputDir: 'tests/e2e/evidencias/',

  reporter: [
    ['html', { outputFolder: 'tests/e2e/reporte' }],
    ['list'],
  ],
});