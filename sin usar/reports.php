<?php
require_once 'auth_check.php';
check_auth();

// Generate sample report data
$reports = [
    [
        'id' => 1,
        'title' => 'Ventas Mensuales',
        'date' => '2024-01-15',
        'status' => 'Completado',
        'size' => '2.4 MB'
    ],
    [
        'id' => 2,
        'title' => 'Inventario General',
        'date' => '2024-01-10',
        'status' => 'Completado',
        'size' => '1.8 MB'
    ],
    [
        'id' => 3,
        'title' => 'Análisis de Usuarios',
        'date' => '2024-01-08',
        'status' => 'En Proceso',
        'size' => '0.9 MB'
    ]
];

echo get_html_header('Reportes', 'reports.php');
?>

<div class="card">
    <h2>Centro de Reportes</h2>
    <p>Accede a todos tus reportes generados aquí.</p>
</div>

<div class="card">
    <h3>Reportes Disponibles</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Tamaño</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reports as $report): ?>
            <tr>
                <td><?php echo $report['id']; ?></td>
                <td><?php echo htmlspecialchars($report['title']); ?></td>
                <td><?php echo $report['date']; ?></td>
                <td>
                    <?php if ($report['status'] === 'Completado'): ?>
                        <span style="color: green;">✅ <?php echo $report['status']; ?></span>
                    <?php else: ?>
                        <span style="color: orange;">⏳ <?php echo $report['status']; ?></span>
                    <?php endif; ?>
                </td>
                <td><?php echo $report['size']; ?></td>
                <td>
                    <?php if ($report['status'] === 'Completado'): ?>
                        <a href="#" onclick="alert('Funcionalidad de descarga aquí')">📥 Descargar</a>
                    <?php else: ?>
                        <span style="color: #ccc;">Esperando...</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="card warning">
    <h3>Generar Nuevo Reporte</h3>
    <p>Selecciona el tipo de reporte que deseas generar:</p>
    <button onclick="alert('Generando reporte de ventas...')">📊 Reporte de Ventas</button>
    <button onclick="alert('Generando reporte de inventario...')">📦 Reporte de Inventario</button>
    <button onclick="alert('Generando reporte de usuarios...')">👥 Reporte de Usuarios</button>
</div>

<?php echo get_html_footer(); ?>