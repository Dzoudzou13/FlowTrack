<?php

declare(strict_types=1);

$scripts = $scripts ?? [];
?>
    <?php foreach ($scripts as $script): ?>
      <script src="<?= htmlspecialchars(asset($script), ENT_QUOTES, 'UTF-8') ?>"></script>
    <?php endforeach; ?>
  </body>
</html>
