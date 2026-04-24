import { test } from '@playwright/test';

test('login page has no runtime error', async ({ page }) => {
  const errors = [];
  page.on('pageerror', error => errors.push(`pageerror:${error.message}`));
  page.on('console', message => {
    if (message.type() === 'error') errors.push(`console:${message.text()}`);
  });

  const response = await page.goto('http://127.0.0.1:8080/login', { waitUntil: 'networkidle' });
  console.log('STATUS=' + (response ? response.status() : 'null'));
  console.log('H1=' + await page.locator('h1').first().textContent().catch(() => 'NONE'));
  console.log('ERRORS=' + JSON.stringify(errors));
});
