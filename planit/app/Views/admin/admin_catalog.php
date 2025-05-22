<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador - Planit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="/imgs/planit-logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/catalog.css">
    <link rel="stylesheet" href="/css/events.css">
</head>

<body>
    <div class="container mt-5 position-relative">
        <a href="/">
            <h1 class="mb-4"><img src="/imgs/planit-logo.png" width="48" height="48"> Planit</h1>
        </a>
        <a href="/admin/users" class="btn btn-sm btn-warning">Gestionar Usuarios</a>
        <a href="admin/categories" class="btn btn-sm btn-warning">Gestionar Categorías</a>
        <a href="/admin-catalog" class="btn btn-sm btn-warning">Mostrar Todos</a>
        <form method="get" action="/admin-catalog">
            <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="Buscar por titulo de evento" name="evento">
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
        <h2 class="mb-4">Planes Activos</h2>
        <?php if ($user_id): ?>
            <div id="profile-dropdown"
                data-username="<?= htmlspecialchars($username) ?>"
                data-photo="<?= htmlspecialchars($photo) ?>"
                data-user-id="<?= htmlspecialchars($user_id) ?>">
            </div>
        <?php else: ?>
            <a href="/admin/users" class="btn btn-sm btn-warning">Gestionar Usuarios</a>
            <p><a href="/login" class="btn btn-secondary">Iniciar Sesión</a> | <a href="/register" class="btn btn-secondary">Registrarse</a></p>
        <?php endif; ?>
        <div class="row">
            <?php foreach ($events as $event): ?>
                <?php if ($event['date'] > date('Y-m-d') && $event['canceled'] !== 'si'): ?>
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <?php if ($event['image']): ?>
                                <img src="data:image/jpeg;base64,<?= base64_encode($event['image']) ?>" class="card-img-top" alt="Imagen del Plan" loading="lazy">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= esc($event['title']) ?></h5>
                                <p class="card-text">Fecha: <?= esc($event['date']) ?></p>
                                <p class="card-text">Categoría: <?= esc($event['category_name']) ?></p>
                                <div class="mt-2">
                                    <a href="/events/<?= $event['id'] ?>" class="btn btn-sm btn-primary">Ver</a>
                                    <a href="/events/edit/<?= $event['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                    <a href="#" class="btn btn-sm btn-danger" onclick="showConfirmation('¿Estás seguro de que quieres eliminar este evento?', '/events/delete/<?= $event['id'] ?>')">Eliminar</a>
                                    <a href="#" class="btn btn-sm btn-secondary" onclick="showConfirmation('¿Estás seguro de que quieres cancelar este evento?', '/events/cancel/<?= $event['id'] ?>')">Cancelar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <h2>Eventos Pasados</h2>
        <div class="row">
            <?php foreach ($events as $event): ?>
                <?php if ($event['date'] <= date('Y-m-d') || $event['canceled'] === 'si'): ?>
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <?php if ($event['image']): ?>
                                <div class="card-img-top">
                                    <img src="data:image/jpeg;base64,<?= base64_encode($event['image']) ?>" class="card-img-top" alt="Imagen del evento" loading="lazy">
                                    <?php if ($event['canceled'] === 'si'): ?>
                                        <img src="/imgs/canceled.png" class="canceled-overlay" alt="Plan cancelado" loading="lazy">
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= esc($event['title']) ?></h5>
                                <p class="card-text">Fecha: <?= esc($event['date']) ?></p>
                                <p class="card-text">Categoría: <?= esc($event['category_name']) ?></p>
                                <p class="card-text">Cancelado: <?= esc($event['canceled']) ?></p>
                                <div class="mt-2">
                                    <a href="/events/<?= $event['id'] ?>" class="btn btn-sm btn-primary">Ver</a>
                                    <a href="/events/edit/<?= $event['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                    <a href="/events/showComments/<?= $event['id'] ?>" class="btn btn-sm btn-info">Ver Comentarios</a>
                                    <a href="#" class="btn btn-sm btn-danger" onclick="showConfirmation('¿Estás seguro de que quieres eliminar este evento?', '/events/delete/<?= $event['id'] ?>')">Eliminar</a>
                                    <?php if ($event['canceled'] === 'si'): ?>
                                        <a href="#" class="btn btn-sm btn-success" onclick="showConfirmation('¿Estás seguro de que quieres rehabilitar este evento?', '/events/rehabilitate/<?= $event['id'] ?>')">Rehabilitar</a>
                                    <?php else: ?>
                                        <a href="#" class="btn btn-sm btn-secondary" onclick="showConfirmation('¿Estás seguro de que quieres cancelar este evento?', '/events/cancel/<?= $event['id'] ?>')">Rehabilitar</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
    <?php include_once __DIR__ . '/../includes/confirm.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="/react/index-ypKMb6Ny.js"></script>
</body>

</html>