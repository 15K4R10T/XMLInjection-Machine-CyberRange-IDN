<?php
$active = 'xpath';
$msg = ''; $mtype = ''; $qshown = '';

// Simulasi "database" XML — dalam kondisi nyata, file ini ada di server
$xml_data = '<?xml version="1.0" encoding="UTF-8"?>
<users>
    <user>
        <id>1</id>
        <username>admin</username>
        <password>admin123</password>
        <role>admin</role>
        <secret>FLAG{xpath_auth_bypass_complete}</secret>
    </user>
    <user>
        <id>2</id>
        <username>alice</username>
        <password>alice_pass</password>
        <role>user</role>
        <secret>FLAG{xpath_alice_extracted}</secret>
    </user>
    <user>
        <id>3</id>
        <username>bob</username>
        <password>b0b_s3cr3t</password>
        <role>user</role>
        <secret>FLAG{xpath_bob_extracted}</secret>
    </user>
    <user>
        <id>4</id>
        <username>charlie</username>
        <password>ch@rlie99</password>
        <role>user</role>
        <secret>FLAG{xpath_charlie_extracted}</secret>
    </user>
</users>';

$found_users = [];
$xpath_query = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Vulnerable XPath query — input langsung dikoncatenasi
    $xpath_query = "//user[username/text()='$username' and password/text()='$password']";
    $qshown = $xpath_query;

    libxml_use_internal_errors(true);
    $xml = @simplexml_load_string($xml_data);

    if ($xml) {
        $result = @$xml->xpath($xpath_query);
        if ($result && count($result) > 0) {
            foreach ($result as $user) {
                $found_users[] = [
                    'id'       => (string)$user->id,
                    'username' => (string)$user->username,
                    'role'     => (string)$user->role,
                    'secret'   => (string)$user->secret,
                ];
            }
            if (count($found_users) === 1) {
                $msg   = "Login berhasil &mdash; selamat datang, <strong>" . htmlspecialchars($found_users[0]['username'], ENT_QUOTES, 'UTF-8') . "</strong> (role: {$found_users[0]['role']})";
                $mtype = 'ok';
            } else {
                $msg   = "Semua user berhasil di-dump &mdash; " . count($found_users) . " akun ditemukan.";
                $mtype = 'ok';
            }
        } else {
            $msg   = "Login gagal &mdash; username atau password tidak ditemukan.";
            $mtype = 'err';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>XPath Injection — ID-Networkers Lab</title>
<?php include '../includes/shared_css.php'; ?>
</head>
<body>
<?php include '../includes/nav.php'; ?>

<div class="phdr">
  <div class="phdr-in">
    <div class="bc">
      <a href="/">Dashboard</a><span class="bc-sep">/</span><span>XPath Injection</span>
    </div>
    <h1>XPath Injection <span class="tag o">MEDIUM</span></h1>
    <p class="phdr-desc">Aplikasi ini menyimpan data pengguna dalam file XML dan menggunakan XPath query untuk autentikasi. Manipulasi query untuk bypass login dan mengekstrak seluruh isi dokumen.</p>
  </div>
</div>

<div class="wrap">

  <div class="box">
    <div class="box-t">Objectives</div>
    <ul class="obj-list">
      <li><div class="obj-n">1</div><span>Pahami struktur XPath query yang digunakan untuk autentikasi</span></li>
      <li><div class="obj-n">2</div><span>Bypass login sebagai <code class="ic">admin</code> tanpa mengetahui password</span></li>
      <li><div class="obj-n">3</div><span>Dump semua akun user beserta field <code class="ic">secret</code>-nya</span></li>
      <li><div class="obj-n">4</div><span>Kumpulkan keempat FLAG dari masing-masing akun</span></li>
    </ul>
  </div>

  <div class="box">
    <div class="box-t">XPath vs SQL — Perbandingan</div>
    <p class="prose" style="margin-bottom:14px">XPath Injection bekerja dengan prinsip yang mirip SQL Injection, namun menarget query XPath alih-alih SQL. Perbedaannya:</p>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
      <div>
        <div class="ql" style="margin-bottom:6px;font-family:var(--mono);font-size:.6rem;letter-spacing:.12em;text-transform:uppercase;color:var(--t3)">SQL Query</div>
        <div class="qbox" style="margin-bottom:0">SELECT * FROM users<br>WHERE username='<span class="str">INPUT</span>'<br>AND password='<span class="str">INPUT</span>'</div>
      </div>
      <div>
        <div class="ql" style="margin-bottom:6px;font-family:var(--mono);font-size:.6rem;letter-spacing:.12em;text-transform:uppercase;color:var(--t3)">XPath Query</div>
        <div class="qbox" style="margin-bottom:0">//user[username='<span class="str">INPUT</span>'<br>and password='<span class="str">INPUT</span>']</div>
      </div>
    </div>
  </div>

  <div class="box">
    <div class="box-t">Login Form &mdash; Vulnerable to XPath Injection</div>
    <form method="POST" action="/xpath/">
      <div class="fg">
        <label class="fl">Username</label>
        <input class="fi" type="text" name="username"
          value="<?= htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
          placeholder="username atau XPath payload">
      </div>
      <div class="fg">
        <label class="fl">Password</label>
        <input class="fi" type="text" name="password"
          value="<?= htmlspecialchars($_POST['password'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
          placeholder="password atau XPath payload">
      </div>
      <div style="display:flex;gap:10px">
        <button type="submit" class="btn btn-r">Login</button>
        <?php if($_SERVER['REQUEST_METHOD']==='POST'): ?>
        <a href="/xpath/" class="btn btn-g">Reset</a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <?php if ($qshown): ?>
  <div class="box">
    <div class="box-t">Executed XPath Query</div>
    <div class="qbox" style="margin-bottom:0"><div class="ql">XPath</div><?= htmlspecialchars($qshown, ENT_QUOTES, 'UTF-8') ?></div>
  </div>
  <?php endif; ?>

  <?php if ($msg): ?>
  <div class="alert a-<?= $mtype ?>"><?= $msg ?></div>
  <?php endif; ?>

  <?php if (!empty($found_users)): ?>
  <div class="box">
    <div class="box-t">User Data &mdash; <?= count($found_users) ?> record(s) returned</div>
    <div class="tbl-wrap">
      <table class="tbl">
        <thead><tr><th>ID</th><th>Username</th><th>Role</th><th>Secret / FLAG</th></tr></thead>
        <tbody>
          <?php foreach ($found_users as $u): ?>
          <tr>
            <td><?= htmlspecialchars($u['id'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($u['username'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($u['role'], ENT_QUOTES, 'UTF-8') ?></td>
            <td style="color:var(--green)"><?= htmlspecialchars($u['secret'], ENT_QUOTES, 'UTF-8') ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>

  <div class="box">
    <div class="box-t">Hints</div>

    <details class="hint">
      <summary>Hint 1 &mdash; Struktur XPath query</summary>
      <div class="hint-body">
        Query yang dieksekusi:<br>
        <code class="ic">//user[username/text()='INPUT' and password/text()='INPUT']</code><br><br>
        Tujuannya: buat kondisi menjadi selalu <strong>TRUE</strong> dan buang kondisi password.
      </div>
    </details>

    <details class="hint">
      <summary>Hint 2 &mdash; Bypass login admin</summary>
      <div class="hint-body">
        Username: <code class="ic">admin' or '1'='1</code> (password bebas)<br>
        XPath menjadi: <code class="ic">//user[username/text()='admin' or '1'='1' and password...]</code><br>
        Karena <code class="ic">'1'='1'</code> selalu TRUE, kondisi terpenuhi.
      </div>
    </details>

    <details class="hint">
      <summary>Hint 3 &mdash; Dump semua user</summary>
      <div class="hint-body">
        Username: <code class="ic">' or 1=1 or 'x'='</code><br>
        Query menjadi selalu TRUE untuk semua node, sehingga seluruh user di-return sekaligus.
      </div>
    </details>

    <details class="hint">
      <summary>Hint 4 &mdash; XPath berbeda dari SQL</summary>
      <div class="hint-body">
        XPath tidak mengenal <code class="ic">--</code> sebagai komentar. Gunakan teknik <em>quote balancing</em>
        dengan menyeimbangkan tanda kutip di akhir payload seperti contoh di Hint 2 dan 3.
      </div>
    </details>
  </div>

</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>
