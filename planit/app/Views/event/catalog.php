<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="icon" type="image/png" href="/imgs/planit-logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="/css/catalog.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
</head>

<body>
    <div class="container mt-5 position-relative">
        <a href="/">
            <h1 class="mb-4"><img src="/imgs/planit-logo.png" width="48" height="48" alt="Logo de planit" loading="lazy"> Planit</h1>
        </a>
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>
        <h2 class="mb-4">Planes Destacados</h2>
        <?php if ($user_id): ?>
            <div id="profile-dropdown"
                data-username="<?= htmlspecialchars($username) ?>"
                data-photo="<?= htmlspecialchars($photo) ?>"
                data-user-id="<?= htmlspecialchars($user_id) ?>">
            </div>
            <a href="/events/create" class="btn btn-primary mb-4">Crear nuevo Plan</a>
            <?php if ($isAdmin): ?>
                <a href="/admin-catalog" class="btn btn-warning mb-4">Panel de Administrador</a>
            <?php endif; ?>
        <?php else: ?>
            <p><a href="/login" class="btn btn-secondary">Iniciar Sesión</a> | <a href="/register" class="btn btn-secondary">Registrarse</a></p>
        <?php endif; ?>

        <div class="mb-4 d-flex flex-wrap align-items-start gap-3">
            <div class="d-flex flex-column">
                <label for="search-title" class="form-label">Buscar por Título</label>
                <input type="text" class="form-control" id="search-title" placeholder="Título del evento">
            </div>

            <div class="d-flex flex-column">
                <label for="filter-city" class="form-label">Filtrar por Ciudad</label>
                <input type="text" class="form-control" id="filter-city" placeholder="Ciudad">
            </div>
            <div class="d-flex flex-column">
                <label for="filter-category" class="form-label">Filtrar por Categoría</label>
                <select class="form-control" id="filter-category">
                    <option value="">Todas las Categorías</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= htmlspecialchars($category['nombre']) ?>"><?= htmlspecialchars($category['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="d-flex flex-column">
                <label for="price-range" class="form-label">Filtrar por Rango de Precio</label>
                <div id="price-range" style="width: 150px;"></div>
                <span id="price-value" class="mt-2">0 - 100 €</span>
            </div>
            <div class="d-flex flex-column">
                <label class="form-label">&nbsp;</label>
                <a href="/"><button class="btn btn-primary">Reiniciar Filtros</button></a>
            </div>
        </div>

        <div class="row">
            <?php foreach ($events as $event): ?>
                <?php if ($event['date'] > date('Y-m-d') && $event['canceled'] !== 'si'): ?>
                    <div class="col-md-4 mb-4"
                        data-category-name="<?= htmlspecialchars($event['category_name']) ?>"
                        data-price="<?= htmlspecialchars($event['price']) ?>"
                        data-city="<?= htmlspecialchars($event['city']) ?>">
                        <a href="/events/<?= $event['id'] ?>" class="text-decoration-none text-dark">
                            <div class="card h-100">
                                <?php if ($event['image']): ?>
                                    <img src="data:image/jpeg;base64,<?= base64_encode($event['image']) ?>" class="card-img-top" alt="Imagen del Plan" loading="lazy">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($event['title']) ?></h5>
                                    <p class="card-text"><?= htmlspecialchars($event['description']) ?></p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <p class="card-text date mb-0"><small class="text-muted"><?= htmlspecialchars($event['date']) ?></small></p>
                                        <p class="card-text category mb-0"><small class="text-muted"><?= htmlspecialchars($event['category_name']) ?> en <?= htmlspecialchars($event['city']) ?></small></p>
                                    </div>
                                    <p class="card-text text-end mt-2">
                                        <?php if ($event['price'] && $event['price'] > 0): ?>
                                            <?= number_format($event['price'], 2) ?> €
                                        <?php else: ?>
                                            Gratis
                                        <?php endif; ?>
                                    </p>
                                    <?php if (isset($attended_events_ids) && in_array($event['id'], $attended_events_ids)): ?>
                                        <span class="badge bg-success">Ya estas apuntado</span>
                                    <?php elseif ($event['user_id'] == $user_id): ?>
                                        <span class="badge bg-success">Eres el creador de este evento</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
    <?php include_once __DIR__ . '/../includes/confirm.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="/react/index-ypKMb6Ny.js"></script>
    <script src="/js/jquery.js"></script>
</body>

</html>