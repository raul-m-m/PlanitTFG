<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $event['title'] ?> - Planit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="/imgs/planit-logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="/react/index-CE6iOV0z.css">
    <link rel="stylesheet" href="/css/show.css">
</head>

<body>

    <div class="container mt-5 position-relative">
        <a href="/">
            <h1 class="mb-4"><img src="/imgs/planit-logo.png" width="48" height="48"> Planit</h1>

        </a>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <h2 class="mb-4"><?= $event['title'] ?></h2>
        <?php if ($user_id): ?>
            <div id="profile-dropdown"
                data-username="<?= htmlspecialchars($username) ?>"
                data-photo="<?= htmlspecialchars($photo) ?>"
                data-user-id="<?= $user_id ?>"></div>
        <?php else: ?>
            <p><a href="/login" class="btn btn-secondary">Iniciar Sesión</a> | <a href="/register" class="btn btn-secondary">Registrarse</a></p>
        <?php endif; ?>
        <div class="row">
            <div class="col-md-6">
                <?php if ($event['image']): ?>
                    <img src="data:image/jpeg;base64,<?= base64_encode($event['image']) ?>" class="event-image mb-3" alt="Imagen del evento" style="height: 400px; width: 100%; object-fit: cover;">
                <?php endif; ?>
                <div class="description-box"><?= $event['description'] ?></div>
                <p><?= $event['date'] ?></p>
                <p><?= $event['hour'] ?></p>
                <p><strong>Categoría:</strong> <?= $event['category'] ?></p>
                <p><strong>Aforo:</strong> <?= $event['capacity'] ?: 'Sin límite' ?></p>
                <p>
                    <strong>Creado por:</strong>
                    <a href="<?= htmlspecialchars('/user/' . $creator_username) ?>" class="text-decoration-none">
                        @<?= htmlspecialchars($creator_username) ?><span class="photo-container">
                            <img src="data:image/jpeg;base64,<?= $creator_photo ?>" alt="Foto de perfil del creador" class="rounded-circle" loading="lazy">
                        </span>
                    </a>
                </p>
                <?php if (isset($attendees_count) && $attendees_count == 0): ?>
                    <p><strong>Se el primero en apuntarte</strong></p>
                <?php endif; ?>
                <?php if (isset($attendees_count) && $attendees_count > 0): ?>
                    <p><strong>
                            <?= $attendees_count ?> <?= $attendees_count === 1 ? 'persona va' : 'personas van' ?><?= $attendees_count === 1 ? '' : 's' ?> a este Plan
                        </strong></p>
                    <p class="text-danger">
                        <?php if ($attendees_count < $attendees_count * 0.9): ?>
                            ¡Últimas plazas disponibles!
                        <?php endif; ?>
                    </p>
                <?php endif; ?>
                <?php if ($creator_id == $user_id): ?>
                    <p class="text-success">Eres el creador de este evento.</p>
                    <a href="/users/attendees/<?= $event['id'] ?>" class="btn btn-success">Ver asistentes</a>
                <?php else: ?>
                    <?php if (isset($isBlocked)): ?>
                        <p class="text-danger">No puedes asistir a este evento. El usuario te ha bloqueado</p>
                    <?php else: ?>
                        <?php if ($user_id): ?>
                            <?php if ($is_joined): ?>
                                <p class="text-success">Ya estás apuntado a este Plan.</p>
                                <a href="#" class="btn btn-danger" onclick="showConfirmation('¿Estás Seguro de que quieres cancelar tu asistencia?', '/event/leave/<?= $event['id'] ?>')">Cancelar asistencia</a>
                            <?php elseif ($is_full): ?>
                                <p class="text-danger">Plan lleno</p>
                            <?php else: ?>
                                <?php if ($event['price'] && $event['price'] > 0): ?>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#paymentModal">
                                        Pagar (<?= number_format($event['price'], 2) ?> €)
                                    </button>
                                <?php else: ?>
                                    <a href="#" class="btn btn-success" onclick="showConfirmation('¿Estás Seguro de que quieres apuntarte al envento?', '/event/join/<?= $event['id'] ?>')">Apuntarse</a>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if (!isset($user_id)): ?>
                            <p class="text-danger">Inicia sesión para apuntarte a este evento.</p>
                        <?php endif; ?>
                        <?php if ($isAdmin): ?>
                            <a href="/admin-catalog/" class="btn btn-warning">Volver</a>
                        <?php else: ?>
                            <a href="/catalog" class="btn btn-secondary">Volver al Catálogo</a>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <div id="map"></div>
                <p class="mt-2"><?= $event['direccion'] ?></p>
            </div>
        </div>
    </div>
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">Pago con Tarjeta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="paymentForm" action="/event/process-payment/<?= $event['id'] ?>" method="POST">
                        <div class="mb-3">
                            <label for="cardNumber" class="form-label">Número de Tarjeta</label>
                            <input type="text" class="form-control" id="cardNumber" name="card_number" placeholder="1234 5678 9012 3456" required>
                        </div>
                        <div class="mb-3">
                            <label for="cardHolder" class="form-label">Nombre del Titular</label>
                            <input type="text" class="form-control" id="cardHolder" name="card_holder" placeholder="Nombre Apellido" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="expiryDate" class="form-label">Fecha de Caducidad</label>
                                <input type="text" class="form-control" id="expiryDate" name="expiry_date" placeholder="MM/AA" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="cvv" class="form-label">CVV</label>
                                <input type="text" class="form-control" id="cvv" name="cvv" placeholder="123" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <p><strong>Cantidad a Pagar:</strong> <?= number_format($event['price'], 2) ?> €</p>
                        </div>
                        <button type="submit" class="btn btn-primary">Confirmar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php include_once __DIR__ . '/../includes/confirm.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="/react/index-ypKMb6Ny.js"></script>
    <script>
        fetch("/map/mover-mapa", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    direccion: "<?= $event['direccion'] ?>"
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "ok") {
                    let lat = parseFloat(data.lat);
                    let lon = parseFloat(data.lon);
                    let map = L.map("map").setView([lat, lon], 15);
                    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                    }).addTo(map);
                    L.marker([lat, lon]).addTo(map);
                }
            });
    </script>
</body>

</html>