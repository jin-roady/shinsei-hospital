// footer.js
(function () {
    const html = `
    <footer class="sitefooter sitefooter--shinsei">
      <div class="container footer__inner">
  
        <!-- ロゴ -->
        <div class="footer__brand">
          <img class="footer__logo" src="images/logo-shinsei.png" alt="信生病院ロゴ">
        </div>
  
        <!-- 情報ブロック -->
        <div class="footer__info-row">
          <div class="footer__info-left">
            <div class="footer__addr">〒682-0017<br>鳥取県倉吉市清谷町１丁目286</div>
            <div class="footer__telbox">
              <div class="footer__tel">
                <span class="footer__tel-ico">📞</span>
                <span class="footer__tel-num">0858-26-7773</span>
              </div>
              <div class="footer__fax">FAX：0858-26-7753</div>
            </div>
          </div>
  
          <div class="footer__info-right">
            <div class="footer__hours-hd">診察受付時間</div>
            <div class="footer__hourline">
              <span class="chip chip--weekday">平　日</span>
              <span class="footer__hourtime">午前 8:30〜17:00</span>
            </div>
            <div class="footer__hourline">
              <span class="chip chip--sat">土　曜</span>
              <span class="footer__hourtime">午前 8:30〜12:00（※休診の場合もございます）</span>
            </div>
          </div>
        </div>
  
        <!-- CTA -->
        <div class="footer__cta">
          <a href="/recruit.html" class="btn-recruit" aria-label="求人情報">
            <span>求人情報</span><i aria-hidden="true">➜</i>
          </a>
        </div>
      </div>
  
      <div class="footer__bar"></div>
      <div class="pagetop-fixed"><a href="#top" aria-label="ページトップへ">PAGE TOP</a></div>
    </footer>
    `;
  
    const mountFooter = () => {
      const host = document.getElementById("site-footer");
      if (host) host.innerHTML = html;
    };
  
    if (document.readyState === "loading") {
      document.addEventListener("DOMContentLoaded", mountFooter);
    } else {
      mountFooter();
    }
  })();
  