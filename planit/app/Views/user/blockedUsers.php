<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Usuarios Bloqueados - Planit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="/imgs/planit-logo.png">
    <link rel="stylesheet" href="/css/catalog.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5 position-relative">
        <h2>Usuarios Bloqueados</h2>
        <?php if ($user_id): ?>
            <div id="profile-dropdown"
                data-username="<?= htmlspecialchars($username) ?>"
                data-photo="<?= htmlspecialchars($photo) ?>"
                data-user-id="<?= htmlspecialchars($user_id) ?>">
            </div>
        <?php else: ?>
            <p><a href="/login" class="btn btn-secondary">Iniciar Sesión</a> | <a href="/register" class="btn btn-secondary">Registrarse</a></p>
        <?php endif; ?>
        <a href="/" class="btn btn-sm btn-warning mb-3">Volver al inicio</a>

        <form method="get" action="/users/blockedUsers">
            <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="Buscar usuario para bloquear" name="username">
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
                    <th>Nombre de usuario</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($users)): ?>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="2" class="text-center">No se encontraron usuarios.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= esc($user['username']) ?></td>
                                <td>
                                    <?php
                                    $isBlocked = false;
                                    if (isset($blocked_ids)) {
                                        foreach ($blocked_ids as $blocked) {
                                            if ($blocked['id_blocked'] == $user['id']) {
                                                $isBlocked = true;
                                                break;
                                            }
                                        }
                                    }
                                    ?>
                                    <?php if ($isBlocked): ?>
                                        <a href="#" class="btn btn-sm btn-danger" onclick="showConfirmation('¿Estás seguro de que quieres desbloquear a este usuario?', '/user/unblock/<?= $user['id'] ?>')">Desbloquear</a>

                                    <?php else: ?>
                                        <a href="#" class="btn btn-sm btn-primary" onclick="showConfirmation('¿Estás seguro de que quieres bloquear a este usuario?', '/user/block/<?= $user['id'] ?>')">Bloquear</a>
            
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php elseif (isset($blocked_users)): ?>
                    <?php if (empty($blocked_users)): ?>
                        <tr>
                            <td colspan="2" class="text-center">No tienes usuarios bloqueados.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($blocked_users as $user): ?>
                            <tr>
                                <td><?= esc($user['username']) ?></td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-danger" onclick="showConfirmation('¿Estás seguro de que quieres desbloquear a este usuario?', '/user/unblock/<?= $user['id'] ?>')">Desbloquear</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php include_once __DIR__ . '/../includes/confirm.php'; ?>
    <script src="/react/index-ypKMb6Ny.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>