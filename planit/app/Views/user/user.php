<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@<?= htmlspecialchars($username) ?> - Planit</title>
    <link rel="icon" type="image/png" href="/imgs/planit-logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/react/index-CE6iOV0z.css">
    <link rel="stylesheet" href="/css/user.css">
</head>

<body>
    <div class="container mt-5">
        <div class="text-center">
            <div class="photo-container mx-auto">
                <?php if ($photo): ?>
                    <img src="data:image/jpeg;base64,<?= base64_encode($photo) ?>" alt="Foto de perfil" class="rounded-circle" data-bs-toggle="modal" data-bs-target="#photoModal">
                <?php else: ?>
                    <img src="/imgs/default-profile.png" alt="Foto por defecto" class="rounded-circle">
                <?php endif; ?>
            </div>
            <h2 class="mt-3"><?= htmlspecialchars($username) ?></h2>
            <p class="text-muted"><?= htmlspecialchars($location ?: 'Ubicación no especificada') ?></p>
            <div class="mt-4">
                <h3>Planit Points: <?= $points ?></h3>
                <p><?= $level ?></p>
            </div>

            <?php if (session()->get('user_id') && session()->get('user_id') != $user_id): ?>
                <?php if (!$hasPraised): ?>
                    <a href="/user/praise/<?= $user_id ?>" class="btn btn-success mt-2">Elogiar (+5 puntos)</a>
                <?php else: ?>
                    <p class="text-muted mt-2">Ya has elogiado a este usuario</p>
                <?php endif; ?>
            <?php endif; ?>
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
                <p><?= htmlspecialchars($username) ?> no tiene Planes activos</p>
            <?php else: ?>
                <?php foreach ($upcomingEvents as $event): ?>
                    <div class="col-md-4 mb-4">
                        <a href="/events/<?= $event['id'] ?>">
                            <div class="card h-100">
                                <?php if ($event['image']): ?>
                                    <img src="data:image/jpeg;base64,<?= base64_encode($event['image']) ?>" class="card-img-top" alt="Imagen del evento" loading="lazy">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($event['title']) ?></h5>
                                    <p class="card-text"><?= htmlspecialchars($event['description']) ?></p>
                                    <p class="card-text date"><small class="text-muted"><?= htmlspecialchars($event['date']) ?></small></p>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <h2 class="mt-5 mb-4">Planes Pasados</h2>
        <?php if (!empty($pastEvents)): ?>
            <div class="row">
                <?php foreach ($pastEvents as $event): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <?php if ($event['image']): ?>
                                <div class="card-img-top">
                                    <img src="data:image/jpeg;base64,<?= base64_encode($event['image']) ?>" class="card-img-top" alt="Imagen del evento" loading="lazy">
                                    <?php if ($event['canceled'] === 'si'): ?>
                                        <img src="/imgs/canceled.png" class="canceled-overlay" alt="Evento cancelado" loading="lazy">
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($event['title']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars($event['description']) ?></p>
                                <p class="card-text date"><small class="text-muted"><?= htmlspecialchars($event['date']) ?></small></p>
                                <div class="d-flex justify-content-around">
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if (empty($pastEvents)): ?>
            <p>No hay Planes pasados.</p>
        <?php endif; ?>
    </div>
    </div>
    <div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="photoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-body">
                <img src="data:image/jpeg;base64,<?= base64_encode($photo) ?>" alt="Foto de perfil ampliada" class="rounded-circle w-100" style="aspect-ratio: 1 / 1; object-fit: cover;">
                <h1 class="text-white text-center mt-3"><?= htmlspecialchars($username) ?></h1>
            </div>
        </div>
    </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>