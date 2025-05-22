<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - Planit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="/imgs/planit-logo.png">
    <link rel="stylesheet" href="/react/index-CE6iOV0z.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link rel="stylesheet" href="/css/edit.css">
</head>

<body>
    <div class="container mt-5 position-relative">
        <div class="container mt-5">
            <a href="/">
                <h1 class="mb-4"><img src="/imgs/planit-logo.png" width="48" height="48"> Planit</h1>
            </a>
            <div id="profile-dropdown"
                data-username="<?= htmlspecialchars($username) ?>"
                data-photo="<?= htmlspecialchars($photo) ?>"
                data-user-id="<?= htmlspecialchars($user_id) ?>">
            </div>
            <h2 class="mb-4"><?= htmlspecialchars($title) ?></h2>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <?php if ($image): ?>
                <div class="mb-4 text-center">
                    <img src="data:image/jpeg;base64,<?= htmlspecialchars($image) ?>" alt="Imagen del evento" class="img-fluid" style="max-width: 300px;" loading="lazy">
                </div>
            <?php endif; ?>

            <?php if (!empty($comments)): ?>
                <div class="list-group mb-4">
                    <?php foreach ($comments as $comment): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-start">
                            <div>
                                <a href="<?= htmlspecialchars('/user/' . $comment['username']) ?>" class="text-decoration-none">
                                    <h5 class="mb-1">@<?= htmlspecialchars($comment['username']) ?></h5>
                                </a>
                                <p class="mb-1"><?= htmlspecialchars($comment['comment']) ?></p>
                                <small class="text-muted">Publicado el: <?= date('d/m/Y H:i', strtotime($comment['created_at'])) ?></small>
                            </div>
                            <?php if ($comment['id_user'] == $session_id  || $isAdmin || $event_creator == $session_id): ?>
                                <a href="/events/deleteComment/<?= $comment['id'] ?>" class="btn btn-danger btn-sm" title="Eliminar comentario">
                                    <i class="bi bi-trash"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>No hay comentarios para este evento.</p>
            <?php endif; ?>

            <?php if ($can_comment || $isAdmin): ?>
                <form action="/events/addComment/<?= $event_id ?>" method="post">
                    <div class="mb-3">
                        <label for="comment" class="form-label">Tu comentario:</label>
                        <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Publicar comentario</button>
                </form>
            <?php elseif ($event_date >= date('Y-m-d')): ?>
                <p>No puedes comentar hasta que el evento haya pasado (fecha del evento: <?= htmlspecialchars($event_date) ?>).</p>
            <?php else: ?>
                <p>No puedes comentar porque no asististe a este evento.</p>
            <?php endif; ?>

            <a href="/my-events" class="btn btn-secondary mt-4">Volver</a>
        </div>
        <?php include_once __DIR__ . '/../includes/confirm.php'; ?>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="/react/index-ypKMb6Ny.js"></script>

</body>
</body>

</html>