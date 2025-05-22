<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos Apuntados - Planit</title>
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
        <h2 class="mb-4">Eventos Apuntados por <?= esc($username) ?></h2>
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>
        <div class="row">
            <?php if (empty($events)): ?>
                <div class="col-12">
                    <p class="text-center">Este usuario no está apuntado a ningún evento.</p>
                </div>
            <?php else: ?>
                <?php foreach ($events as $event): ?>
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
                                    <a href="#" class="btn btn-sm btn-danger" onclick="showConfirmation('¿Estás seguro de que quieres eliminar a este usuario de este evento?', '/admin/removeFromEvent/<?= $userId ?>/<?= $event['id'] ?>')">Eliminar del Plan</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php include_once __DIR__ . '/../includes/confirm.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>