<?php 
include 'validar_admin.php'; 
include 'header.php'; 
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center py-5">
                        <h2 class="mb-4">Escaneo de Acceso</h2>
                        <p class="text-muted">Simula el escaneo ingresando el ID del socio</p>
                        
                        <form action="process_acceso.php" method="POST">
                            <div class="mb-3">
                                <input type="text" name="id_socio" class="form-control form-control-lg text-center" 
                                       placeholder="Ingresa ID o Código" autofocus required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                Verificar Entrada
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>