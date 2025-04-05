<?php
require_once 'database.php';

try {
    // Create users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('administrator', 'kasir', 'waiter', 'owner') NOT NULL,
        name VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->exec($sql);

    // Create tables table
    $sql = "CREATE TABLE IF NOT EXISTS tables (
        id INT AUTO_INCREMENT PRIMARY KEY,
        table_number VARCHAR(10) UNIQUE NOT NULL,
        status ENUM('available', 'occupied') DEFAULT 'available',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->exec($sql);

    // Create products table
    $sql = "CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) UNIQUE NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        stock INT NOT NULL DEFAULT 0,
        stock_minimum INT NOT NULL DEFAULT 5,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->exec($sql);

    // Create orders table
    $sql = "CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        table_id INT NOT NULL,
        waiter_id INT NOT NULL,
        status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (table_id) REFERENCES tables(id),
        FOREIGN KEY (waiter_id) REFERENCES users(id)
    )";
    $conn->exec($sql);

    // Create order_items table
    $sql = "CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id),
        FOREIGN KEY (product_id) REFERENCES products(id)
    )";
    $conn->exec($sql);

    // Create transactions table
    $sql = "CREATE TABLE IF NOT EXISTS transactions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        total_amount DECIMAL(10,2) NOT NULL,
        payment_method ENUM('cash', 'card', 'qris') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES orders(id)
    )";
    $conn->exec($sql);

    // New tables for additional features
    $conn->exec("
        CREATE TABLE IF NOT EXISTS notifications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            type ENUM('order', 'stock', 'system') NOT NULL,
            message TEXT NOT NULL,
            is_read BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    $conn->exec("
        CREATE TABLE IF NOT EXISTS activity_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            action VARCHAR(50) NOT NULL,
            description TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
        )
    ");

    $conn->exec("
        CREATE TABLE IF NOT EXISTS cashier_shifts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            start_time TIMESTAMP NOT NULL,
            end_time TIMESTAMP NULL,
            total_transactions INT DEFAULT 0,
            total_amount DECIMAL(10,2) DEFAULT 0,
            status ENUM('active', 'closed') DEFAULT 'active',
            FOREIGN KEY (user_id) REFERENCES users(id)
        )
    ");

    $conn->exec("
        CREATE TABLE IF NOT EXISTS table_reservations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            table_id INT NOT NULL,
            customer_name VARCHAR(100) NOT NULL,
            customer_phone VARCHAR(20) NOT NULL,
            reservation_time TIMESTAMP NOT NULL,
            party_size INT NOT NULL,
            status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (table_id) REFERENCES tables(id)
        )
    ");

    // Insert default users
    $users = [
        ['admin', password_hash('admin123', PASSWORD_DEFAULT), 'administrator', 'Administrator'],
        ['kasir', password_hash('kasir123', PASSWORD_DEFAULT), 'kasir', 'Kasir'],
        ['owner', password_hash('owner123', PASSWORD_DEFAULT), 'owner', 'Owner'],
        ['waiter1', password_hash('waiter123', PASSWORD_DEFAULT), 'waiter', 'Waiter 1'],
        ['waiter2', password_hash('waiter123', PASSWORD_DEFAULT), 'waiter', 'Waiter 2']
    ];

    foreach ($users as $user) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$user[0]]);
        if (!$stmt->fetch()) {
            $stmt = $conn->prepare("INSERT INTO users (username, password, role, name) VALUES (?, ?, ?, ?)");
            $stmt->execute($user);
        }
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
            $stmt = $conn->prepare("INSERT INTO transactions (order_id, total_amount, payment_method) VALUES (?, ?, ?)");
            $stmt->execute([$orderId, $totalAmount, rand(0, 2) ? 'cash' : (rand(0, 1) ? 'card' : 'qris')]);

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

    // Insert dummy notifications
    $notificationTypes = ['order', 'stock', 'system'];
    $notificationMessages = [
        'order' => [
            'Pesanan baru dari Meja A1',
            'Pesanan dari Meja B2 telah selesai',
            'Pesanan dari Meja C3 dibatalkan'
        ],
        'stock' => [
            'Stok Nasi Goreng hampir habis',
            'Stok Ayam Bakar perlu ditambah',
            'Stok Es Teh sudah diisi ulang'
        ],
        'system' => [
            'Backup database berhasil',
            'Update sistem selesai',
            'Maintenance terjadwal besok'
        ]
    ];

    foreach ($notificationTypes as $type) {
        foreach ($notificationMessages[$type] as $message) {
            $stmt = $conn->prepare("INSERT INTO notifications (type, message, is_read) VALUES (?, ?, ?)");
            $stmt->execute([$type, $message, rand(0, 1)]);
        }
    }

    // Insert dummy activity logs
    $actions = ['login', 'logout', 'create', 'update', 'delete'];
    $descriptions = [
        'login' => 'User login ke sistem',
        'logout' => 'User logout dari sistem',
        'create' => 'Membuat data baru',
        'update' => 'Mengubah data',
        'delete' => 'Menghapus data'
    ];

    // Get all user IDs
    $stmt = $conn->query("SELECT id FROM users");
    
    $userIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($userIds as $userId) {
        // Create 3-5 activity logs per user
        $numLogs = rand(3, 5);
        for ($i = 0; $i < $numLogs; $i++) {
            $action = $actions[array_rand($actions)];
            $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, description) VALUES (?, ?, ?)");
            $stmt->execute([$userId, $action, $descriptions[$action]]);
        }
    }

    // Insert dummy cashier shifts
    // Get cashier IDs
    $stmt = $conn->query("SELECT id FROM users WHERE role = 'kasir'");
    $cashierIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($cashierIds as $cashierId) {
        // Create 2-3 shifts per cashier
        $numShifts = rand(2, 3);
        for ($i = 0; $i < $numShifts; $i++) {
            $startTime = date('Y-m-d H:i:s', strtotime("-" . rand(1, 7) . " days"));
            $endTime = date('Y-m-d H:i:s', strtotime($startTime . " +" . rand(4, 8) . " hours"));
            $totalTransactions = rand(5, 20);
            $totalAmount = $totalTransactions * rand(50000, 200000);
            
            $stmt = $conn->prepare("INSERT INTO cashier_shifts (user_id, start_time, end_time, total_transactions, total_amount, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$cashierId, $startTime, $endTime, $totalTransactions, $totalAmount, 'closed']);
        }
        
        // Create one active shift
        $startTime = date('Y-m-d H:i:s', strtotime("-" . rand(1, 3) . " hours"));
        $totalTransactions = rand(1, 10);
        $totalAmount = $totalTransactions * rand(20000, 100000);
        
        $stmt = $conn->prepare("INSERT INTO cashier_shifts (user_id, start_time, total_transactions, total_amount, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$cashierId, $startTime, $totalTransactions, $totalAmount, 'active']);
    }

    // Insert dummy table reservations
    // Get table IDs
    $stmt = $conn->query("SELECT id FROM tables");
    $tableIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $customerNames = ['Budi Santoso', 'Siti Rahayu', 'Ahmad Hidayat', 'Dewi Kusuma', 'Rudi Hartono'];
    $customerPhones = ['081234567890', '082345678901', '083456789012', '084567890123', '085678901234'];
    $reservationStatuses = ['pending', 'confirmed', 'cancelled'];
    
    foreach ($tableIds as $tableId) {
        // Create 1-2 reservations per table
        $numReservations = rand(1, 2);
        for ($i = 0; $i < $numReservations; $i++) {
            $customerName = $customerNames[array_rand($customerNames)];
            $customerPhone = $customerPhones[array_rand($customerPhones)];
            $reservationTime = date('Y-m-d H:i:s', strtotime("+" . rand(1, 7) . " days"));
            $partySize = rand(2, 8);
            $status = $reservationStatuses[array_rand($reservationStatuses)];
            
            $stmt = $conn->prepare("INSERT INTO table_reservations (table_id, customer_name, customer_phone, reservation_time, party_size, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$tableId, $customerName, $customerPhone, $reservationTime, $partySize, $status]);
        }
    }

    echo "Database setup completed successfully with dummy data!";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>