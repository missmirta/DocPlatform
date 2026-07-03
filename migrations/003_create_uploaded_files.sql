CREATE TABLE IF NOT EXISTS uploaded_files (
    id          INTEGER PRIMARY KEY,
    file_id     VARCHAR(255) NOT NULL UNIQUE,
    size_bytes  INTEGER      NOT NULL DEFAULT 0,
    metadata    TEXT         NOT NULL DEFAULT '{}',
    uploaded_at TEXT         NOT NULL DEFAULT (strftime('%Y-%m-%dT%H:%M:%SZ', 'now'))
);

CREATE INDEX IF NOT EXISTS idx_uf_uploaded_at
    ON uploaded_files (uploaded_at);
