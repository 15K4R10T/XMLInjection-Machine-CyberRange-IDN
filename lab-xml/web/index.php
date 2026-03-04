<?php $active = 'home'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>XML Injection Lab — ID-Networkers</title>
<?php include 'includes/shared_css.php'; ?>
<style>
/* HERO */
.hero{background:var(--surface);border-bottom:1px solid var(--bd)}
.hero-in{max-width:1160px;margin:0 auto;padding:64px 40px 56px;display:grid;grid-template-columns:1fr 210px;gap:56px;align-items:center;position:relative}
.hero-in::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 50% 100% at 0 50%,rgba(230,57,70,.05),transparent 65%);pointer-events:none}
.hero-eye{display:inline-flex;align-items:center;gap:8px;font-size:.66rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--red);background:var(--rbg);border:1px solid var(--rbdr);padding:4px 12px;border-radius:20px;margin-bottom:20px}
.hero-eye i{width:6px;height:6px;border-radius:50%;background:var(--red);animation:blink 2s infinite;flex-shrink:0}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.2}}
.hero h1{font-size:2.6rem;font-weight:800;line-height:1.15;letter-spacing:-.025em;color:var(--t1);margin-bottom:16px}
.hero h1 b{color:var(--red)}
.hero-sub{font-size:.9rem;color:var(--t2);max-width:500px;line-height:1.8;margin-bottom:24px}
.hero-note{display:inline-flex;align-items:center;gap:10px;font-size:.72rem;font-family:var(--mono);color:var(--t3);border:1px solid var(--bd);border-radius:var(--r);padding:8px 16px;background:var(--bg)}
.dot-r{width:7px;height:7px;border-radius:50%;background:var(--red);flex-shrink:0;animation:blink 2s infinite}
.hero-stats{display:flex;flex-direction:column;gap:10px}
.stat{background:var(--card);border:1px solid var(--bd);border-radius:var(--r2);padding:16px 20px;text-align:center;transition:border-color .15s}
.stat:hover{border-color:var(--red)}
.stat-n{font-size:2rem;font-weight:800;color:var(--red);font-family:var(--mono);line-height:1}
.stat-l{font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--t3);margin-top:5px}

/* MAIN */
.main{max-width:1160px;margin:0 auto;padding:48px 40px 72px}
.sec{margin-bottom:48px}
.sec-head{display:flex;align-items:center;gap:12px;margin-bottom:22px}
.sec-head::before{content:'';width:3px;height:16px;background:var(--red);border-radius:2px;flex-shrink:0}
.sec-head h2{font-size:.72rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--t2)}

/* ABOUT */
.about{background:var(--card);border:1px solid var(--bd);border-left:3px solid var(--red);border-radius:var(--r2);padding:22px 26px}
.about p{font-size:.88rem;color:var(--t2);line-height:1.85}

/* ATTACK FLOW */
.flow{display:grid;grid-template-columns:repeat(4,1fr);gap:12px}
.flow-step{background:var(--card);border:1px solid var(--bd);border-radius:var(--r2);padding:18px 16px;position:relative}
.flow-step::after{content:'→';position:absolute;right:-14px;top:50%;transform:translateY(-50%);color:var(--t3);font-size:1rem;z-index:1}
.flow-step:last-child::after{display:none}
.flow-num{font-size:.64rem;font-weight:700;letter-spacing:.1em;font-family:var(--mono);color:var(--red);margin-bottom:8px}
.flow-title{font-size:.82rem;font-weight:700;color:var(--t1);margin-bottom:4px}
.flow-desc{font-size:.74rem;color:var(--t2);line-height:1.55}

/* MODULES */
.mods{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}
.mod{display:block;color:inherit;background:var(--card);border:1px solid var(--bd);border-radius:var(--r2);overflow:hidden;transition:transform .15s,border-color .15s,box-shadow .15s}
.mod:hover{transform:translateY(-4px);border-color:var(--bd2);box-shadow:0 16px 48px rgba(0,0,0,.5)}
.mod-line{height:3px}
.mod-line.g{background:var(--green)}.mod-line.o{background:var(--orange)}.mod-line.r{background:var(--red)}
.mod-body{padding:22px}
.mod-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px}
.mod-ico{width:38px;height:38px;border-radius:var(--r);display:flex;align-items:center;justify-content:center}
.mod-ico.g{background:var(--gbg);color:var(--green)}
.mod-ico.o{background:var(--obg);color:var(--orange)}
.mod-ico.r{background:var(--rbg);color:var(--red)}
.mod-ico svg{width:18px;height:18px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.mod h3{font-size:.95rem;font-weight:700;color:var(--t1);margin-bottom:8px;letter-spacing:-.01em}
.mod-desc{font-size:.82rem;color:var(--t2);line-height:1.7;margin-bottom:14px}
.mod-list{list-style:none;display:flex;flex-direction:column;gap:5px;margin-bottom:20px}
.mod-list li{font-size:.76rem;color:var(--t3);font-family:var(--mono);padding-left:14px;position:relative}
.mod-list li::before{content:'›';position:absolute;left:0;color:var(--red)}
.mod-foot{display:flex;align-items:center;justify-content:space-between;padding-top:14px;border-top:1px solid var(--bd);font-size:.78rem;font-weight:600;color:var(--t3);transition:color .15s}
.mod:hover .mod-foot{color:var(--red)}
.mod-foot svg{width:14px;height:14px;fill:none;stroke:currentColor;stroke-width:2.5;stroke-linecap:round;stroke-linejoin:round}

/* CHEATSHEET */
.cheats{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}
.cheat{background:var(--card);border:1px solid var(--bd);border-radius:var(--r2);padding:20px}
.cheat-ttl{font-size:.64rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;font-family:var(--mono);margin-bottom:12px}
.cheat-ttl.g{color:var(--green)}.cheat-ttl.o{color:var(--orange)}.cheat-ttl.r{color:var(--red)}
.code{background:var(--bg);border:1px solid var(--bd);border-radius:var(--r);padding:12px 14px;font-family:var(--mono);font-size:.78rem;line-height:1.85;color:var(--t2)}

@media(max-width:900px){
  .hero-in,.mods,.cheats,.flow{grid-template-columns:1fr}
  .hero-stats{flex-direction:row}
  .stat{flex:1}
  .nav,.main,footer{padding-left:20px;padding-right:20px}
  .hero-in{padding:40px 20px}
  .flow-step::after{display:none}
}
</style>
</head>
<body>
<?php include 'includes/nav.php'; ?>

<div class="hero">
  <div class="hero-in">
    <div>
      <div class="hero-eye"><i></i>Vulnerability Research</div>
      <h1>XML Injection<br><b>Lab Environment</b></h1>
      <p class="hero-sub">Lingkungan praktik XML Injection terstruktur untuk keperluan edukasi keamanan siber. Tiga modul mencakup teknik dari basic XML manipulation, XPath injection, hingga XXE (XML External Entity) yang kritis.</p>
      <div class="hero-note">
        <span class="dot-r"></span>
        FOR EDUCATIONAL USE ONLY &mdash; Gunakan hanya di environment lab terisolasi
      </div>
    </div>
    <div class="hero-stats">
      <div class="stat"><div class="stat-n">3</div><div class="stat-l">Modules</div></div>
      <div class="stat"><div class="stat-n">4</div><div class="stat-l">Challenges</div></div>
      <div class="stat"><div class="stat-n">4</div><div class="stat-l">Flags</div></div>
    </div>
  </div>
</div>

<div class="main">

  <div class="sec">
    <div class="sec-head"><h2>Tentang Lab</h2></div>
    <div class="about">
      <p>Lab ini dirancang untuk memahami kerentanan yang muncul ketika aplikasi memproses data XML tanpa validasi yang memadai. Dimulai dari manipulasi struktur XML dasar, lanjut ke XPath Injection untuk bypass autentikasi, hingga teknik XXE (XML External Entity) yang dapat digunakan untuk membaca file sistem dan melakukan SSRF terhadap layanan internal.</p>
    </div>
  </div>

  <div class="sec">
    <div class="sec-head"><h2>Attack Flow</h2></div>
    <div class="flow">
      <div class="flow-step">
        <div class="flow-num">01</div>
        <div class="flow-title">Identify</div>
        <div class="flow-desc">Temukan endpoint yang menerima dan memproses input XML dari pengguna</div>
      </div>
      <div class="flow-step">
        <div class="flow-num">02</div>
        <div class="flow-title">Probe</div>
        <div class="flow-desc">Uji karakter khusus XML untuk mengetahui apakah input di-sanitasi</div>
      </div>
      <div class="flow-step">
        <div class="flow-num">03</div>
        <div class="flow-title">Inject</div>
        <div class="flow-desc">Sisipkan payload — tag tambahan, XPath expression, atau entity declaration</div>
      </div>
      <div class="flow-step">
        <div class="flow-num">04</div>
        <div class="flow-title">Extract</div>
        <div class="flow-desc">Baca data sensitif, bypass autentikasi, atau akses resource internal</div>
      </div>
    </div>
  </div>

  <div class="sec">
    <div class="sec-head"><h2>Lab Modules</h2></div>
    <div class="mods">

      <a href="/basic/" class="mod">
        <div class="mod-line g"></div>
        <div class="mod-body">
          <div class="mod-top">
            <div class="mod-ico g">
              <svg viewBox="0 0 24 24"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
            </div>
            <span class="tag g">EASY</span>
          </div>
          <h3>Basic XML Injection</h3>
          <p class="mod-desc">Manipulasi struktur dokumen XML dengan menyisipkan tag dan atribut tambahan pada input yang tidak di-sanitasi.</p>
          <ul class="mod-list">
            <li>Tag injection pada form input</li>
            <li>Attribute value manipulation</li>
            <li>XML structure breakout</li>
          </ul>
          <div class="mod-foot">
            <span>Mulai Modul</span>
            <svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </div>
        </div>
      </a>

      <a href="/xpath/" class="mod">
        <div class="mod-line o"></div>
        <div class="mod-body">
          <div class="mod-top">
            <div class="mod-ico o">
              <svg viewBox="0 0 24 24"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>
            </div>
            <span class="tag o">MEDIUM</span>
          </div>
          <h3>XPath Injection</h3>
          <p class="mod-desc">Manipulasi query XPath pada form autentikasi untuk bypass login dan mengekstrak seluruh isi dokumen XML.</p>
          <ul class="mod-list">
            <li>XPath authentication bypass</li>
            <li>Blind XPath enumeration</li>
            <li>Full document extraction</li>
          </ul>
          <div class="mod-foot">
            <span>Mulai Modul</span>
            <svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </div>
        </div>
      </a>

      <a href="/xxe/" class="mod">
        <div class="mod-line r"></div>
        <div class="mod-body">
          <div class="mod-top">
            <div class="mod-ico r">
              <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
            <span class="tag r">HARD</span>
          </div>
          <h3>XXE Injection</h3>
          <p class="mod-desc">Deklarasi XML External Entity untuk membaca file sistem lokal, melakukan SSRF, dan memetakan jaringan internal.</p>
          <ul class="mod-list">
            <li>Local file disclosure (LFD)</li>
            <li>SSRF via XXE</li>
            <li>Blind XXE exfiltration</li>
          </ul>
          <div class="mod-foot">
            <span>Mulai Modul</span>
            <svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </div>
        </div>
      </a>

    </div>
  </div>

  <div class="sec">
    <div class="sec-head"><h2>Quick Reference</h2></div>
    <div class="cheats">
      <div class="cheat">
        <div class="cheat-ttl g">Karakter Khusus XML</div>
        <div class="code">
<span class="kw">&lt;</span>   less-than     <span class="cm">&amp;lt;</span>
<span class="kw">&gt;</span>   greater-than  <span class="cm">&amp;gt;</span>
<span class="kw">&amp;</span>   ampersand     <span class="cm">&amp;amp;</span>
<span class="kw">"</span>   double quote  <span class="cm">&amp;quot;</span>
<span class="kw">'</span>   single quote  <span class="cm">&amp;apos;</span></div>
      </div>
      <div class="cheat">
        <div class="cheat-ttl o">XPath Always True</div>
        <div class="code">
<span class="str">' or '1'='1</span>
<span class="str">' or 1=1 or 'a'='a</span>
<span class="cm">-- pilih semua node</span>
<span class="kw">*</span>
<span class="cm">-- kondisi selalu benar</span>
<span class="kw">1=1</span></div>
      </div>
      <div class="cheat">
        <div class="cheat-ttl r">XXE Entity Skeleton</div>
        <div class="code">
<span class="kw">&lt;!DOCTYPE</span> <span class="attr">foo</span> [
  <span class="kw">&lt;!ENTITY</span> <span class="attr">xxe</span>
    <span class="kw">SYSTEM</span> <span class="str">"file:///etc/passwd"</span>
  <span class="kw">&gt;</span>
]<span class="kw">&gt;</span>
<span class="tag-xml">&lt;data&gt;</span><span class="kw">&amp;xxe;</span><span class="tag-xml">&lt;/data&gt;</span></div>
      </div>
    </div>
  </div>

</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
