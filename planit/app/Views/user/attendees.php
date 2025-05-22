<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planit - Asistentes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="/imgs/planit-logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/catalog.css">
    <link rel="stylesheet" href="/css/attendees.css">
</head>

<body>
    <div class="container mt-5 position-relative">
        <a href="/">
            <h1 class="mb-4"><img src="/imgs/planit-logo.png" width="48" height="48"> Planit</h1>
        </a>
        <h2 class="mb-4">Asistentes del Evento</h2>
        <div class="row">
            <?php if (count($attendees) == 0): ?>
                <h6>Nadie se ha apuntado todavía.</h6>
            <?php endif; ?>
            <?php foreach ($attendees as $attendee): ?>
                <div class="col-md-4 mb-4">
                    <a href="/user/<?= htmlspecialchars($attendee['username']) ?>" class="text-decoration-none text-dark">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <img
                                    src="data:image/jpeg;base64,<?= base64_encode($attendee['photo']) ?>"
                                    alt="Foto de perfil"
                                    class="profile-photo">
                                <h5 class="card-title mt-3"><?= htmlspecialchars($attendee['username']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars($attendee['location']) ?></p>
                                <?php if (isset($isCreator) && $isCreator): ?>
                                    <a href="#" class="btn btn-danger mx-5" onclick="showConfirmation('¿Estás seguro de que quieres bloquear a <?= htmlspecialchars($attendee['username']) ?>?', '/user/block/<?= htmlspecialchars($attendee['id']) ?>')">Bloquear</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php include_once __DIR__ . '/../includes/confirm.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>