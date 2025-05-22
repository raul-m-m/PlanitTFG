<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planes Creados Por Ti - Planit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="/imgs/planit-logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/events.css">
</head>

<body>
    <div class="container mt-5 position-relative">
        <div class="container mt-5">
            <a href="/">
                <h1 class="mb-4"><img src="/imgs/planit-logo.png" width="48" height="48"> Planit</h1>
            </a>
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>
            <h2 class="mb-4">Planes creados por ti</h2>
            <div id="profile-dropdown"
                data-username="<?= htmlspecialchars($username) ?>"
                data-photo="<?= htmlspecialchars($photo) ?>"
                data-user-id="<?= htmlspecialchars($user_id) ?>">
            </div>
            <div class="row">
                <?php
                $currentDate = date("Y-m-d");
                $upcomingEvents = [];
                $pastEvents = [];

                foreach ($events as $event) {
                    if ($event['date'] >= $currentDate && $event['canceled'] !== 'si') {
                        $upcomingEvents[] = $event;
                    } else {
                        $pastEvents[] = $event;
                    }
                }
                ?>
                <h2 class="mt-5 mb-4">Planes Activos</h2>
                <?php if (empty($upcomingEvents)): ?>
                    <p>Todavia no has creado ningun Plan.</p>
                <?php else: ?>
                    <?php foreach ($upcomingEvents as $event): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <?php if ($event['image']): ?>
                                    <img src="data:image/jpeg;base64,<?= base64_encode($event['image']) ?>" class="card-img-top" alt="Imagen del evento" loading="lazy">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($event['title']) ?></h5>
                                    <p class="card-text"><?= htmlspecialchars($event['description']) ?></p>
                                    <p class="card-text date"><small class="text-muted"><?= htmlspecialchars($event['date']) ?></small></p>
                                    <div class="d-flex justify-content-around">
                                        <a href="/events/edit/<?= $event['id'] ?>"><button class="btn btn-success">Editar</button></a>
                                        <a href="/users/attendees/<?= $event['id'] ?>" class="btn btn-warning">Ver asistentes</a>
                                        <a href="#" class="btn btn-danger" onclick="showConfirmation('¿Estás seguro de que quieres cancelar este evento? Esta acción no se puede deshacer', '/events/cancel/<?= $event['id'] ?>')">Cancelar</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <h2 class="mt-5 mb-4">Planes Pasados</h2>
            <?php if (!empty($pastEvents)): ?>
                <div class="row">
                    <?php foreach ($pastEvents as $event): ?>
                        <div class="col-md-4 mb-4">
                            <?php if ($event['canceled'] === 'no'): ?>
                                <a href="/events/showComments/<?= $event['id'] ?>" class="text-decoration-none text-dark">
                            <?php endif; ?>
                                <div class="card h-100">
                                    <?php if ($event['image']): ?>
                                        <div class="card-img-top">
                                            <img src="data:image/jpeg;base64,<?= base64_encode($event['image']) ?>" class="card-img-top" alt="Imagen del evento" loading="lazy">
                                            <?php if ($event['canceled'] === 'si'): ?>
                                                <img src="/imgs/canceled.png" class="canceled-overlay" alt="Plan cancelado" loading="lazy">
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($event['title']) ?></h5>
                                        <p class="card-text"><?= htmlspecialchars($event['description']) ?></p>
                                        <p class="card-text date"><small class="text-muted"><?= htmlspecialchars($event['date']) ?></small></p>
                                    </div>
                                </div>
                            <?php if ($event['canceled'] === 'no'): ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>No hay planes pasados disponibles.</p>
            <?php endif; ?>
        </div>
        <?php include_once __DIR__ . '/../includes/confirm.php'; ?>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="/react/index-ypKMb6Ny.js"></script>
    </div>
</body>

</html>