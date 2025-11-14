(() => {
    // ========== サイドバー用CSS（最小必要分）を動的注入 ==========
    const css = `
    :root{
      --sb-w:300px; --sb-line:#e7ebf0; --ink:#2b2b2b;
      --nav-accent:#2b69a4; --dot:#f6b7ab;
      --tg-w:25px; --tg-h:64px; --tg-top:22svh; --tg-overlap:1px;
    }
    .sidebar{position:fixed; inset:0 auto 0 0; width:var(--sb-w); height:100svh;
      background:#fff; color:var(--ink); display:flex; flex-direction:column;
      transform:translateX(-100%); transition:transform .28s ease; box-shadow:6px 0 24px rgba(0,0,0,.12);
      z-index:1000; min-height:0;}
    .sidebar.is-open{transform:translateX(0);}
  
    .shead{flex-shrink:0; text-align:center; padding:28px 16px 20px; border-bottom:1px solid var(--sb-line);}
    .shead__logo--round{width:140px; height:140px; margin:0 auto 12px; object-fit:contain; border-radius:999px; background:#fff;}
    .shead__zip,.shead__addr{font-size:16px; color:#444; line-height:1.6; margin:4px 0;}
    .shead__tel{display:inline-flex; align-items:center; gap:8px; padding:12px 22px; border-radius:12px;
      background:#f49b87; color:#fff; text-decoration:none; font-weight:800; font-size:22px; letter-spacing:.02em;
      box-shadow:0 4px 10px rgba(244,155,135,.28);}
    .shead__tel-ico{font-size:20px; line-height:1; transform:translateY(-1px);}
  
    .snav{flex:1; min-height:0; overflow-y:auto; -webkit-overflow-scrolling:touch; background:#fff;
      padding-bottom:max(12px, env(safe-area-inset-bottom));}
    .snav__item{display:flex; align-items:center; justify-content:space-between; padding:18px 20px; color:var(--ink);
      text-decoration:none; font-size:18px; font-weight:600; border-bottom:1px solid var(--sb-line); transition:background .2s ease;}
    .snav__item:hover{background:#f9f9f9;}
    .snav__item::after{content:"›"; font-size:20px; color:var(--nav-accent); line-height:1; transform:translateY(-1px);}
  
    .sminor{flex-shrink:0; border-top:1px solid var(--sb-line); padding:20px 22px calc(28px + env(safe-area-inset-bottom));
      display:grid; gap:16px; background:#fff;}
    .sminor__item{display:flex; align-items:center; gap:12px; color:#333; text-decoration:none; font-size:17px; font-weight:600;}
    .sminor__dot{width:14px; height:14px; border-radius:999px;
      background:radial-gradient(circle at 35% 35%, #fff 0 30%, var(--dot) 35% 100%); border:1px solid #f1c9c1;}
  
    .sidebar-mask{position:fixed; inset:0; background:rgba(0,0,0,.35); z-index:900;}
    .sidebar-mask[hidden]{display:none!important;}
  
    /* トグル（SP表示） */
    @media (max-width:1023.98px){
      .sidebar-toggle{position:fixed; left:0; top:var(--tg-top); transform:translateY(-50%);
        width:var(--tg-w); height:var(--tg-h); border:0; border-radius:0 12px 12px 0; background:#fff;
        box-shadow:0 0 10px rgba(0,0,0,.14); display:flex; align-items:center; justify-content:center; padding:0;
        cursor:pointer; transition:left .28s ease, transform .12s ease, box-shadow .2s ease; z-index:1100;}
      .sidebar-toggle .chev{width:12px; height:12px; border-right:3px solid var(--nav-accent); border-bottom:3px solid var(--nav-accent);
        transform:rotate(-45deg); transition:transform .28s ease;}
      body.sidebar-open .sidebar-toggle{left:calc(var(--sb-w) - var(--tg-overlap)); box-shadow:none;}
      body.sidebar-open .sidebar-toggle .chev{transform:rotate(135deg);}
      .sidebar-toggle:focus-visible{outline:3px solid #a7c8ff; outline-offset:2px;}
    }
  
    /* PCは常時表示・本文押し出し */
    @media (min-width:1024px){
      .sidebar{transform:none !important;}
      body{padding-left:var(--sb-w);}
      .sidebar-toggle,.sidebar-mask{display:none !important;}
    }
  
    /* 内部構造の安定化とスクロール */
    .sidebar>.sb-fit{display:flex; flex-direction:column; height:100%; min-height:0;}
    body.no-scroll{overflow:hidden;}
    .snav::-webkit-scrollbar{width:8px;}
    .snav::-webkit-scrollbar-thumb{background:rgba(0,0,0,.15); border-radius:4px;}
    .snav::-webkit-scrollbar-thumb:hover{background:rgba(0,0,0,.25);}
    `;
    const style = document.createElement('style');
    style.textContent = css;
    document.head.appendChild(style);
  
    // ========== DOM生成 ==========
    const aside = document.createElement('aside');
    aside.id = 'site-sidebar';
    aside.className = 'sidebar sidebar--sheet';
    aside.setAttribute('aria-hidden', 'true');
  
    const fit = document.createElement('div');
    fit.className = 'sb-fit';
    fit.id = 'sb-fit';
  
    // Header
    const shead = document.createElement('div');
    shead.className = 'shead shead--stack';
    shead.innerHTML = `
      <img src="images/logo-shinsei.png" alt="信生病院ロゴ" class="shead__logo--round">
      <p class="shead__zip">〒682-0017</p>
      <p class="shead__addr">鳥取県倉吉市清谷町１丁目286番地</p>
      <a class="shead__tel" href="tel:0858-26-7773" aria-label="電話する">
        <span class="shead__tel-ico">📞</span>
        <span class="shead__tel-num">0858-26-7773</span>
      </a>
    `;
  
    // Menu
    const snav = document.createElement('nav');
    snav.className = 'snav snav--flat';
    snav.setAttribute('aria-label', 'サイトメニュー');
    const menu = [
      { href: '/', text: 'トップページ' },
      { href: '/about.html', text: '病院概要' },
      { href: '/dept.html', text: '部門紹介' },
      { href: '/guide.html', text: '診療科目' },
      { href: '/outpatient.html', text: '外来' },
      { href: '/checkup.html', text: '一般・特定健診' },
      { href: '/vaccine.html', text: '予防接種' },
      { href: '/inpatient.html', text: '入院' },
      { href: '/homecare.html', text: '居宅介護事業所' },
      { href: '/contact.html', text: 'お問い合わせ' },
    ];
    snav.innerHTML = menu.map(m => `<a class="snav__item" href="${m.href}">${m.text}</a>`).join('');
  
    // Footer links
    const smini = document.createElement('div');
    smini.className = 'sminor sm-plain';
    smini.innerHTML = `
      <a class="sminor__item" href="/access.html"><span class="sminor__dot"></span><span>アクセス</span></a>
      <a class="sminor__item" href="/recruit.html"><span class="sminor__dot"></span><span>求人情報</span></a>
    `;
  
    fit.appendChild(shead);
    fit.appendChild(snav);
    fit.appendChild(smini);
    aside.appendChild(fit);
    document.body.appendChild(aside);
  
    // Toggleボタン
    const toggleBtn = document.createElement('button');
    toggleBtn.className = 'sidebar-toggle';
    toggleBtn.setAttribute('aria-controls', 'site-sidebar');
    toggleBtn.setAttribute('aria-expanded', 'false');
    toggleBtn.innerHTML = `<span class="chev" aria-hidden="true"></span><span class="sr-only"></span>`;
    document.body.appendChild(toggleBtn);
  
    // 背景マスク
    const mask = document.createElement('div');
    mask.className = 'sidebar-mask';
    mask.hidden = true;
    document.body.appendChild(mask);
  
    // ========== 開閉制御 ==========
    const mq = window.matchMedia('(min-width:1024px)');
  
    function openSidebar() {
      aside.classList.add('is-open');
      document.body.classList.add('no-scroll', 'sidebar-open');
      toggleBtn.setAttribute('aria-expanded', 'true');
      mask.hidden = false;
      aside.setAttribute('aria-hidden', 'false');
    }
    function closeSidebar() {
      aside.classList.remove('is-open');
      document.body.classList.remove('no-scroll', 'sidebar-open');
      toggleBtn.setAttribute('aria-expanded', 'false');
      mask.hidden = true;
      aside.setAttribute('aria-hidden', 'true');
    }
    function toggleSidebar() {
      aside.classList.contains('is-open') ? closeSidebar() : openSidebar();
    }
  
    toggleBtn.addEventListener('click', toggleSidebar);
    mask.addEventListener('click', closeSidebar);
    document.addEventListener('keydown', e => {
      if (e.key === 'Escape' && aside.classList.contains('is-open')) closeSidebar();
    });
  
    function applyMode(e) {
      if (e.matches) { // PC
        aside.classList.add('is-open');
        document.body.classList.remove('no-scroll', 'sidebar-open');
        toggleBtn.setAttribute('aria-expanded', 'true');
        mask.hidden = true;
        aside.setAttribute('aria-hidden', 'false');
      } else {         // SP
        aside.classList.remove('is-open');
        document.body.classList.remove('sidebar-open');
        toggleBtn.setAttribute('aria-expanded', 'false');
        mask.hidden = true;
        aside.setAttribute('aria-hidden', 'true');
      }
    }
  
    applyMode(mq);
    mq.addEventListener('change', applyMode);
  })();
  