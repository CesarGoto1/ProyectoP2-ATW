<?php
    declare(strict_types=1);
    use Clases\EstadisticaConcreta;
    require_once 'clases/EstadisticaConcreta.php';

    $paso = 1;
    $numConjuntos = 1; 
    $resultado = null;
    $erroresValidacion = [];
    
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (isset($_POST['agregar_conjunto'])) {
            $numConjuntos = (int)$_POST['numConjuntos'] + 1;
            $paso = 1;
        } elseif (isset($_POST['calcular'])) {
            $numConjuntos = (int)$_POST['numConjuntos'];
            $conjuntos = $_POST['conjuntos'];
            $nombres = $_POST['nombres'];
            $nombresUsados = [];
            for($i = 0; $i < $numConjuntos; $i++){
                $nombreConjunto = !empty($nombres[$i]) ? trim($nombres[$i]) : "Conjunto " . ($i+1);
                
                if(isset($nombresUsados[$nombreConjunto])){
                    $erroresValidacion[] = "El nombre '$nombreConjunto' está duplicado en los conjuntos.";
                } else {
                    $nombresUsados[$nombreConjunto] = true;
                }
            }
            for($i = 0; $i < $numConjuntos; $i++){
                $nombreConjunto = !empty($nombres[$i]) ? $nombres[$i] : "Conjunto " . ($i+1);
                
                if(empty($conjuntos[$i]) || trim($conjuntos[$i]) === ''){
                    $erroresValidacion[] = "El conjunto '$nombreConjunto' está vacío.";
                    continue; 
                }
                
                $datosOriginales = explode(",", trim($conjuntos[$i]));
                foreach($datosOriginales as $dato){
                    $datoLimpio = trim($dato);
                    if(!empty($datoLimpio) && !is_numeric($datoLimpio)){
                        $erroresValidacion[] = "En el conjunto '$nombreConjunto' existe un carácter no numérico: '$datoLimpio'";
                        break; 
                    }
                }
            }
            if(empty($erroresValidacion)){
                try{
                    $conjuntosDatos = array();
                    for($i = 0; $i < $numConjuntos; $i++){
                        if(!empty($conjuntos[$i])){
                            $id = $nombres[$i] ? : "Conjunto " . ($i+1);
                            $datos = EstadisticaConcreta::parseDatos($conjuntos[$i]);
                            if(!empty($datos)){
                                $conjuntosDatos[$id] = $datos;
                            }
                        }
                    }
                    
                    if(!empty($conjuntosDatos)){
                        $estadistica = new EstadisticaConcreta($conjuntosDatos);
                        $resultado = $estadistica->mostrarInforme($conjuntosDatos);
                        $paso = 2;
                    }else{
                        $resultado = ['error'=> "No se pudieron guardar los datos, los datos ingresados no son válidos"];
                        $paso = 2;
                    }
                } catch (Exception $e){
                    $resultado = ['error' => "No se pudieron procesar los datos: " . $e->getMessage()];
                    $paso = 2;
                }
            }
            
        }
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Análisis Estadístico</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center mb-4">
            <div class="col-lg-8">
                <div class="card shadow-lg">
                    <div class="gradient-header text-center">
                        <i class="bi bi-bar-chart-line icon-title"></i>
                        <span style="font-size:1.7rem;">Análisis Estadístico</span>
                    </div>
                    <div class="card-body">
                        <?php if ($paso === 1): ?>
                            <form method="POST" class="needs-validation fade-in" novalidate>
                                <input type="hidden" name="numConjuntos" value="<?= $numConjuntos ?>">
                                
                                <?php for ($i = 0; $i < $numConjuntos; $i++): ?>
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <div class="card border-0 shadow-sm">
                                                <div class="card-header bg-info text-white text-center fw-bold">
                                                    Conjunto <?= $i + 1 ?>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row g-3">
                                                        <div class="col-md-4">
                                                            <label for="nombre<?= $i ?>" class="form-label">Nombre del conjunto:</label>
                                                            <input type="text" id="nombre<?= $i ?>" name="nombres[]" class="form-control" placeholder="Conjunto <?= $i + 1 ?>" value="<?= isset($_POST['nombres'][$i]) ? htmlspecialchars($_POST['nombres'][$i]) : '' ?>">
                                                        </div>
                                                        <div class="col-md-8">
                                                            <label for="datos<?= $i ?>" class="form-label">Datos (separados por comas):</label>
                                                            <input type="text" id="datos<?= $i ?>" name="conjuntos[]" class="form-control" placeholder="1, 2, 3, 4, 5" value="<?= isset($_POST['conjuntos'][$i]) ? htmlspecialchars($_POST['conjuntos'][$i]) : '' ?>" required>
                                                            <div class="form-text">Ejemplo: 1, 2, 3, 4, 5</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endfor; ?>

                                <?php if (!empty($erroresValidacion)): ?>
                                    <div class="alert alert-danger mb-4" role="alert">
                                        <div class="text-center mb-3">
                                            <i class="bi bi-exclamation-triangle"></i> 
                                            <strong>Se encontraron los siguientes errores:</strong>
                                        </div>
                                        <ul class="mb-0">
                                            <?php foreach ($erroresValidacion as $error): ?>
                                                <li><?= htmlspecialchars($error) ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                        <div class="mt-3 text-center small">
                                            Por favor, corrija los datos y vuelva a intentarlo.
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <div class="card border-0 shadow-sm bg-light">
                                            <div class="card-body text-center">
                                                <h6 class="mb-3">¿Desea ingresar otro conjunto?</h6>
                                                <div class="d-flex justify-content-center gap-3">
                                                    <button type="submit" name="agregar_conjunto" class="btn btn-success px-4 py-2">
                                                        <i class="bi bi-plus-circle"></i> Sí
                                                    </button>
                                                    <button type="submit" name="calcular" class="btn btn-primary px-4 py-2">
                                                        <i class="bi bi-calculator"></i> No, Calcular
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        <?php elseif ($paso === 2): ?>
                            <div class="fade-in">
                                <h5 class="mb-3 text-center result-label"><i class="bi bi-check-circle-fill"></i> Resultados Estadísticos:</h5>
                                <?php if (isset($resultado['error'])): ?>
                                    <div class="alert alert-danger text-center" role="alert">
                                        <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($resultado['error']) ?>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($resultado as $identificador => $estadisticas): ?>
                                        <div class="alert alert-info mb-3">
                                            <h6 class="result-label"><i class="bi bi-graph-up"></i> <?= htmlspecialchars($identificador) ?></h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-2"><span class="result-label">Datos:</span> <span class="result-value">[<?= implode(', ', $estadisticas['datos']) ?>]</span></div>
                                                    <div class="mb-2"><span class="result-label">Datos Ordenados:</span> <span class="result-value">[<?= implode(', ', $estadisticas['ordenados']) ?>]</span></div>
                                                    <div class="mb-2"><span class="result-label">Cantidad:</span> <span class="result-value"><?= $estadisticas['cantidad'] ?></span></div>
                                                    
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-2"><span class="result-label">Media:</span> <span class="result-value"><?= number_format($estadisticas['media'], 2) ?></span></div>
                                                    <div class="mb-2"><span class="result-label">Mediana:</span> <span class="result-value"><?= number_format($estadisticas['mediana'], 2) ?></span></div>
                                                    <div class="mb-2"><span class="result-label">Moda:</span> <span class="result-value">[<?= is_array($estadisticas['moda']) ? implode(', ', $estadisticas['moda']) : $estadisticas['moda'] ?>]</span></div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <div class="d-flex justify-content-center mb-3">
                                <a href="index.php" class="btn btn-success px-4 py-2"><i class="bi bi-arrow-repeat"></i> Nuevo Análisis</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <footer class="text-center mt-5 text-muted small">
            <i class="bi bi-c-circle"></i> <?= date('Y') ?> - Derechos Reservados
            <p>César González</p>
        </footer>
    </div>
</body>
</html>