<?php
$title = 'Rule Configuration — ' . ($tenantId ?? '');
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3">Rule Configuration</h1>
  <a href="/rules/create?tenant=<?= urlencode($tenantId ?? '') ?>" class="btn btn-primary">Add Rule</a>
</div>

<?php if (empty($rules)): ?>
  <div class="alert alert-info">No rules configured yet. Add your first rule.</div>
<?php else: ?>
  <table class="table table-bordered table-striped">
    <thead class="table-dark">
      <tr>
        <th>#</th>
        <th>Rule Type</th>
        <th>Parameters</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($rules as $index => $rule): ?>
        <tr>
          <td><?= $index + 1 ?></td>
          <td><?= htmlspecialchars($schemas[$rule->ruleType->value]['label'] ?? $rule->ruleType->value) ?></td>
          <td>
            <?php foreach ($rule->parameters as $key => $value): ?>
              <div>
                <strong><?= htmlspecialchars($key) ?>:</strong>
                <?php if (is_array($value)): ?>
                  <?= htmlspecialchars(implode(', ', $value)) ?>
                <?php else: ?>
                  <?= htmlspecialchars((string) $value) ?>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </td>
          <td>
            <a href="/rules/edit?tenant=<?= urlencode($tenantId ?? '') ?>&id=<?= (int) $rule->id ?>" class="btn btn-sm btn-secondary me-1">Edit</a>
            <form method="POST" action="/rules/delete" class="d-inline" onsubmit="return confirm('Delete this rule?')">
              <input type="hidden" name="tenant" value="<?= htmlspecialchars($tenantId ?? '') ?>">
              <input type="hidden" name="rule_id" value="<?= (int) $rule->id ?>">
              <button type="submit" class="btn btn-sm btn-danger">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
