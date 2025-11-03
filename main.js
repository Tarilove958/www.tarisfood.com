document.addEventListener('DOMContentLoaded', () => {
  const toggleBtn = document.querySelector('.nav-toggle');
  const nav = document.querySelector('[data-nav]');
  if (toggleBtn && nav) {
    toggleBtn.addEventListener('click', () => {
      const isOpen = nav.classList.toggle('open');
      toggleBtn.setAttribute('aria-expanded', String(isOpen));
    });
    nav.addEventListener('click', (e) => {
      const t = e.target;
      if (t && t.tagName === 'A' && nav.classList.contains('open')) {
        nav.classList.remove('open');
        toggleBtn.setAttribute('aria-expanded', 'false');
      }
    });
  }

  const yearEl = document.getElementById('year');
  if (yearEl) yearEl.textContent = new Date().getFullYear();

  const current = location.pathname.split('/').pop() || 'index.html';
  document.querySelectorAll('.site-nav a').forEach((a) => {
    a.classList.toggle('active', a.getAttribute('href') === current);
  });

  const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  document.body.addEventListener('click', (e) => {
    const a = e.target.closest('a[href^="#"]');
    if (!a) return;
    const hash = a.getAttribute('href');
    if (!hash || hash === '#') return;
    const el = document.querySelector(hash);
    if (!el) return;
    e.preventDefault();
    el.scrollIntoView(prefersReduced ? undefined : { behavior: 'smooth', block: 'start' });
    history.pushState(null, '', hash);
  });

  document.querySelectorAll('img').forEach((img) => {
    if (!img.hasAttribute('loading')) img.setAttribute('loading', 'lazy');
    if (!img.hasAttribute('decoding')) img.setAttribute('decoding', 'async');
    try { img.referrerPolicy = 'no-referrer'; } catch (_) {}
    // Give the browser a hint for responsive layout
    if (!img.hasAttribute('sizes')) {
      const inCardsGrid = img.closest('.cards-grid');
      img.setAttribute('sizes', inCardsGrid ? '(min-width: 861px) 33vw, 86vw' : '100vw');
    }
    // After load, set intrinsic dimensions to reduce layout shift
    const onLoad = () => {
      if (!img.getAttribute('width')) img.setAttribute('width', String(img.naturalWidth || ''));
      if (!img.getAttribute('height')) img.setAttribute('height', String(img.naturalHeight || ''));
      img.removeEventListener('load', onLoad);
    };
    img.addEventListener('load', onLoad, { once: true });
    const onErr = () => {
      img.removeEventListener('error', onErr);
      const altSeed = encodeURIComponent(img.alt || 'food');
      const w = Math.max(240, img.clientWidth || parseInt(img.getAttribute('width')) || 800);
      const h = Math.max(240, img.clientHeight || parseInt(img.getAttribute('height')) || 600);
      const src1x = `https://picsum.photos/seed/${altSeed}/${w}/${h}`;
      const src2x = `https://picsum.photos/seed/${altSeed}/${w*2}/${h*2}`;
      img.src = src1x;
      img.srcset = `${src1x} 1x, ${src2x} 2x`;
      if (!img.hasAttribute('sizes')) img.setAttribute('sizes', '100vw');
    };
    img.addEventListener('error', onErr, { once: true });
  });

  const lightbox = document.getElementById('lightbox');
  if (lightbox) {
    const lbImg = lightbox.querySelector('img');
    const lbClose = lightbox.querySelector('.lightbox-close');
    const open = (src, alt) => {
      lbImg.src = src;
      lbImg.alt = alt || '';
      lightbox.classList.add('open');
      lightbox.setAttribute('aria-hidden', 'false');
    };
    const close = () => {
      lightbox.classList.remove('open');
      lightbox.setAttribute('aria-hidden', 'true');
      lbImg.src = '';
    };
    document.addEventListener('click', (e) => {
      const target = e.target;
      const trigger = target.closest('[data-lightbox]');
      if (!trigger) return;
      const img = trigger.tagName === 'IMG' ? trigger : trigger.querySelector('img');
      if (!img) return;
      e.preventDefault();
      open(img.src, img.alt);
    });
    lbClose && lbClose.addEventListener('click', close);
    lightbox.addEventListener('click', (e) => { if (e.target === lightbox) close(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && lightbox.classList.contains('open')) close(); });
  }

  const grid = document.getElementById('menuGrid');
  if (grid) {
    const dishes = Array.from(grid.querySelectorAll('.dish'));
    const buttons = Array.from(document.querySelectorAll('.filter-btn'));
    const search = document.getElementById('menuSearch');
    let activeFilter = 'all';

    const apply = () => {
      const q = (search && search.value || '').toLowerCase();
      dishes.forEach((d) => {
        const matchCat = activeFilter === 'all' || d.dataset.category === activeFilter;
        const text = d.textContent.toLowerCase();
        const matchText = !q || text.includes(q);
        d.style.display = matchCat && matchText ? '' : 'none';
      });
    };

    buttons.forEach((btn) => {
      btn.addEventListener('click', () => {
        buttons.forEach((b) => b.classList.remove('active'));
        btn.classList.add('active');
        activeFilter = btn.dataset.filter || 'all';
        apply();
      });
    });
    if (search) search.addEventListener('input', apply);
    apply();

    const priceMap = {
      'Jollof Rice': 2500,
      'Fried Rice': 2600,
      'Coconut Rice': 2800,
      'White Rice & Stew': 2300,
      'Egusi Soup': 3000,
      'Okra Soup': 2800,
      'Pepper Soup': 3200,
      'Seafood Soup': 3800,
      'Grilled Chicken': 3500,
      'Grilled Fish': 4000,
      'Beef Kebab': 2700,
      'BBQ Ribs': 5200,
      'Meat Pie': 800,
      'Puff-Puff': 600,
      'Shawarma': 2000,
      'Pizza Slices': 1500,
      'Zobo': 700,
      'Fruit Smoothie': 1800,
      'Fresh Juice': 1200,
      'Mocktail': 2000,
      'Beef Fried Rice': 3000,
      'Afang Soup': 3200,
      'Spicy Wings': 2500,
      'Club Sandwich': 2200,
      'Milkshake': 1800,
      'Spiced Biryani': 3400
    };

    const ensureButtons = () => {
      dishes.forEach((d) => {
        const titleEl = d.querySelector('h3');
        if (!titleEl) return;
        const name = titleEl.textContent.trim();
        const price = priceMap[name] || 2000;
        d.dataset.name = name;
        d.dataset.price = String(price);
        if (!d.querySelector('.add-to-cart')) {
          const btn = document.createElement('button');
          btn.className = 'btn add-to-cart';
          btn.type = 'button';
          btn.textContent = `Add to cart • ₦${price.toLocaleString()}`;
          d.appendChild(btn);
        }
      });
    };
    ensureButtons();

    const getCart = () => {
      try { return JSON.parse(localStorage.getItem('cart') || '[]'); } catch { return []; }
    };
    const saveCart = (cart) => localStorage.setItem('cart', JSON.stringify(cart));
    const addToCart = (name, price, qty = 1) => {
      const cart = getCart();
      const idx = cart.findIndex((i) => i.name === name);
      if (idx > -1) cart[idx].qty += qty; else cart.push({ name, price, qty });
      saveCart(cart);
    };

    grid.addEventListener('click', (e) => {
      const btn = e.target.closest('.add-to-cart');
      if (!btn) return;
      const card = btn.closest('.dish');
      if (!card) return;
      const name = card.dataset.name;
      const price = parseInt(card.dataset.price, 10) || 0;
      addToCart(name, price, 1);
      btn.textContent = 'Added ✓';
      setTimeout(() => { btn.textContent = `Add to cart • ₦${price.toLocaleString()}`; }, 900);
    });
  }

  document.querySelectorAll('.link-card').forEach((card) => {
    card.setAttribute('tabindex', '0');
    const link = card.querySelector('a');
    if (!link) return;
    const go = () => link.click();
    card.addEventListener('click', (e) => { if (e.target.tagName !== 'A') go(); });
    card.addEventListener('keydown', (e) => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); go(); } });
  });

  const carousel = document.getElementById('testimonialCarousel');
  if (carousel) {
    const track = carousel.querySelector('.carousel-track');
    const prev = carousel.querySelector('.prev');
    const next = carousel.querySelector('.next');
    const stepEl = track.querySelector('.t-card');
    const gap = 12;
    const step = () => (stepEl ? stepEl.offsetWidth + gap : 300);
    let autoplay;

    const go = (dir = 1) => {
      const scroller = carousel; // visible container
      const s = step();
      const maxScroll = track.scrollWidth - scroller.clientWidth;
      const maxIndex = Math.max(0, Math.floor(maxScroll / s));
      // Compute current snapped index
      const currentIndex = Math.round(scroller.scrollLeft / s);
      let nextIndex = currentIndex + (dir > 0 ? 1 : -1);
      if (nextIndex > maxIndex) nextIndex = 0; else if (nextIndex < 0) nextIndex = maxIndex;
      scroller.scrollTo({ left: nextIndex * s, behavior: 'smooth' });
    };

    prev && prev.addEventListener('click', () => go(-1));
    next && next.addEventListener('click', () => go(1));

    // Delegated click handler to catch clicks on inner glyphs or if DOM changes
    carousel.addEventListener('click', (e) => {
      const prevEl = e.target.closest('.prev');
      const nextEl = e.target.closest('.next');
      if (prevEl) { e.preventDefault(); go(-1); }
      if (nextEl) { e.preventDefault(); go(1); }
    });

    const start = () => { if (!autoplay) autoplay = setInterval(() => go(1), 2000); };
    const stop = () => { if (autoplay) { clearInterval(autoplay); autoplay = null; } };

    carousel.addEventListener('mouseenter', stop);
    carousel.addEventListener('mouseleave', start);
    carousel.addEventListener('focusin', stop);
    carousel.addEventListener('focusout', start);

    start();
  }

  const cartBody = document.getElementById('cartBody');
  if (cartBody) {
    const cartItemsEl = document.getElementById('cartItems');
    const cartTotalEl = document.getElementById('cartTotal');
    const clearBtn = document.getElementById('clearCartBtn');
    const payBtn = document.getElementById('payNowBtn');
    const status = document.getElementById('paymentStatus');
    const addCustomBtn = document.getElementById('addCustomBtn');
    const customName = document.getElementById('customName');
    const customPrice = document.getElementById('customPrice');

    const getCart = () => { try { return JSON.parse(localStorage.getItem('cart') || '[]'); } catch { return []; } };
    const saveCart = (c) => localStorage.setItem('cart', JSON.stringify(c));

    const render = () => {
      const cart = getCart();
      cartBody.innerHTML = '';
      let items = 0; let total = 0;
      cart.forEach((it, i) => {
        items += it.qty; total += it.price * it.qty;
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td style="padding:8px;">${it.name}</td>
          <td style="padding:8px; text-align:right;">${it.price.toLocaleString()}</td>
          <td style="padding:8px; text-align:center;">
            <input type="number" min="1" value="${it.qty}" data-idx="${i}" class="qty-input" style="width:64px;" />
          </td>
          <td style="padding:8px; text-align:right;">${(it.price * it.qty).toLocaleString()}</td>
          <td style="padding:8px; text-align:center;">
            <button class="btn" data-remove="${i}">Remove</button>
          </td>`;
        cartBody.appendChild(tr);
      });
      cartItemsEl.textContent = String(items);
      cartTotalEl.textContent = total.toLocaleString();
    };

    cartBody.addEventListener('input', (e) => {
      const inp = e.target.closest('.qty-input');
      if (!inp) return;
      const idx = parseInt(inp.dataset.idx, 10);
      const val = Math.max(1, parseInt(inp.value, 10) || 1);
      const cart = getCart();
      if (cart[idx]) cart[idx].qty = val;
      saveCart(cart); render();
    });

    cartBody.addEventListener('click', (e) => {
      const btn = e.target.closest('[data-remove]');
      if (!btn) return;
      const idx = parseInt(btn.getAttribute('data-remove'), 10);
      const cart = getCart();
      cart.splice(idx, 1); saveCart(cart); render();
    });

    clearBtn && clearBtn.addEventListener('click', () => { saveCart([]); render(); });

    addCustomBtn && addCustomBtn.addEventListener('click', () => {
      const name = (customName.value || '').trim();
      const price = parseInt(customPrice.value, 10) || 0;
      if (!name || price <= 0) { status.textContent = 'Enter a valid name and price.'; return; }
      const cart = getCart();
      cart.push({ name, price, qty: 1 });
      saveCart(cart); render();
      customName.value = ''; customPrice.value = '';
      status.textContent = 'Added custom item to cart.';
      setTimeout(() => status.textContent = '', 1200);
    });

    const methodInputs = document.querySelectorAll('input[name="payMethod"]');
    const cardFields = document.getElementById('cardFields');
    const bankFields = document.getElementById('bankFields');

    const showMethod = () => {
      const method = document.querySelector('input[name="payMethod"]:checked')?.value || 'card';
      if (cardFields) cardFields.style.display = method === 'card' ? '' : 'none';
      if (bankFields) bankFields.style.display = method === 'bank' ? '' : 'none';
    };
    methodInputs.forEach((i) => i.addEventListener('change', showMethod));
    showMethod();

    const setErr = (id, msg) => { const el = document.querySelector(`[data-error-for="${id}"]`); if (el) el.textContent = msg || ''; };
    const cleanNumber = (v) => v.replace(/\D+/g, '').slice(0, 19);
    const formatCard = (v) => cleanNumber(v).replace(/(\d{4})(?=\d)/g, '$1 ').trim();
    const luhn = (num) => { let sum=0, alt=false; for (let i=num.length-1;i>=0;i--){let n=parseInt(num[i],10); if(alt){n*=2; if(n>9)n-=9;} sum+=n; alt=!alt;} return sum%10===0; };

    const cardNumber = document.getElementById('cardNumber');
    const cardExpiry = document.getElementById('cardExpiry');
    const cardCvv = document.getElementById('cardCvv');
    const cardName = document.getElementById('cardName');
    if (cardNumber) cardNumber.addEventListener('input', () => { cardNumber.value = formatCard(cardNumber.value); });
    if (cardExpiry) cardExpiry.addEventListener('input', () => { cardExpiry.value = cardExpiry.value.replace(/[^\d/]/g,'').slice(0,5); });

    payBtn && payBtn.addEventListener('click', () => {
      const cart = getCart();
      const total = cart.reduce((s, it) => s + it.price * it.qty, 0);
      if (!cart.length || total <= 0) { status.textContent = 'Your cart is empty.'; return; }

      const method = document.querySelector('input[name="payMethod"]:checked')?.value || 'card';
      let ok = true;
      if (method === 'card') {
        const name = (cardName?.value || '').trim();
        const num = cleanNumber(cardNumber?.value || '');
        const exp = (cardExpiry?.value || '').trim();
        const cvv = (cardCvv?.value || '').trim();
        if (!name) { setErr('cardName', 'Enter cardholder name'); ok = false; } else setErr('cardName');
        if (num.length < 13 || !luhn(num)) { setErr('cardNumber', 'Enter a valid card number'); ok = false; } else setErr('cardNumber');
        const m = exp.match(/^(\d{2})\/(\d{2})$/);
        if (!m) { setErr('cardExpiry', 'MM/YY'); ok = false; } else {
          const mm = parseInt(m[1],10), yy = 2000 + parseInt(m[2],10);
          const now = new Date(); const expDate = new Date(yy, mm);
          if (mm<1||mm>12|| expDate <= now) { setErr('cardExpiry', 'Card expired'); ok = false; } else setErr('cardExpiry');
        }
        if (!/^\d{3,4}$/.test(cvv)) { setErr('cardCvv', 'Invalid CVV'); ok = false; } else setErr('cardCvv');
      } else {
        const bankRef = document.getElementById('bankRef');
        const ref = (bankRef?.value || '').trim();
        if (!ref || ref.length < 3) { setErr('bankRef', 'Enter transfer reference'); ok = false; } else setErr('bankRef');
      }
      if (!ok) { status.textContent = 'Please correct the highlighted fields.'; return; }

      status.textContent = 'Processing payment...';
      setTimeout(() => { status.textContent = 'Payment successful! Order confirmed. ✅'; saveCart([]); render(); }, 1500);
    });

    render();
  }

  const reservationForm = document.getElementById('reservationForm');
  if (reservationForm) {
    const status = document.getElementById('reservationStatus');
    const setError = (id, msg) => { const el = document.querySelector(`[data-error-for="${id}"]`); if (el) el.textContent = msg || ''; };
    reservationForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const name = reservationForm.rName.value.trim();
      const phone = reservationForm.rPhone.value.trim();
      const date = reservationForm.rDate.value;
      const time = reservationForm.rTime.value;
      const guests = parseInt(reservationForm.rGuests.value, 10) || 0;
      const occasion = reservationForm.rOccasion.value;
      let ok = true;
      if (!name) { setError('rName', 'Enter your name'); ok = false; } else setError('rName');
      if (!phone || phone.length < 7) { setError('rPhone', 'Enter a valid phone'); ok = false; } else setError('rPhone');
      if (!date) { setError('rDate', 'Select a date'); ok = false; } else setError('rDate');
      if (!time) { setError('rTime', 'Select a time'); ok = false; } else setError('rTime');
      if (guests < 1) { setError('rGuests', 'Guests must be at least 1'); ok = false; } else setError('rGuests');
      if (!occasion) { setError('rOccasion', 'Select an occasion'); ok = false; } else setError('rOccasion');
      if (!ok) { status.textContent = 'Please correct the fields above.'; return; }
      status.textContent = 'Submitting...';
      setTimeout(() => {
        status.textContent = 'Reservation received! We will confirm shortly.';
        reservationForm.reset();
      }, 900);
    });
  }
});
