<?php 
include 'config.php';
include 'validar.php'; // este archivo valida que seas admin para entrar a esta pagina
include 'header.php'; 


// Recibimos los IDs desde la URL
$id_socio = isset($_GET['id_socio']) ? $_GET['id_socio'] : '';
$id_ent = isset($_GET['id_ent']) ? $_GET['id_ent'] : '';

// Consultamos el nombre del socio
$res_socio = $conexion->query("SELECT nombre FROM socios WHERE id_socio = '$id_socio'");

if($res_socio && $res_socio->num_rows > 0) {
    $socio = $res_socio->fetch_assoc();
    $nombre_socio = $socio['nombre'];
} else {
    $nombre_socio = "No encontrado";
}
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <div class="row align-items-center mb-4">
            <div class="col">
                <h2 class="page-title text-yellow">Nueva Evaluación Física</h2>
                <div class="text-muted mt-1">Socio: <strong><?php echo $nombre_socio; ?></strong></div>
            </div>
            <div class="col-auto">
                <a href="seguimiento_entrenador.php?id=<?php echo $id_ent; ?>" class="btn btn-secondary border-0 shadow-sm">
                    <i class="ti ti-arrow-back me-2"></i>Volver
                </a>
            </div>
        </div>

        <div class="col-md-8">
            <form action="guardar_progreso.php" method="POST" class="card shadow-sm border-0">
                <div class="card-status-start bg-yellow"></div>
                <div class="card-body">
                    <input type="hidden" name="id_socio" value="<?php echo $id_socio; ?>">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Peso (kg)</label>
                            <input type="number" step="0.1" name="peso" id="peso" class="form-control" placeholder="Ej: 85.5" oninput="calcularIMC()" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Estatura (cm)</label>
                            <input type="number" name="talla" id="talla" class="form-control" placeholder="Ej: 175" oninput="calcularIMC()" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label text-blue font-weight-bold">IMC Calculado</label>
                            <input type="text" name="imc" id="imc_display" class="form-control bg-blue-lt font-weight-bold text-center" readonly placeholder="Se calculará automáticamente">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Cintura (cm)</label>
                            <input type="number" step="0.1" name="cintura" class="form-control" placeholder="Ej: 100" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Cadera (cm)</label>
                            <input type="number" step="0.1" name="cadera" class="form-control" placeholder="Ej: 130" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">% Grasa</label>
                            <input type="number" step="0.1" name="porcentaje_grasa" class="form-control" placeholder="Ej: 40" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">% Músculo</label>
                            <input type="number" step="0.1" name="porcentaje_musculo" class="form-control" placeholder="Ej: 30" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Comentarios / Observaciones</label>
                        <textarea name="comentarios" class="form-control" rows="3" placeholder="Ej: está cañón"></textarea>
                    </div>
                </div>
                <div class="card-footer text-end bg-light">
                    <a href="ver_progreso.php?id=<?php echo $id_socio; ?>" class="btn btn-link text-muted">Cancelar</a>
                    <button type="submit" class="btn btn-yellow text-dark font-weight-bold shadow-sm">
                        <i class="ti ti-device-floppy me-2"></i>Guardar Evaluación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function calcularIMC() {
    // 1. Obtenemos los valores de peso (kg) y talla (cm)
    const pesoInput = document.getElementById('peso');
    const tallaCmInput = document.getElementById('talla');
    const imcInput = document.getElementById('imc_display');

    // Convertimos a números flotantes
    const peso = parseFloat(pesoInput.value);
    const tallaCm = parseFloat(tallaCmInput.value);

    // Limpiamos el color y valor previo si están vacíos o son 0
    imcInput.value = "";
    imcInput.style.backgroundColor = "";
    imcInput.style.color = ""; // Color de texto por defecto

    // 2. Validamos que ambos tengan valores válidos
    if (peso > 0 && tallaCm > 0) {
        // 3. Calculamos el IMC: Peso / (Talla en metros)^2
        const tallaM = tallaCm / 100; // Convertimos cm a metros
        const imc = (peso / (tallaM * tallaM)).toFixed(2); // Calculamos y redondeamos a 2 decimales
        
        // Mostrar el resultado en el input readonly
        imcInput.value = imc;

        // 4. SEMÁFORO DE ALERTA (Rápidos rangos de la OMS)
        // Usamos colores de fondo pastel para que no se vea agresivo, 
        // pero con texto oscuro para que se lea bien.

        if (imc < 18.5) {
            // --- ROJO: BAJO PESO (Alerta nutricional) ---
            imcInput.style.backgroundColor = "#ffcccc"; // Rojo pastel muy claro
            imcInput.style.color = "#990000"; // Texto rojo oscuro
            imcInput.setAttribute('title', 'Alerta: Bajo Peso');

        } else if (imc >= 18.5 && imc <= 24.9) {
            // --- VERDE: PESO NORMAL (¡Excelente!) ---
            imcInput.style.backgroundColor = "#dcfce7"; // Verde pastel claro
            imcInput.style.color = "#166534"; // Texto verde oscuro
            imcInput.setAttribute('title', 'Peso Saludable');

        } else if (imc >= 25.0 && imc <= 29.9) {
            // --- AMARILLO: SOBREPESO (Atención moderada) ---
            imcInput.style.backgroundColor = "#fef9c3"; // Amarillo pastel claro
            imcInput.style.color = "#854d0e"; // Texto amarillo/marrón oscuro
            imcInput.setAttribute('title', 'Atención: Sobrepeso');

        } else if (imc >= 30.0) {
            // --- ROJO: OBESIDAD (Alerta de salud) ---
            imcInput.style.backgroundColor = "#ffcccc"; // Rojo pastel muy claro
            imcInput.style.color = "#990000"; // Texto rojo oscuro
            imcInput.setAttribute('title', 'Alerta: Obesidad');
        }
    }
}
</script>

<?php include 'footer.php'; ?>