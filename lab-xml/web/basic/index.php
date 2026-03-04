<?php
$active = 'basic';
$result = null;
$raw_xml = '';
$parsed = [];
$error = '';
$payload = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = $_POST['name']     ?? '';
    $email    = $_POST['email']    ?? '';
    $message  = $_POST['message']  ?? '';
    $payload  = $name;

    // Vulnerable: input langsung dimasukkan ke XML tanpa sanitasi
    $raw_xml = '<?xml version="1.0" encoding="UTF-8"?>
<submission>
    <name>' . $name . '</name>
    <email>' . $email . '</email>
    <message>' . $message . '</message>
    <timestamp>' . date('Y-m-d H:i:s') . '</timestamp>
    <role>user</role>
    <secret>FLAG{basic_xml_tag_injection}</secret>
</submission>';

    libxml_use_internal_errors(true);
    $xml = @simplexml_load_string($raw_xml);

    if ($xml === false) {
        $errs = libxml_get_errors();
        $error = $errs[0]->message ?? 'XML parse error';
        libxml_clear_errors();
    } else {
        // Tampilkan semua node yang berhasil diparsing
        foreach ($xml->children() as $tag => $val) {
            $parsed[] = ['tag' => $tag, 'value' => (string)$val];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Basic XML Injection — ID-Networkers Lab</title>
<?php include '../includes/shared_css.php'; ?>
</head>
<body>
<?php include '../includes/nav.php'; ?>

<div class="phdr">
  <div class="phdr-in">
    <div class="bc">
      <a href="/">Dashboard</a><span class="bc-sep">/</span><span>Basic XML Injection</span>
    </div>
    <h1>Basic XML Injection <span class="tag g">EASY</span></h1>
    <p class="phdr-desc">Form pengiriman pesan ini membangun dokumen XML dari input pengguna tanpa sanitasi. Sisipkan tag XML pada field nama untuk memanipulasi struktur dokumen.</p>
  </div>
</div>

<div class="wrap">

  <div class="box">
    <div class="box-t">Objectives</div>
    <ul class="obj-list">
      <li><div class="obj-n">1</div><span>Konfirmasi kerentanan dengan menyisipkan karakter <code class="ic">&lt;</code> atau <code class="ic">&gt;</code> pada input</span></li>
      <li><div class="obj-n">2</div><span>Tutup tag <code class="ic">&lt;name&gt;</code> dan sisipkan tag baru ke dalam dokumen XML</span></li>
      <li><div class="obj-n">3</div><span>Override nilai tag <code class="ic">&lt;role&gt;</code> dari <code class="ic">user</code> menjadi <code class="ic">admin</code></span></li>
      <li><div class="obj-n">4</div><span>Baca nilai field <code class="ic">&lt;secret&gt;</code> yang tersembunyi di dalam XML</span></li>
    </ul>
  </div>

  <div class="box">
    <div class="box-t">Vulnerability Context</div>
    <p class="prose">
      Aplikasi ini membangun dokumen XML dengan cara konkatenasi string langsung &mdash; tanpa menggunakan
      <strong>XML encoding</strong> atau <strong>escaping</strong> pada input pengguna.
      Akibatnya, karakter khusus XML seperti <code class="ic">&lt;</code> dan <code class="ic">&gt;</code>
      diinterpretasikan sebagai bagian dari markup, bukan sebagai data.
    </p>
  </div>

  <div class="box">
    <div class="box-t">Contact Form — Vulnerable</div>
    <form method="POST" action="/basic/">
      <div class="fg">
        <label class="fl">Nama <span style="color:var(--red);font-size:.65rem">(VULNERABLE FIELD)</span></label>
        <input class="fi" type="text" name="name"
          value="<?= htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
          placeholder="Masukkan nama atau payload XML">
      </div>
      <div class="fg">
        <label class="fl">Email</label>
        <input class="fi" type="text" name="email"
          value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
          placeholder="email@example.com">
      </div>
      <div class="fg">
        <label class="fl">Pesan</label>
        <textarea class="fi" name="message" rows="3"
          placeholder="Isi pesan..."><?= htmlspecialchars($_POST['message'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
      </div>
      <div style="display:flex;gap:10px">
        <button type="submit" class="btn btn-r">Kirim</button>
        <?php if($_SERVER['REQUEST_METHOD']==='POST'): ?>
        <a href="/basic/" class="btn btn-g">Reset</a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>

  <div class="box">
    <div class="box-t">Generated XML Document</div>
    <div class="qbox" style="margin-bottom:0"><div class="ql">Raw XML (server-side)</div><?= htmlspecialchars($raw_xml, ENT_QUOTES, 'UTF-8') ?></div>
  </div>

  <?php if ($error): ?>
    <div class="alert a-err"><strong>XML Parse Error:</strong> <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?><br><small style="opacity:.7">Struktur XML tidak valid &mdash; payload mungkin perlu disesuaikan.</small></div>
  <?php elseif (!empty($parsed)): ?>
    <div class="box">
      <div class="box-t">Parsed XML Nodes &mdash; <?= count($parsed) ?> field(s) detected</div>
      <div class="tbl-wrap">
        <table class="tbl">
          <thead><tr><th>Tag</th><th>Value</th></tr></thead>
          <tbody>
            <?php foreach ($parsed as $p): ?>
            <tr>
              <td><span class="tag-xml" style="color:var(--purple);font-family:var(--mono)">&lt;<?= htmlspecialchars($p['tag'], ENT_QUOTES, 'UTF-8') ?>&gt;</span></td>
              <td><?= htmlspecialchars($p['value'], ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <?php if (count($parsed) > 5): ?>
    <div class="alert a-ok">Injection berhasil &mdash; <?= count($parsed) - 5 ?> tag tambahan berhasil disisipkan ke dalam dokumen XML.</div>
    <?php endif; ?>

    <?php foreach ($parsed as $p): if (str_contains(strtolower($p['value']), 'flag{')): ?>
    <div class="alert a-ok" style="font-family:var(--mono)"><strong>FLAG DITEMUKAN:</strong> <?= htmlspecialchars($p['value'], ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; endforeach; ?>
  <?php endif; ?>

  <?php endif; ?>

  <div class="box">
    <div class="box-t">Hints</div>

    <details class="hint">
      <summary>Hint 1 &mdash; Konfirmasi kerentanan</summary>
      <div class="hint-body">
        Masukkan karakter <code class="ic">&lt;</code> pada field nama.<br>
        Jika muncul XML parse error, berarti input tidak di-escape dan langsung masuk ke dokumen XML.
      </div>
    </details>

    <details class="hint">
      <summary>Hint 2 &mdash; Tutup dan sisipkan tag</summary>
      <div class="hint-body">
        Struktur XML yang dibuat: <code class="ic">&lt;name&gt;INPUT&lt;/name&gt;</code><br>
        Tutup tag lalu tambahkan tag baru:<br>
        <code class="ic">Alice&lt;/name&gt;&lt;role&gt;admin&lt;/role&gt;&lt;name&gt;</code>
      </div>
    </details>

    <details class="hint">
      <summary>Hint 3 &mdash; Override tag yang sudah ada</summary>
      <div class="hint-body">
        XML parser biasanya membaca tag pertama yang ditemukan. Coba sisipkan tag <code class="ic">&lt;role&gt;</code>
        sebelum tag <code class="ic">&lt;role&gt;</code> aslinya untuk override nilainya:<br>
        <code class="ic">test&lt;/name&gt;&lt;role&gt;admin&lt;/role&gt;&lt;name&gt;x</code>
      </div>
    </details>

    <details class="hint">
      <summary>Hint 4 &mdash; Baca field secret</summary>
      <div class="hint-body">
        Perhatikan tabel hasil parsing di atas. Jika injection berhasil, semua tag &mdash; termasuk
        <code class="ic">&lt;secret&gt;</code> &mdash; akan terlihat di output tabel.
      </div>
    </details>
  </div>

</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>
