-- Create promo codes table
CREATE TABLE IF NOT EXISTS promo_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255),
    discount_percent DECIMAL(5,2) DEFAULT 0,
    discount_amount DECIMAL(10,2) DEFAULT 0,
    valid_from DATETIME NOT NULL,
    valid_until DATETIME NOT NULL,
    max_uses INT DEFAULT NULL,
    current_uses INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert sample promo codes
INSERT INTO promo_codes 
    (code, description, discount_percent, discount_amount, valid_from, valid_until, max_uses, is_active)
VALUES 
    ('WELCOME50', '50% off for new users', 50, 0, '2023-01-01 00:00:00', '2030-12-31 23:59:59', 1000, 1),
    ('FREECONTENT', 'Free content for promotional events', 100, 0, '2023-01-01 00:00:00', '2030-12-31 23:59:59', 100, 1),
    ('FIXED5', '$5 off any purchase', 0, 5, '2023-01-01 00:00:00', '2030-12-31 23:59:59', NULL, 1);
