CREATE TABLE IF NOT EXISTS tenants (
    id        INTEGER PRIMARY KEY,
    tenant_id VARCHAR(255) NOT NULL UNIQUE,
    db_path   TEXT         NOT NULL UNIQUE
);
