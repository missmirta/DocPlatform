<?php
$title = 'Edit Rule — ' . ($tenantId ?? '');
ob_start();
?>
<div class="mb-3">
  <a href="/rules?tenant=<?= urlencode($tenantId ?? '') ?>" class="btn btn-outline-secondary btn-sm">&larr; Back to Rules</a>
</div>

<h1 class="h3 mb-4">Edit Rule</h1>

<?php if (!empty($errors)): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errors as $error): ?>
        <li><?= htmlspecialchars($error) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form method="POST" action="/rules/edit">
  <input type="hidden" name="tenant" value="<?= htmlspecialchars($tenantId ?? '') ?>">
  <input type="hidden" name="rule_id" value="<?= $rule->id ?>">
  <input type="hidden" name="rule_type" value="<?= htmlspecialchars($rule->ruleType->value) ?>">

  <div class="mb-3">
    <label class="form-label">Rule Type</label>
    <div>
      <span class="badge bg-secondary fs-6"><?= htmlspecialchars($schemas[$rule->ruleType->value]['label'] ?? $rule->ruleType->value) ?></span>
    </div>
  </div>

  <?php $ruleType = $rule->ruleType; ?>
  <?php if (isset($schemas[$ruleType->value])): ?>
    <h6 class="text-muted mb-3"><?= htmlspecialchars($schemas[$ruleType->value]['description'] ?? '') ?></h6>
    <?php foreach ($schemas[$ruleType->value]['parameters'] as $field => $param): ?>
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
        <?php $fieldValue = $displayParams[$field] ?? ''; ?>
        <?php if ($param['type'] === 'integer'): ?>
          <input type="number"
                 name="params[<?= htmlspecialchars($field) ?>]"
                 class="form-control"
                 min="<?= isset($param['minimum']) ? (int) $param['minimum'] : 1 ?>"
                 value="<?= htmlspecialchars((string) $fieldValue) ?>">
        <?php elseif ($param['type'] === 'array'): ?>
          <textarea name="params[<?= htmlspecialchars($field) ?>]"
                    class="form-control"
                    rows="3"><?= htmlspecialchars((string) $fieldValue) ?></textarea>
          <div class="form-text">One per line</div>
        <?php else: ?>
          <input type="text"
                 name="params[<?= htmlspecialchars($field) ?>]"
                 class="form-control"
                 value="<?= htmlspecialchars((string) $fieldValue) ?>">
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

  <button type="submit" class="btn btn-primary">Save Changes</button>
</form>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
