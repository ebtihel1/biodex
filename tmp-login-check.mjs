export default async function run(page) {
    await page.locator('input[name="email"]').fill('sonia.chtioui@gmail.com');
    await page.locator('input[name="password"]').fill('password');

    const responsePromise = page.waitForResponse((response) =>
        response.request().method() === 'POST' && new URL(response.url()).pathname === '/login'
    );

    await Promise.all([
        responsePromise,
        page.getByRole('button', { name: 'Sign in' }).click(),
    ]);

    const response = await responsePromise;
    return {
        status: response.status(),
        location: response.headers()['location'] ?? null,
        finalUrl: page.url(),
        pageText: (await page.locator('body').innerText()).slice(0, 500),
    };
}
