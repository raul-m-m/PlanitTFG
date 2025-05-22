<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="/imgs/planit-logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/profile.css">
</head>

<body>
    <main class="main-container">
        <section class="login-container">
            <h1 class="mb-4">Configuración del Perfil</h1>
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
            <form action="/profile/update/<?= htmlspecialchars($id) ?>" method="post" enctype="multipart/form-data">
                <div class="mb-3 text-center">
                    <div class="photo-container">
                        <img
                            id="profile-photo"
                            src="data:image/jpeg;base64,<?= htmlspecialchars($photo) ?>"
                            alt="Foto de perfil actual"
                            class="rounded-circle bg-light border border-2 border-dashed"
                            onclick="document.getElementById('photo-input').click();">
                        <span class="hover-text">Cambiar foto de perfil</span>
                    </div>
                    <input
                        type="file"
                        id="photo-input"
                        name="photo"
                        accept="image/*"
                        style="display: none;"
                        onchange="previewPhoto(this);">
                </div>
                <div class="mb-3">
                    <label for="username" class="form-label">Nombre de Usuario</label>
                    <input type="text" class="form-control" id="username" name="username"
                        value="<?= htmlspecialchars($username) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Nueva Contraseña (dejar en blanco para no cambiar)</label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>
                <div class="mb-3">
                    <label for="password_confirm" class="form-label">Confirmar Nueva Contraseña</label>
                    <input type="password" class="form-control" id="password_confirm" name="password_confirm">
                </div>
                <div class="mb-3">
                    <label for="location" class="form-label">Ubicación</label>
                    <input type="text" class="form-control" id="location" name="location"
                        value="<?= htmlspecialchars($location) ?>">
                </div>
                <div class="d-flex justify-content-between">
                    <?php if ($id != session()->get('user_id')): ?>
                        <a href="/admin/users" class="btn btn-primary">Volver</a>
                    <?php endif; ?>
                    <?php if ($id == session()->get('user_id')): ?>
                        <a href="/" class="btn btn-primary">Volver</a>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    <a href="/user/<?= htmlspecialchars($username) ?>" class="btn btn-secondary">Ver Perfil Público</a>
                </div>
            </form>
        </section>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src=/js/profile.js></script>
</body>
</html>