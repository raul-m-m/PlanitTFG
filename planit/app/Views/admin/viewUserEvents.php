<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos de Usuario - Planit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="/imgs/planit-logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/catalog.css">
</head>

<body>
    <div class="container mt-5 position-relative">
        <a href="/">
            <h1 class="mb-4"><img src="/imgs/planit-logo.png" width="48" height="48"> Planit</h1>
        </a>
        <a href="/admin/users" class="btn btn-sm btn-warning">Volver a Usuarios</a>

        <h2 class="mb-4">Eventos de <?= htmlspecialchars($username) ?></h2>

        <h3>Eventos Activos</h3>
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
                                <p class="card-text">Categoría: <?= isset($event['category_name']) ? esc($event['category_name']) : 'Sin categoría' ?></p>
                                <div class="mt-2">
                                    <a href="/events/edit/<?= $event['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                    <a href="#" class="btn btn-sm btn-danger" onclick="showConfirmation('¿Estás seguro de que quieres eliminar este evento?', '/events/delete/<?= $event['id'] ?>')">Eliminar</a>
                                    <a href="/events/cancel/<?= $event['id'] ?>" class="btn btn-sm btn-secondary">Cancelar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <h3>Eventos Pasados</h3>
        <div class="row">
            <?php foreach ($events as $event): ?>
                <?php if ($event['date'] <= date('Y-m-d') || $event['canceled'] === 'si'): ?>
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <?php if ($event['image']): ?>
                                <img src="data:image/jpeg;base64,<?= base64_encode($event['image']) ?>" class="card-img-top" alt="Imagen del Plan" loading="lazy">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= esc($event['title']) ?></h5>
                                <p class="card-text">Fecha: <?= esc($event['date']) ?></p>
                                <p class="card-text">Categoría: <?= isset($event['category_name']) ? esc($event['category_name']) : 'Sin categoría' ?></p>
                                <p class="card-text">Cancelado: <?= esc($event['canceled']) ?></p>
                                <div class="mt-2">
                                    <a href="/events/edit/<?= $event['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                    <a href="#" class="btn btn-sm btn-danger" onclick="showConfirmation('¿Estás seguro de que quieres eliminar este evento?', '/events/delete/<?= $event['id'] ?>')">Eliminar</a>
                                    <?php if ($event['canceled'] === 'si'): ?>
                                        <a href="/events/rehabilitate/<?= $event['id'] ?>" class="btn btn-sm btn-success">Rehabilitar</a>
                                    <?php else: ?>
                                        <a href="/events/cancel/<?= $event['id'] ?>" class="btn btn-sm btn-secondary">Cancelar</a>
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
</body>

</html>