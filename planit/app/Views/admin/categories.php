<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Administrar Categorias - Planit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="/imgs/planit-logo.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-4">
        <h2>Todas las categorias</h2>
        <a href="/admin/createCategory" class="btn btn-sm btn-warning">Crear nueva Categoría</a>
        <a href="/admin/users" class="btn btn-sm btn-warning">Gestionar Usuarios</a>
        <a href="/admin-catalog" class="btn btn-sm btn-warning">Gestionar Eventos</a>
        <a href="/admin/categories" class="btn btn-sm btn-warning">Mostrar Todos</a>
        <form method="get" action="/admin/categories">
            <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="Buscar por nombre" name="category">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="submit">Buscar</button>
                </div>
            </div>
        </form>
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?= esc($category['id']) ?></td>
                        <td><?= esc($category['nombre']) ?></td>
                        <td>
                            <a href="#" class="btn btn-sm btn-danger" onclick="showConfirmation('¿Estás seguro de que quieres eliminar esta categoria?', '/admin/deleteCategory/<?= $category['id'] ?>')">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php include_once __DIR__ . '/../includes/confirm.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>