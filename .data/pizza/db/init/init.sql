CREATE TABLE IF NOT EXISTS items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    items JSON NOT NULL,
    quantity INT NOT NULL,
    email VARCHAR(255) NOT NULL,
    address VARCHAR(500) NOT NULL,
    status ENUM('pending', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO items (name, price) VALUES
    ('Margherita', 25.00),
    ('Pepperoni', 28.00),
    ('Hawajska', 27.00),
    ('Cztery Sery', 30.00),
    ('Wegetariańska', 26.00),
    ('Miłośników Mięsa', 32.00),
    ('BBQ Kurczak', 29.00),
    ('Owoce Morza', 35.00),
    ('Kalzone', 24.00),
    ('Mucha na dziko', 24.00);
