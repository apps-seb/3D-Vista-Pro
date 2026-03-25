import asyncio
from playwright.async_api import async_playwright
import os

async def main():
    async with async_playwright() as p:
        # Use a mobile device profile
        pixel_5 = p.devices['Pixel 5']
        browser = await p.chromium.launch(headless=True)
        context = await browser.new_context(**pixel_5)
        page = await context.new_page()

        # Get absolute path to index.html
        abs_path = os.path.abspath('index.html')
        file_url = f"file://{abs_path}"

        await page.goto(file_url)
        # Wait for the viewer to load
        await page.wait_for_timeout(3000)

        # Ensure the output directory exists
        os.makedirs('/home/jules/verification', exist_ok=True)

        # Click the sphere view button
        await page.click("button:has-text('Vista Esfera')")
        await page.wait_for_timeout(2000)

        # Take a screenshot
        screenshot_path = '/home/jules/verification/index_fisheye.png'
        await page.screenshot(path=screenshot_path, full_page=True)

        print(f"Screenshot saved to {screenshot_path}")

        await browser.close()

asyncio.run(main())
