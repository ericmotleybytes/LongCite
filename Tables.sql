CREATE TABLE IF NOT EXISTS longcite_citation (
    longcite_guid char(32),
    longcite_id   varchar(255),
    longcite_page varchar(255),
    longcite_json varchar(20000),
    UNIQUE KEY longcite_guid_pk  (longcite_guid),
    KEY        longcite_id_idx   (longcite_id),
    KEY        longcite_page_idx (longcite_page)
);
