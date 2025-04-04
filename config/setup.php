<?php
require_once 'database.php';

try {
    // Create users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('administrator', 'waiter', 'kasir', 'owner') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->exec($sql);

    // Create tables table
    $sql = "CREATE TABLE IF NOT EXISTS tables (
        id INT AUTO_INCREMENT PRIMARY KEY,
        table_number VARCHAR(10) NOT NULL,
        status ENUM('available', 'occupied') DEFAULT 'available',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->exec($sql);

    // Create products table
    $sql = "CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        stock INT NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->exec($sql);

    // Create orders table
    $sql = "CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        table_id INT,
        waiter_id INT,
        status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (table_id) REFERENCES tables(id),
        FOREIGN KEY (waiter_id) REFERENCES users(id)
    )";
    $conn->exec($sql);

    // Create order_items table
    $sql = "CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT,
        product_id INT,
        quantity INT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES orders(id),
        FOREIGN KEY (product_id) REFERENCES products(id)
    )";
    $conn->exec($sql);

    // Create transactions table
    $sql = "CREATE TABLE IF NOT EXISTS transactions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT,
        cashier_id INT,
        total_amount DECIMAL(10,2) NOT NULL,
        payment_method ENUM('cash', 'card') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES orders(id),
        FOREIGN KEY (cashier_id) REFERENCES users(id)
    )";
    $conn->exec($sql);

    // Insert default users
    $users = [
        ['username' => 'admin', 'password' => 'admin123', 'role' => 'administrator'],
        ['username' => 'kasir', 'password' => 'kasir123', 'role' => 'kasir'],
        ['username' => 'owner', 'password' => 'owner123', 'role' => 'owner'],
        ['username' => 'waiter1', 'password' => 'waiter123', 'role' => 'waiter'],
        ['username' => 'waiter2', 'password' => 'waiter123', 'role' => 'waiter']
    ];

    foreach ($users as $user) {
        $sql = "INSERT INTO users (username, password, role) 
                SELECT ?, ?, ?
                WHERE NOT EXISTS (SELECT 1 FROM users WHERE username = ?)";
        $stmt = $conn->prepare($sql);
        $hashedPassword = password_hash($user['password'], PASSWORD_DEFAULT);
        $stmt->execute([$user['username'], $hashedPassword, $user['role'], $user['username']]);
    }

    // Insert dummy tables
    $tables = ['A1', 'A2', 'A3', 'B1', 'B2', 'B3', 'C1', 'C2', 'C3'];
    foreach ($tables as $table) {
        $sql = "INSERT INTO tables (table_number) 
                SELECT ?
                WHERE NOT EXISTS (SELECT 1 FROM tables WHERE table_number = ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$table, $table]);
    }

    // Insert dummy products
    $products = [
        ['name' => 'Nasi Goreng', 'price' => 25000, 'stock' => 50],
        ['name' => 'Mie Goreng', 'price' => 23000, 'stock' => 50],
        ['name' => 'Ayam Bakar', 'price' => 35000, 'stock' => 30],
        ['name' => 'Sate Ayam', 'price' => 30000, 'stock' => 40],
        ['name' => 'Es Teh', 'price' => 5000, 'stock' => 100],
        ['name' => 'Es Jeruk', 'price' => 7000, 'stock' => 100],
        ['name' => 'Juice Alpukat', 'price' => 15000, 'stock' => 25],
        ['name' => 'Sop Iga', 'price' => 45000, 'stock' => 20]
    ];

    foreach ($products as $product) {
        $sql = "INSERT INTO products (name, price, stock) 
                SELECT ?, ?, ?
                WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$product['name'], $product['price'], $product['stock'], $product['name']]);
    }

    // Get waiter IDs
    $stmt = $conn->query("SELECT id FROM users WHERE role = 'waiter'");
    $waiterIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Get table IDs
    $stmt = $conn->query("SELECT id FROM tables");
    $tableIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Get product IDs
    $stmt = $conn->query("SELECT id, price FROM products");
    $products = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    // Get cashier ID
    $stmt = $conn->query("SELECT id FROM users WHERE role = 'kasir' LIMIT 1");
    $cashierId = $stmt->fetch(PDO::FETCH_COLUMN);

    // Insert dummy orders and related data
    for ($i = 0; $i < 10; $i++) {
        try {
            $conn->beginTransaction();

            // Create order
            $waiterId = $waiterIds[array_rand($waiterIds)];
            $tableId = $tableIds[array_rand($tableIds)];
            
            $stmt = $conn->prepare("INSERT INTO orders (table_id, waiter_id, status) VALUES (?, ?, ?)");
            $stmt->execute([$tableId, $waiterId, 'completed']);
            $orderId = $conn->lastInsertId();

            // Create order items
            $totalAmount = 0;
            $numItems = rand(1, 4); // Random 1-4 items per order
            $selectedProducts = array_rand($products, $numItems);
            if (!is_array($selectedProducts)) {
                $selectedProducts = [$selectedProducts];
            }

            foreach ($selectedProducts as $productId) {
                $quantity = rand(1, 3);
                $price = $products[$productId];
                $totalAmount += $quantity * $price;

                $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                $stmt->execute([$orderId, $productId, $quantity, $price]);
            }

            // Create transaction
            $stmt = $conn->prepare("INSERT INTO transactions (order_id, cashier_id, total_amount, payment_method) VALUES (?, ?, ?, ?)");
            $stmt->execute([$orderId, $cashierId, $totalAmount, rand(0, 1) ? 'cash' : 'card']);

            $conn->commit();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    // Insert some pending orders
    for ($i = 0; $i < 3; $i++) {
        try {
            $conn->beginTransaction();

            $waiterId = $waiterIds[array_rand($waiterIds)];
            $tableId = $tableIds[array_rand($tableIds)];
            
            $stmt = $conn->prepare("INSERT INTO orders (table_id, waiter_id, status) VALUES (?, ?, 'pending')");
            $stmt->execute([$tableId, $waiterId]);
            $orderId = $conn->lastInsertId();

            // Update table status
            $stmt = $conn->prepare("UPDATE tables SET status = 'occupied' WHERE id = ?");
            $stmt->execute([$tableId]);

            // Add order items
            $numItems = rand(1, 4);
            $selectedProducts = array_rand($products, $numItems);
            if (!is_array($selectedProducts)) {
                $selectedProducts = [$selectedProducts];
            }

            foreach ($selectedProducts as $productId) {
                $quantity = rand(1, 3);
                $price = $products[$productId];

                $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                $stmt->execute([$orderId, $productId, $quantity, $price]);
            }

            $conn->commit();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    echo "Database setup completed successfully with dummy data!";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>