<?php 
include 'config.php';
include 'validar_entrenador.php'; 
include 'header_entrenador.php'; 

$id_socio = isset($_GET['id']) ? $_GET['id'] : '';

// Consultamos el nombre del socio para que el profe sepa a quién evalúa
$res_socio = $conexion->query("SELECT nombre, apellido FROM socios WHERE id_socio = '$id_socio'");
$socio = ($res_socio && $res_socio->num_rows > 0) ? $res_socio->fetch_assoc() : null;
$nombre_socio = $socio ? $socio['nombre'] . " " . $socio['apellido'] : "No encontrado";
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <div class="row align-items-center mb-4">
            <div class="col">
                <h2 class="page-title text-purple fw-bold">Nueva Evaluación Física</h2>
                <div class="text-muted mt-1">Socio: <strong><?php echo $nombre_socio; ?></strong></div>
            </div>
            <div class="col-auto">
                <a href="mis_socios.php" class="btn btn-secondary">
                    <i class="ti ti-arrow-back me-2"></i>Volver
                </a>
            </div>
        </div>

        <div class="col-md-8 mx-auto">
            <form action="guardar_evaluacion_profe.php" method="POST" class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <input type="hidden" name="id_socio" value="<?php echo $id_socio; ?>">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Peso (kg)</label>
                            <input type="number" step="0.1" name="peso" id="peso" class="form-control shadow-none" placeholder="Ej: 85.5" oninput="calcularIMC()" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Estatura (cm)</label>
                            <input type="number" name="talla" id="talla" class="form-control shadow-none" placeholder="Ej: 175" oninput="calcularIMC()" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <label class="form-label text-purple fw-bold">IMC Calculado</label>
                            <input type="text" name="imc" id="imc_display" class="form-control text-center" readonly placeholder="Se calculará automáticamente">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Cintura (cm)</label>
                            <input type="number" step="0.1" name="cintura" class="form-control shadow-none" placeholder="Ej: 100" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Cadera (cm)</label>
                            <input type="number" step="0.1" name="cadera" class="form-control shadow-none" placeholder="Ej: 130" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">% Grasa</label>
                            <input type="number" step="0.1" name="porcentaje_grasa" class="form-control shadow-none" placeholder="Ej: 25" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">% Músculo</label>
                            <input type="number" step="0.1" name="porcentaje_musculo" class="form-control shadow-none" placeholder="Ej: 35" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Comentarios / Observaciones</label>
                        <textarea name="comentarios" class="form-control shadow-none" rows="3" placeholder="Notas sobre el progreso..."></textarea>
                    </div>
                </div>
                <div class="card-footer text-end bg-light p-3" style="border-bottom-left-radius: 15px; border-bottom-right-radius: 15px;">
                    <button type="submit" class="btn btn-purple px-4 shadow-sm">
                        <i class="ti ti-device-floppy me-2"></i>Guardar Evaluación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function calcularIMC() {
    const peso = parseFloat(document.getElementById('peso').value);
    const tallaCm = parseFloat(document.getElementById('talla').value);
    const imcInput = document.getElementById('imc_display');

    if (peso > 0 && tallaCm > 0) {
        const tallaM = tallaCm / 100;
        const imc = (peso / (tallaM * tallaM)).toFixed(2);
        imcInput.value = imc;

        // Semáforo de colores pro
        if (imc < 18.5) { 
            imcInput.style.backgroundColor = "#fff4f4"; imcInput.style.color = "#dc3545"; 
        } else if (imc >= 18.5 && imc <= 24.9) { 
            imcInput.style.backgroundColor = "#f4fff4"; imcInput.style.color = "#198754"; 
        } else if (imc >= 25.0 && imc <= 29.9) { 
            imcInput.style.backgroundColor = "#fffcf4"; imcInput.style.color = "#ffc107"; 
        } else { 
            imcInput.style.backgroundColor = "#fff4f4"; imcInput.style.color = "#dc3545"; 
        }
    } else {
        imcInput.value = "";
        imcInput.style.backgroundColor = "";
    }
}
</script>

<?php include 'footer.php'; ?>