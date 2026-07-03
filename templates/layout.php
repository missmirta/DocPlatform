<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title ?? 'DocPlatform') ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
      <a class="navbar-brand" href="/">DocPlatform</a>
      <div class="navbar-nav me-auto">
        <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/rules') ? 'active' : '' ?>" href="/rules?tenant=<?= urlencode($tenantId ?? '') ?>">Rule Configuration</a>
        <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/upload') ? 'active' : '' ?>" href="/upload?tenant=<?= urlencode($tenantId ?? '') ?>">Upload Document</a>
      </div>
      <!-- Tenant selector form -->
      <form method="GET" action="" class="d-flex align-items-center gap-2">
        <!-- Preserve current page path (rules or upload) via JS on submit, fallback: action stays empty which goes to same path -->
        <label class="text-white mb-0 me-1 small">Tenant:</label>
        <select name="tenant" class="form-select form-select-sm" onchange="this.form.action=window.location.pathname; this.form.submit()">
          <?php foreach ($tenants ?? [] as $t): ?>
            <option value="<?= htmlspecialchars($t) ?>" <?= ($tenantId ?? '') === $t ? 'selected' : '' ?>>
              <?= htmlspecialchars($t) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </form>
    </div>
  </nav>

  <div class="container">
    <?php if (!empty($_GET['flash'])): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_GET['flash']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <?= $content ?? '' ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
