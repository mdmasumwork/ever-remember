CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stripe_payment_id VARCHAR(255) NOT NULL,
    user_name VARCHAR(255) NOT NULL,
    user_email VARCHAR(255),
    amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50),
    payment_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    content_id VARCHAR(255),
    content_type VARCHAR(50),
    feedback TEXT DEFAULT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (stripe_payment_id),
    INDEX (user_email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;