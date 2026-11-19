/* Storefront interactions — no framework, no build step. */
(() => {
  const $ = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => [...r.querySelectorAll(s)];
  const csrf = $('meta[name="csrf-token"]').content;
  const scrim = $('[data-scrim]');

  /* ---------- Toast ---------- */
  const toastEl = $('[data-toast]');
  let toastTimer;
  const toast = (msg) => {
    toastEl.textContent = msg;
    toastEl.classList.add('is-on');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => toastEl.classList.remove('is-on'), 2800);
  };

  /* ---------- Panels (menu, search, cart, filters) ---------- */
  let openPanel = null;
  const open = (name) => {
    close();
    const p = $(`[data-panel="${name}"]`);
    if (!p) return;
    p.classList.add('is-open');
    scrim.classList.add('is-open');
    document.body.style.overflow = 'hidden';
    openPanel = p;
    if (name === 'search') setTimeout(() => $('input', p)?.focus(), 200);
    if (name === 'cart') loadCart();
  };
  const close = () => {
    if (!openPanel) return;
    openPanel.classList.remove('is-open');
    scrim.classList.remove('is-open');
    document.body.style.overflow = '';
    openPanel = null;
  };
  document.addEventListener('click', (e) => {
    const o = e.target.closest('[data-open]');
    if (o) { e.preventDefault(); open(o.dataset.open); return; }
    if (e.target.closest('[data-close]') || e.target === scrim) close();
  });
  document.addEventListener('keydown', (e) => e.key === 'Escape' && close());

  /* ---------- Header shadow ---------- */
  const header = $('[data-header]');
  const onScroll = () => header.classList.toggle('is-scrolled', scrollY > 8);
  addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  /* ---------- Cart ---------- */
  const setCount = (n) => $$('[data-cart-count]').forEach((el) => { el.textContent = n || ''; el.dataset.n = n; });
  const renderCart = (data) => {
    $('[data-cart-drawer]').innerHTML = data.html;
    setCount(data.count);
  };
  const loadCart = () => fetch(STORE.drawer, { headers: { Accept: 'application/json' } }).then((r) => r.json()).then(renderCart);

  // Any form with data-ajax-cart adds to the bag without a page reload.
  document.addEventListener('submit', async (e) => {
    const form = e.target.closest('form[data-ajax-cart]');
    if (!form || e.submitter?.name === 'buy_now') return;
    e.preventDefault();
    const btn = e.submitter || $('button[type="submit"], button:not([type])', form);
    btn?.setAttribute('disabled', '');
    try {
      const res = await fetch(form.action, { method: 'POST', body: new FormData(form), headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrf } });
      const data = await res.json();
      if (!res.ok) { toast(data.message || 'Something went wrong.'); return; }
      renderCart(data);
      open('cart');
    } catch { toast('Network error — please try again.'); }
    finally { btn?.removeAttribute('disabled'); }
  });

  // Quantity buttons in the drawer and on the cart page.
  document.addEventListener('click', async (e) => {
    const b = e.target.closest('[data-qty]');
    if (!b) return;
    e.preventDefault();
    const body = new FormData();
    body.append('key', b.dataset.key);
    body.append('qty', b.dataset.qty);
    const res = await fetch(STORE.update, { method: 'POST', body, headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrf } });
    if (!res.ok) return toast('Could not update your bag.');
    if (b.closest('[data-cart-page]')) return location.reload();
    renderCart(await res.json());
  });

  /* ---------- Wishlist (stored in this browser) ---------- */
  const WKEY = 'wishlist';
  const readWish = () => { try { return JSON.parse(localStorage.getItem(WKEY)) || []; } catch { return []; } };
  const writeWish = (ids) => { try { localStorage.setItem(WKEY, JSON.stringify(ids)); } catch {} };
  const paintWish = () => {
    const ids = readWish();
    $$('[data-wish]').forEach((b) => {
      const on = ids.includes(+b.dataset.wish);
      b.classList.toggle('is-on', on);
      b.setAttribute('aria-pressed', on);
    });
    $$('[data-wish-count]').forEach((c) => { c.textContent = ids.length || ''; c.dataset.n = ids.length; });
    $$('[data-wishlist-link]').forEach((a) => { a.href = `${STORE.wishlist}?ids=${ids.join(',')}`; });
  };
  document.addEventListener('click', (e) => {
    const b = e.target.closest('[data-wish]');
    if (!b) return;
    e.preventDefault();
    const id = +b.dataset.wish;
    let ids = readWish();
    const on = ids.includes(id);
    ids = on ? ids.filter((x) => x !== id) : [id, ...ids];
    writeWish(ids);
    paintWish();
    toast(on ? 'Removed from wishlist' : 'Saved to wishlist');
    if (on && b.closest('[data-wishlist-page]')) b.closest('.card')?.remove();
  });
  paintWish();
  // Wishlist page opened without ids → reload with the saved ones.
  const wp = $('[data-wishlist-page]');
  if (wp && wp.dataset.loaded === '0' && readWish().length) location.replace(`${STORE.wishlist}?ids=${readWish().join(',')}`);

  /* ---------- Reveal on scroll ---------- */
  if ('IntersectionObserver' in window) {
    const io = new IntersectionObserver((entries) => entries.forEach((en) => {
      if (en.isIntersecting) { en.target.classList.add('is-in'); io.unobserve(en.target); }
    }), { rootMargin: '0px 0px -8% 0px' });
    $$('.reveal').forEach((el) => io.observe(el));
  } else {
    $$('.reveal').forEach((el) => el.classList.add('is-in'));
  }

  /* ---------- Tabs ---------- */
  $$('[data-tabs]').forEach((root) => {
    const btns = $$('[data-tab]', root);
    btns.forEach((b) => b.addEventListener('click', () => {
      btns.forEach((x) => { x.classList.toggle('is-active', x === b); x.setAttribute('aria-selected', x === b); });
      $$('[data-tab-panel]', root).forEach((p) => p.classList.toggle('is-active', p.dataset.tabPanel === b.dataset.tab));
    }));
  });

  /* ---------- Product gallery ---------- */
  const stage = $('[data-stage]');
  if (stage) {
    const track = $('.stage-track', stage);
    const slides = $$('img', track);
    const thumbs = $$('[data-thumb]');
    const dots = $$('.dots i', stage);
    const go = (i) => track.scrollTo({ left: i * track.clientWidth, behavior: 'smooth' });
    thumbs.forEach((t, i) => t.addEventListener('click', () => go(i)));
    track.addEventListener('scroll', () => {
      const i = Math.round(track.scrollLeft / track.clientWidth);
      thumbs.forEach((t, k) => t.classList.toggle('is-active', k === i));
      dots.forEach((d, k) => d.classList.toggle('is-active', k === i));
    }, { passive: true });
    // Desktop: click to zoom, move to pan.
    if (matchMedia('(hover: hover)').matches) {
      stage.addEventListener('click', () => stage.classList.toggle('is-zoom'));
      stage.addEventListener('mousemove', (e) => {
        if (!stage.classList.contains('is-zoom')) return;
        const r = stage.getBoundingClientRect();
        slides.forEach((img) => { img.style.transformOrigin = `${((e.clientX - r.left) / r.width) * 100}% ${((e.clientY - r.top) / r.height) * 100}%`; });
      });
      stage.addEventListener('mouseleave', () => stage.classList.remove('is-zoom'));
    }
  }

  // Qty stepper on the product page.
  $$('[data-step]').forEach((b) => b.addEventListener('click', () => {
    const input = $(b.dataset.step);
    const max = +input.max || 20;
    input.value = Math.min(max, Math.max(1, (+input.value || 1) + (+b.dataset.d)));
  }));

  // Selected option labels ("Size: M").
  $$('[data-opt]').forEach((group) => group.addEventListener('change', (e) => {
    const out = $(`[data-opt-out="${group.dataset.opt}"]`);
    if (out) out.textContent = e.target.value;
  }));

  // Mobile sticky add-to-bag appears once the main button scrolls away.
  const sticky = $('[data-sticky-buy]');
  const mainBuy = $('[data-main-buy]');
  if (sticky && mainBuy && 'IntersectionObserver' in window) {
    document.body.classList.add('has-sticky');
    new IntersectionObserver(([en]) => sticky.classList.toggle('is-on', !en.isIntersecting && en.boundingClientRect.top < 0)).observe(mainBuy);
  }

  /* ---------- Shop filters: auto-submit sort ---------- */
  $$('[data-autosubmit]').forEach((el) => el.addEventListener('change', () => el.form.submit()));

  /* ---------- Hero slider: autoplay, arrows, dots, swipe, pause on hover ---------- */
  const hero = $('[data-hero]');
  if (hero) {
    // Text blocks and photos share a data-slide index and switch together.
    const parts = $$('[data-slide]', hero);
    const slides = [...new Set(parts.map((p) => p.dataset.slide))];
    const dots = $$('[data-go]', hero);
    const MS = 6500;
    hero.style.setProperty('--hero-ms', `${MS}ms`);
    let cur = 0, timer = null, paused = false;
    const show = (i) => {
      cur = (i + slides.length) % slides.length;
      parts.forEach((p) => {
        const on = +p.dataset.slide === cur;
        p.classList.toggle('is-active', on);
        if (p.classList.contains('hero-text')) p.setAttribute('aria-hidden', !on);
      });
      // Restart the progress-bar animation on the active dot.
      dots.forEach((d, k) => { d.classList.remove('is-active'); if (k === cur) { void d.offsetWidth; d.classList.add('is-active'); } });
      schedule();
    };
    const schedule = () => {
      clearTimeout(timer);
      if (slides.length > 1 && !paused && !matchMedia('(prefers-reduced-motion: reduce)').matches) timer = setTimeout(() => show(cur + 1), MS);
    };
    dots.forEach((d) => d.addEventListener('click', () => show(+d.dataset.go)));
    $('[data-next]', hero)?.addEventListener('click', () => show(cur + 1));
    $('[data-prev]', hero)?.addEventListener('click', () => show(cur - 1));
    const pause = (on) => { paused = on; hero.classList.toggle('is-paused', on); on ? clearTimeout(timer) : schedule(); };
    if (matchMedia('(hover: hover)').matches) {
      hero.addEventListener('mouseenter', () => pause(true));
      hero.addEventListener('mouseleave', () => pause(false));
    }
    let x0 = null;
    hero.addEventListener('touchstart', (e) => { x0 = e.touches[0].clientX; }, { passive: true });
    hero.addEventListener('touchend', (e) => {
      if (x0 === null) return;
      const dx = e.changedTouches[0].clientX - x0;
      if (Math.abs(dx) > 50) show(cur + (dx < 0 ? 1 : -1));
      x0 = null;
    });
    document.addEventListener('visibilitychange', () => (document.hidden ? clearTimeout(timer) : schedule()));
    schedule();
  }
})();
