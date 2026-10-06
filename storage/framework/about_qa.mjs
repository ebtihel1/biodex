export default async function run(page, ui) {
  await page.waitForSelector('#ai-lab', { timeout: 15000 })
  await page.waitForTimeout(600)

  const geo = await page.evaluate(() => {
    const shell = document.querySelector('.ab-shell')
    const hero = document.querySelector('.ab-hero')
    const r = shell?.getBoundingClientRect()
    const hr = hero?.getBoundingClientRect()
    return {
      vw: window.innerWidth,
      shellW: r ? Math.round(r.width) : null,
      shellLeft: r ? Math.round(r.left) : null,
      shellTop: r ? Math.round(r.top) : null,
      heroBottom: hr ? Math.round(hr.bottom) : null,
      heroTop: hr ? Math.round(hr.top) : null,
      docW: document.documentElement.scrollWidth,
      bodyW: document.body.scrollWidth,
      sections: document.querySelectorAll('.ab-sec').length,
      dataCount: document.querySelectorAll('[data-count]').length,
      navbars: document.querySelectorAll('#navbarNav').length,
      title: document.title,
      styles: document.querySelectorAll('style').length,
    }
  })

  const status = await page
    .waitForFunction(() => /online|offline|unavailable/i.test(document.querySelector('#aiStatus')?.textContent || ''), { timeout: 20000 })
    .then(() => page.$eval('#aiStatus', (e) => e.textContent.trim()))
    .catch((e) => 'timeout: ' + e.message)

  // Tab 1 — classify
  await page.fill('#clsDescription', 'An empty PET water bottle with an aluminium cap, rinsed')
  await page.fill('#clsType', 'plastic')
  await page.fill('#clsWeight', '3')
  await page.click('#aiClassify .ai-run')
  await page.waitForFunction(
    () => {
      const o = document.querySelector('#aiOut')
      return o && !o.hidden && /Confidence/i.test(o.textContent)
    },
    { timeout: 40000 }
  )
  const classify = await page.$eval('#aiOut', (e) => e.innerText.replace(/\n+/g, ' | ').slice(0, 700))

  // Tab 2 — price
  await page.click('.ai-tab[data-tab="price"]')
  await page.waitForTimeout(300)
  const priceVisible = await page.$eval('#aiPrice', (e) => getComputedStyle(e).display !== 'none')
  await page.fill('#prName', 'Recycled PET flakes')
  await page.selectOption('#prCategory', 'plastic')
  await page.click('#aiPrice .ai-run')
  await page.waitForFunction(
    () => {
      const o = document.querySelector('#aiOut')
      return o && !o.hidden && /DT/.test(o.textContent)
    },
    { timeout: 40000 }
  )
  const price = await page.$eval('#aiOut', (e) => e.innerText.replace(/\n+/g, ' | ').slice(0, 700))

  // Tab 3 — description
  await page.click('.ai-tab[data-tab="write"]')
  await page.waitForTimeout(300)
  await page.fill('#wrMaterial', 'recycled wood')
  await page.fill('#wrMethod', 'sanding and assembly')
  await page.click('#aiWrite .ai-run')
  await page.waitForFunction(
    () => {
      const o = document.querySelector('#aiOut')
      return o && !o.hidden && o.innerText.trim().length > 40 && !/Describe|failed/i.test(o.innerText)
    },
    { timeout: 40000 }
  )
  const desc = await page.$eval('#aiOut', (e) => e.innerText.replace(/\n+/g, ' | ').slice(0, 700))

  const state = await page.evaluate(() => ({
    buttons: Array.from(document.querySelectorAll('.ai-run')).map((b) => b.innerText.trim() + '|' + (b.disabled ? 'disabled' : 'enabled')),
    outHidden: document.querySelector('#aiOut').hidden,
    activePanel: document.querySelector('.ai-form.is-active')?.id,
    overflow: document.documentElement.scrollWidth - window.innerWidth,
  }))

  return { geo, status, priceVisible, classify, price, desc, state }
}
