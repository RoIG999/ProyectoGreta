<?php include("conexion.php"); ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Asistencia</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    .navbar-nav .nav-link { color: white !important; }
    .table tbody tr.present td { background-color: #d1e7dd !important; }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg" style="background-color: black;">
  <div class="container-fluid align-items-center">
    <img src="img/LogoGreta.jpeg" alt="LogoGreta" style="width:80px;height:80px;">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link active" href="index.php">Asistencia</a></li>
        <li class="nav-item"><a class="nav-link" href="gestionUsuarios.php">Usuarios</a></li>
         <li class="nav-item"><a class="nav-link" href="Servicios(Dueña).php">Servicios</a></li>
      </ul>
      <ul class="navbar-nav d-flex flex-row gap-3">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" title="Usuario actual">
            <i class="bi bi-person-circle fs-5"></i> ROOT
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="#">🔔 Notificaciones</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="index.php">🚪 Cerrar sesión</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 id="titulo-asistencia">Asistencia</h2>
    <div class="input-group w-50">
      <input id="input-id" type="text" class="form-control" placeholder="Ingrese su ID y presione Enter...">
    </div>
  </div>

  <form action="guardarAsistencia.php" method="POST">
    <table class="table table-bordered" id="tabla-asistencia">
      <thead>
        <tr>
          <th>ID Usuario</th>
          <th>Nombre</th>
          <th>Asistencia</th>
          <th>Anotaciones</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $sql = "SELECT id, nombre FROM usuarios WHERE estado = 1";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()):
        ?>
        <tr data-id="<?= $row['id'] ?>">
          <td><?= $row['id'] ?></td>
          <td><?= $row['nombre'] ?></td>
          <td class="text-center">
            <input type="checkbox" name="asistencia[<?= $row['id'] ?>]" value="1">
          </td>
          <td>
            <textarea class="form-control" name="anotaciones[<?= $row['id'] ?>]" rows="1"></textarea>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

    <div class="mt-3">
      <button type="submit" class="btn btn-success">Guardar Asistencia</button>
      <button type="button" class="btn btn-secondary" onclick="window.print()">Imprimir</button>
    </div>
  </form>
</div>

<h2 class="mb-4 container mt-5"><a class="nav-link" href="Historial.php">Ver historial de asistencia</a></h2>

<!-- TOAST de confirmación -->
<?php if (isset($_GET['mensaje'])): ?>
<div class="toast-container position-fixed bottom-0 end-0 p-3">
  <div class="toast align-items-center text-bg-success border-0 show" role="alert">
    <div class="d-flex">
      <div class="toast-body">
        <?= htmlspecialchars($_GET['mensaje']) ?>
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>
<?php endif; ?>

<footer class="text-white text-center p-3 mt-5" style="background-color:black;">
  <p>&copy; 2025 Grupo GRETA | Contacto: GIA.com</p>
</footer>

<script>
  document.getElementById("input-id").addEventListener("keypress", function(e) {
    if (e.key === "Enter") {
      e.preventDefault();
      const id = this.value.trim();
      const fila = document.querySelector(`tr[data-id='${id}']`);
      if (fila) {
        fila.classList.add("present");
        fila.querySelector("input[type='checkbox']").checked = true;
        this.value = "";
      } else {
        alert("ID no encontrado");
      }
    }
  });

  window.addEventListener("DOMContentLoaded", () => {
    const hoy = new Date();
    const dia = String(hoy.getDate()).padStart(2, '0');
    const mes = String(hoy.getMonth() + 1).padStart(2, '0');
    const anio = hoy.getFullYear();
    const fechaFormateada = `${dia}-${mes}-${anio}`;
    document.getElementById("titulo-asistencia").textContent = `Asistencia - ${fechaFormateada}`;
  });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
  <div id="toastAsistencia" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
        ✅ Asistencia guardada correctamente.
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
    </div>
  </div>
</div>
<?php if (isset($_GET['asistenciaGuardada'])): ?>
<script>
  const toastAsistencia = new bootstrap.Toast(document.getElementById('toastAsistencia'));
  toastAsistencia.show();
</script>
<?php endif; ?>

</body>
</html>
