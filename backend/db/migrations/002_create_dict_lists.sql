CREATE TABLE IF NOT EXISTS `dict_lists` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM(
        'custom',
        'favorites',
        'history'
    ) DEFAULT 'custom',
    name VARCHAR(255) NOT NULL,
    source_lang CHAR(5) NOT NULL,
    target_lang CHAR(5) NOT NULL,
    CONSTRAINT fk_dict_lists_user
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;