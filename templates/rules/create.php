<?php
$title = 'Add Rule — ' . ($tenantId ?? '');
ob_start();
?>
<div class="mb-3">
  <a href="/rules?tenant=<?= urlencode($tenantId ?? '') ?>" class="btn btn-outline-secondary btn-sm">&larr; Back to Rules</a>
</div>

<h1 class="h3 mb-4">Add Rule</h1>

<?php if (!empty($errors)): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errors as $error): ?>
        <li><?= htmlspecialchars($error) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form method="POST" action="/rules/create">
  <input type="hidden" name="tenant" value="<?= htmlspecialchars($tenantId ?? '') ?>">

  <div class="mb-3">
    <label for="rule_type_select" class="form-label">Rule Type</label>
    <select name="rule_type" id="rule_type_select" class="form-select">
      <?php foreach ($schemas as $type => $schema): ?>
        <option value="<?= htmlspecialchars($type) ?>"
          <?= (($old['rule_type'] ?? '') === $type) ? 'selected' : '' ?>>
          <?= htmlspecialchars($schema['label']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <?php foreach ($schemas as $type => $schema): ?>
    <div id="params_<?= htmlspecialchars($type) ?>" class="params-section" style="display:none">
      <h6 class="text-muted"><?= htmlspecialchars($schema['description'] ?? '') ?></h6>
      <?php foreach ($schema['parameters'] as $field => $param): ?>
        <div class="mb-3">
          <label class="form-label">
            <?= htmlspecialchars($field) ?>
            <?php if (!empty($param['required'])): ?>
              <span class="text-danger">*</span>
            <?php endif; ?>
          </label>
          <?php if (!empty($param['description'])): ?>
            <div class="form-text mb-1"><?= htmlspecialchars($param['description']) ?></div>
          <?php endif; ?>
          <?php
          $oldValue = (isset($old) && ($old['rule_type'] ?? '') === $type)
            ? ($old['params'][$field] ?? '')
            : '';
          ?>
          <?php if ($param['type'] === 'integer'): ?>
            <input type="number"
                   name="params[<?= htmlspecialchars($field) ?>]"
                   class="form-control"
                   min="<?= isset($param['minimum']) ? (int) $param['minimum'] : 1 ?>"
                   value="<?= htmlspecialchars((string) $oldValue) ?>">
          <?php elseif ($param['type'] === 'array'): ?>
            <textarea name="params[<?= htmlspecialchars($field) ?>]"
                      class="form-control"
                      rows="3"><?= htmlspecialchars((string) $oldValue) ?></textarea>
            <div class="form-text">One per line</div>
          <?php else: ?>
            <input type="text"
                   name="params[<?= htmlspecialchars($field) ?>]"
                   class="form-control"
                   value="<?= htmlspecialchars((string) $oldValue) ?>">
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endforeach; ?>

  <button type="submit" class="btn btn-primary">Add Rule</button>
</form>

<script>
function showParams(type) {
  document.querySelectorAll('.params-section').forEach(el => el.style.display = 'none');
  const el = document.getElementById('params_' + type);
  if (el) el.style.display = 'block';
}
const sel = document.getElementById('rule_type_select');
sel.addEventListener('change', () => showParams(sel.value));
// Show the initially selected type
showParams(sel.value);
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
