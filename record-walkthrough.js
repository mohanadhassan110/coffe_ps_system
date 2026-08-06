import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';

async function run() {
    console.log('Starting E2E System Test Video Recording...');
    const recordingDir = path.resolve('./temp_recordings');
    if (!fs.existsSync(recordingDir)) {
        fs.mkdirSync(recordingDir, { recursive: true });
    }

    const browser = await chromium.launch({
        headless: true
    });

    const context = await browser.newContext({
        viewport: { width: 1280, height: 800 },
        recordVideo: {
            dir: recordingDir,
            size: { width: 1280, height: 800 }
        }
    });

    const page = await context.newPage();
    const delay = (ms) => new Promise(res => setTimeout(res, ms));

    try {
        // Step 1: Login
        console.log('1. Navigating to Login page...');
        await page.goto('http://127.0.0.1:8000/login', { waitUntil: 'networkidle' });
        await delay(1500);

        await page.fill('input[name="username"]', 'admin');
        await delay(500);
        await page.fill('input[name="password"]', 'admin123');
        await delay(500);
        await page.click('button[type="submit"]');
        await page.waitForLoadState('networkidle');
        await delay(2000);

        // Step 2: Navigate to POS - PlayStation Section
        console.log('2. Opening Single-Page POS - PlayStation Section...');
        await page.goto('http://127.0.0.1:8000/pos?tab=devices', { waitUntil: 'networkidle' });
        await delay(2500);

        // Click on available PS device (e.g. PS5 - 01)
        const availableDeviceCard = page.locator('.pos-device-card.available').first();
        if (await availableDeviceCard.count() > 0) {
            console.log('Starting open session on PlayStation device...');
            await availableDeviceCard.click();
            await delay(1000);
            await page.fill('#startSessionModal input[name="client_name"]', 'عميل بلايستيشن - أحمد');
            await delay(800);
            await page.click('#startSessionModal button[type="submit"]');
            await page.waitForLoadState('networkidle');
            await delay(3000); // Verify live timer running
        }

        // Step 3: Café Tables Section
        console.log('3. Switching to Café Tables Section...');
        await page.goto('http://127.0.0.1:8000/pos?tab=cafe', { waitUntil: 'networkidle' });
        await delay(2000);

        // Create new table order
        const openTableBtn = page.locator('text=طلب طاولة جديد');
        if (await openTableBtn.count() > 0) {
            await openTableBtn.click();
            await delay(1000);
            await page.fill('#newCafeOrderModal input[name="table_number"]', 'طاولة 1');
            await delay(500);
            await page.fill('#newCafeOrderModal input[name="client_name"]', 'عميل طاولة - الكافيه');
            await delay(500);
            await page.click('#newCafeOrderModal button[type="submit"]');
            await page.waitForLoadState('networkidle');
            await delay(2000);
        }

        // Add products to the selected table bill
        console.log('Adding product grid cards to table bill...');
        const productCards = page.locator('.pos-prod-card:not(.disabled)');
        const prodCount = await productCards.count();
        for (let i = 0; i < Math.min(3, prodCount); i++) {
            await productCards.nth(i).click();
            await page.waitForLoadState('networkidle');
            await delay(1000);
        }
        await delay(2500); // Verify totals & stock update

        // Step 4: Takeaway Section
        console.log('4. Creating Quick Takeaway Order...');
        const openTakeawayBtn = page.locator('text=تيك أواي جديد');
        if (await openTakeawayBtn.count() > 0) {
            await openTakeawayBtn.click();
            await delay(1000);
            await page.fill('#newCafeOrderModal input[name="client_name"]', 'طلب سفري - سريع');
            await delay(500);
            await page.click('#newCafeOrderModal button[type="submit"]');
            await page.waitForLoadState('networkidle');
            await delay(2000);
        }

        // Add item to takeaway
        if (await productCards.count() > 0) {
            await productCards.first().click();
            await page.waitForLoadState('networkidle');
            await delay(1500);
        }

        // Step 5: Checkout & Process Invoice
        console.log('5. Completing Payment & Invoice...');
        const checkoutBtn = page.locator('.pos-btn-checkout.primary');
        if (await checkoutBtn.count() > 0) {
            await checkoutBtn.click();
            await page.waitForLoadState('networkidle');
            await delay(2500);

            // Complete checkout if checkout form exists
            const payBtn = page.locator('button[type="submit"]:has-text("تحصيل"), button[type="submit"]:has-text("تأكيد"), .btn-success');
            if (await payBtn.count() > 0) {
                await payBtn.first().click();
                await page.waitForLoadState('networkidle');
                await delay(2500);
            }
        }

        // Step 6: Reports & Light-Theme Analytics Dashboard
        console.log('6. Showcasing Analytics & Reports Dashboard...');
        await page.goto('http://127.0.0.1:8000/', { waitUntil: 'networkidle' });
        await delay(3000);

        await page.goto('http://127.0.0.1:8000/reports/daily', { waitUntil: 'networkidle' });
        await delay(4000);

        console.log('Walkthrough scenario completed successfully!');
    } catch (err) {
        console.error('Error during test execution:', err);
    } finally {
        const video = page.video();
        await page.close();
        await context.close();
        await browser.close();

        if (video) {
            const videoPath = await video.path();
            const destPath = path.resolve('./system-walkthrough-demo.mp4');
            console.log(`Video recorded to ${videoPath}`);
            fs.copyFileSync(videoPath, destPath);
            console.log(`Saved walkthrough demo video to ${destPath}`);

            // Cleanup temp dir
            try {
                fs.rmSync(recordingDir, { recursive: true, force: true });
            } catch (e) {}
        }
    }
}

run();
