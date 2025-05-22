<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Crear Plan - Planit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="/imgs/planit-logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="/react/index-CE6iOV0z.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link rel="stylesheet" href="/css/create.css">
    <link rel="stylesheet" href="/css/edit.css">

</head>

<body>
    <?php if (!$_SESSION) {
        header('location: /');
        die();
    } ?>

    <div class="container mt-5 position-relative" style="max-width: 800px; margin: auto;">
        <a href="/">
            <h1 class="mb-4 text-center"><img src="/imgs/planit-logo.png" width="48" height="48"> Planit</h1>
        </a>
        <div id="profile-dropdown"
            data-username="<?= htmlspecialchars($username) ?>"
            data-photo="<?= htmlspecialchars($photo) ?>"
            data-user-id="<?= htmlspecialchars($user_id) ?>">
        </div>
        <h2 class="mb-4 text-center">Crear Plan</h2>
        <form id="eventForm" method="post" action="/events/store" enctype="multipart/form-data"
            class="p-4 border rounded shadow-sm bg-white mb-4">
            <div class="mb-3 text-center">
                <input type="file" id="event-photo-input" name="image" accept="image/*" style="display: none;">
                <div id="event-photo-uploader" data-input-id="event-photo-input" style="margin: auto; display: inline-block;"></div>
            </div>
            <div class="mb-3">
                <label for="title" class="form-label">Título</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Descripción</label>
                <textarea class="form-control" id="description" name="description" required></textarea>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="date" class="form-label">Fecha</label>
                    <input type="date" class="form-control" id="date" name="date" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="hour" class="form-label">Hora</label>
                    <input type="time" class="form-control" id="hour" name="hour" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="category" class="form-label">Categoría</label>
                    <select class="form-control" id="category" name="category" required>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= htmlspecialchars($category['nombre']) ?>"><?= htmlspecialchars($category['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="capacity" class="form-label">Aforo (opcional)</label>
                    <input type="number" class="form-control" id="capacity" name="capacity">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="price" class="form-label">Precio (opcional)</label>
                    <input type="number" step="0.01" class="form-control" id="price" name="price">
                </div>
            </div>
            <div class="mb-3">
                <label for="direccion" class="form-label">Dirección</label>
                <input type="text" class="form-control" id="direccion" name="direccion" required>
                <div id="errorDireccion" style="display: none;"></div>
            </div>
            <div class="mb-3">
                <div id="map" class="position-relative">
                    <div id="mapOverlay"></div>
                    <div id="loadingSpinner" class="spinner-border text-light"></div>
                </div>
            </div>
            <input type="hidden" id='city' name="city">
            <div class="d-flex justify-content-end">
                <a href="/" class="btn btn-secondary me-2">Volver</a>
                <button type="submit" id="btnPublicarEvento" class="btn btn-success">Publicar Plan</button>
            </div>
        </form>
    </div>
    <?php include_once __DIR__ . '/../includes/alert.php'; ?>
    <script src="/js/jquery.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="/react/index-ypKMb6Ny.js"></script>
    <script src="/js/map.js"></script>
    <?php if (isset($isAdmin) && !$isAdmin): ?>
        <?php include_once __DIR__ . '/../includes/confirm.php'; ?>
        <script src="/js/validarEvento.js"></script>
    <?php endif; ?>

</body>

</html>