DO $$ 
BEGIN
    IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'list_type') THEN
        CREATE TYPE list_type AS ENUM ('custom', 'favorites', 'history');
    END IF;
END $$;

CREATE TABLE IF NOT EXISTS dict_lists (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL,
    type list_type DEFAULT 'custom',
    name VARCHAR(255) NOT NULL,
    source_lang CHAR(5) NOT NULL,
    target_lang CHAR(5) NOT NULL,
    icon VARCHAR(255) DEFAULT NULL,
    CONSTRAINT fk_dict_lists_user
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);