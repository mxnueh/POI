<?php
require_once 'auth_check.php';
check_auth();

// Simulate products data
$products = [
    ['id' => 1, 'name' => 'Laptop Dell XPS', 'price' => 1299.99, 'stock' => 15],
    ['id' => 2, 'name' => 'iPhone 15 Pro', 'price' => 999.99, 'stock' => 8],
    ['id' => 3, 'name' => 'Samsung 4K Monitor', 'price' => 399.99, 'stock' => 12],
    ['id' => 4, 'name' => 'Mechanical Keyboard', 'price' => 149.99, 'stock' => 25],
    ['id' => 5, 'name' => 'Wireless Mouse', 'price' => 79.99, 'stock' => 30]
];

echo get_html_header('Productos', 'products.php');
?>

<div class="card">
    <h2>Lista de Productos</h2>
    <p>Gestiona tu inventario de productos aquí.</p>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
            <tr>
                <td><?php echo $product['id']; ?></td>
                <td><?php echo htmlspecialchars($product['name']); ?></td>
                <td>$<?php echo number_format($product['price'], 2); ?></td>
                <td><?php echo $product['stock']; ?></td>
                <td>
                    <?php if ($product['stock'] > 10): ?>
                        <span style="color: green;">✅ En Stock</span>
                    <?php elseif ($product['stock'] > 0): ?>
                        <span style="color: orange;">⚠️ Poco Stock</span>
                    <?php else: ?>
                        <span style="color: red;">❌ Agotado</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="card info">
    <h3>Estadísticas de Inventario</h3>
    <p><strong>Total de Productos:</strong> <?php echo count($products); ?></p>
    <p><strong>Valor Total del Inventario:</strong> $<?php echo number_format(array_sum(array_map(function($p) { return $p['price'] * $p['stock']; }, $products)), 2); ?></p>
    <p><strong>Productos con Bajo Stock:</strong> <?php echo count(array_filter($products, function($p) { return $p['stock'] <= 10 && $p['stock'] > 0; })); ?></p>
</div>

<?php echo get_html_footer(); ?>