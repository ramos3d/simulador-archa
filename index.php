<?php
require_once __DIR__ . '/config.php';
include __DIR__ . '/includes/layout/head-simulator.php';

$isEditMode = (strtolower($_GET['mode'] ?? '') === 'edit');
$editSlug   = trim($_GET['projeto'] ?? '');
$editToken  = trim($_GET['token'] ?? $_GET['lead'] ?? '');
$forceReset = (($_GET['reset'] ?? '') === '1');
?>

<script>
(function () {
  const forceReset = <?= $forceReset ? 'true' : 'false' ?>;
  const isEdit     = <?= $isEditMode ? 'true' : 'false' ?>;
  const editToken  = <?= json_encode($editToken) ?>;

  function clearAll() {
    try { localStorage.clear(); sessionStorage.clear(); } catch (_) {}
  }

  if (forceReset) { clearAll(); return; }

  if (isEdit) {
    if (editToken) {
      try {
        localStorage.setItem('leadUuid',    editToken);
        localStorage.setItem('uuidLaravel', editToken);
        localStorage.setItem('leadActive',  '1');
      } catch (_) {}
    }
    window.__EDIT_MODE__ = true;
  } else {
    clearAll();
  }
})();
</script>

<main class="flex-grow-1">
  <div class="container my-5">
    <?php include __DIR__ . '/includes/simulator/form.php'; ?>
  </div>
</main>

<?php include __DIR__ . '/includes/layout/footer-simulator.php'; ?>
