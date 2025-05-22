<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planit</title>
    <link rel="icon" type="image/png" href="/imgs/planit-logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/react/index-CE6iOV0z.css">
    <link rel="stylesheet" href="/css/auth.css">
</head>

<body>
    <main class="main-container">
        <section class="login-container">
            <div class="container mt-5">
                <h1 class="mb-4">Registrarse</h1>
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>
                <form action="/register" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="username" class="form-label">Usuario</label>
                        <input type="text" class="form-control" name="username"
                            value="<?= isset($_COOKIE['register_username']) ? htmlspecialchars($_COOKIE['register_username']) : '' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirm" class="form-label">Confirmar Contraseña</label>
                        <input type="password" class="form-control" name="password_confirm" required>
                    </div>
                    <div class="mb-3">
                        <label for="location" class="form-label">Ubicación</label>
                        <input type="text" class="form-control" name="location"
                            value="<?= isset($_COOKIE['register_location']) ? htmlspecialchars($_COOKIE['register_location']) : '' ?>" required>
                    </div>
                    <div class="mb-3 foto">
                        <input type="file" id="photo-input" name="photo" accept="image/*" style="display: none;">
                        <div id="photo-uploader" data-input-id="photo-input" data-default-img="/imgs/planit-logo.png"></div>
                    </div>
                    <button type="submit" class="btn btn-primary">Registrarse</button>
                </form>
                <p class="mt-3">¿Ya tienes cuenta? <a href="/login">Inicia sesión aquí</a></p>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
            <script src="/react/index-ypKMb6Ny.js"></script>
        </section>
    </main>
</body>

</html>