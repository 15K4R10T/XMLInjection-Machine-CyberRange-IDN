<?php
/* nav.php — shared navbar, set $active before including */
$active = $active ?? 'home';
?>
<nav class="nav">
  <a class="nav-logo" href="/"><img src="/includes/LOGO-IDN-SOSMED-200x63.png" alt="ID-Networkers"></a>
  <div class="nav-menu">
    <a href="/"      class="<?= $active==='home'  ?'on':'' ?>">Dashboard</a>
    <a href="/basic/" class="<?= $active==='basic' ?'on':'' ?>">Basic XML</a>
    <a href="/xpath/" class="<?= $active==='xpath' ?'on':'' ?>">XPath Injection</a>
    <a href="/xxe/"   class="<?= $active==='xxe'   ?'on':'' ?>">XXE</a>
  </div>
  <div class="nav-pill">Security Lab</div>
</nav>
