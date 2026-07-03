CREATE TABLE IF NOT EXISTS validation_rules (
    id         INTEGER PRIMARY KEY,
    rule_type  VARCHAR(100) NOT NULL,
    parameters TEXT         NOT NULL DEFAULT '{}',
    sort_order INTEGER      NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS idx_vr_sort_order
    ON validation_rules (sort_order);
