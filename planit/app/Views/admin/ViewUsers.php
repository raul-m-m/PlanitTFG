<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Lista de Usuarios - Planit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="/imgs/planit-logo.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-4">
        <h2>Lista de Usuarios</h2>
        <a href="/register/" class="btn btn-sm btn-warning">Registrar nuevo usuario</a>
        <a href="/admin/categories" class="btn btn-sm btn-warning">Gestionar Categorias</a>
        <a href="/admin-catalog" class="btn btn-sm btn-warning">Gestionar Eventos</a>
        <a href="/admin/users" class="btn btn-sm btn-warning">Mostrar Todos</a>
        <form method="get" action="/admin/users">
            <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="Buscar por nombre de usuario" name="username">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="submit">Buscar</button>
                </div>
            </div>
        </form>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre de usuario</th>
                    <th>Ubicación</th>
                    <th>Fecha de Registro</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="5" class="text-center">No hay usuarios registrados.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= esc($user['id']) ?></td>
                            <td><?= esc($user['username']) ?></td>
                            <td><?= esc($user['location']) ?></td>
                            <td><?= esc($user['created_at']) ?></td>
                            <td>
                                <a href="/profile/<?= $user['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                <a href="#" class="btn btn-sm btn-danger" onclick="showConfirmation('¿Estás seguro de que quieres eliminar este usuario?', '/users/delete/<?= $user['id'] ?>')">Eliminar</a>
                                <a href="/admin/viewUserEvents/<?= htmlspecialchars($user['id']) ?>" class="btn btn-sm btn-warning">Eventos creados por el usuario</a>
                                <a href="/admin/userAttendedEvents/<?= htmlspecialchars($user['id']) ?>" class="btn btn-sm btn-info">Planes donde está apuntado</a>
                                <a href="/admin/viewUserPoints/<?= htmlspecialchars($user['id']) ?>" class="btn btn-sm btn-info">Puntos del usuario</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php include_once __DIR__ . '/../includes/confirm.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>