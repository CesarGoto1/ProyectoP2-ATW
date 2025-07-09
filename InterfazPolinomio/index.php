<?php
declare(strict_types=1);
use Clases\PolinomioConcreto;
use Clases\FuncionesPolinomio;
require_once 'clases/PolinomioConcreto.php';


$paso = 1;
$grado1 = null;
$grado2 = null;
$resultado = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['grado1'], $_POST['grado2']) && !isset($_POST['coef1'])) {
        if (empty($_POST['grado1']) || empty($_POST['grado2'])) {
            $resultado = [
                "error" => "Los grados de los polinomios no pueden estar vacíos"
            ];
            $paso = 1;
        } elseif (!is_numeric($_POST['grado1']) || !is_numeric($_POST['grado2'])) {
            $resultado = [
                "error" => "Los grados deben ser números válidos"
            ];
            $paso = 1;
        }else {
            $grado1 = (int)$_POST['grado1'];
            $grado2 = (int)$_POST['grado2'];
            $paso = 2;
        }
    } elseif (isset($_POST['coef1'], $_POST['coef2'], $_POST['x'], $_POST['grado1'], $_POST['grado2'])) {
        $grado1 = (int)$_POST['grado1'];
        $grado2 = (int)$_POST['grado2'];
        $coef1 = array_map('floatval', $_POST['coef1']);
        $coef2 = array_map('floatval', $_POST['coef2']);
        $x = (float)$_POST['x'];
        $p1 = [];
        $p2 = [];
        for ($i = 0; $i <= abs($grado1); $i++) {
            if ($grado1 >= 0) {
                $p1[$grado1 - $i] = $coef1[$i];
            } else {
                $p1[$i - abs($grado1)] = $coef1[$i];
            }
        }
        for ($i = 0; $i <= abs($grado2); $i++) {
            if ($grado2 >= 0) {
                $p2[$grado2 - $i] = $coef2[$i];
            } else {
                $p2[$i - abs($grado2)] = $coef2[$i];
            }
        }
        try {
            $pol1 = new PolinomioConcreto($p1);
            $pol2 = new PolinomioConcreto($p2);
            $suma = PolinomioConcreto::sumarPolinomios($p1, $p2);
            $polSuma = new PolinomioConcreto($suma);
            $resultado = [
                'pol1' => $pol1,
                'pol2' => $pol2,
                'polSuma' => $polSuma,
                'x' => $x
            ];
        } catch (Exception $e) {
            $resultado = [
                "error" => "Valores incorrectos"
            ];
        }
        $paso = 3;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Manejo de Polinomios</title>
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
                        <i class="bi bi-calculator icon-title"></i>
                        <span style="font-size:1.7rem;">Manejo de Polinomios</span>
                    </div>
                    <div class="card-body">
                        <?php if ($paso === 1): ?>
                            <?php if (isset($resultado['error'])): ?>
                                <div class="alert alert-danger text-center fade-in" role="alert">
                                    <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($resultado['error']) ?>
                                </div>
                            <?php endif; ?>
                            <form method="POST" class="needs-validation fade-in" novalidate>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="grado1" class="form-label">Grado del polinomio 1:</label>
                                        <input type="number" id="grado1" name="grado1" class="form-control" required value="<?= isset($_POST['grado1']) ? htmlspecialchars($_POST['grado1']) : '' ?>">
                                        <div class="invalid-feedback">
                                            Por favor ingrese un grado válido.
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="grado2" class="form-label">Grado del polinomio 2:</label>
                                        <input type="number"id="grado2" name="grado2" class="form-control" required value="<?= isset($_POST['grado2']) ? htmlspecialchars($_POST['grado2']) : '' ?>">
                                        <div class="invalid-feedback">
                                            Por favor ingrese un grado válido.
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center mt-4">
                                    <button type="submit" class="btn btn-primary px-4 py-2"><i class="bi bi-arrow-right-circle"></i> Aceptar</button>
                                </div>
                            </form>
                        <?php elseif ($paso === 2): ?>
                            <form method="POST" class="needs-validation fade-in" novalidate>
                                <input type="hidden" name="grado1" value="<?= htmlspecialchars((string)$grado1) ?>">
                                <input type="hidden" name="grado2" value="<?= htmlspecialchars((string)$grado2) ?>">
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-header bg-danger text-white text-center fw-bold">Polinomio 1</div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-hover">
                                                        <thead class="table-danger">
                                                            <tr>
                                                                <?php 
                                                                if ($grado1 >= 0) {
                                                                    for ($i = $grado1; $i >= 0; $i--): ?>
                                                                        <th>x<sup><?= $i ?></sup></th>
                                                                    <?php endfor;
                                                                } else {
                                                                    for ($i = 0; $i >= $grado1; $i--): ?>
                                                                        <th>x<sup><?= $i ?></sup></th>
                                                                    <?php endfor;
                                                                }
                                                                ?>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <?php 
                                                                if ($grado1 >= 0) {
                                                                    for ($i = $grado1; $i >= 0; $i--): ?>
                                                                        <td>
                                                                            <input type="number" step="any" name="coef1[]" class="form-control" required>
                                                                        </td>
                                                                    <?php endfor;
                                                                } else {
                                                                    for ($i = 0; $i >= $grado1; $i--): ?>
                                                                        <td>
                                                                            <input type="number" step="any" name="coef1[]" class="form-control" required>
                                                                        </td>
                                                                    <?php endfor;
                                                                }
                                                                ?>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-header bg-info text-white text-center fw-bold">Polinomio 2</div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-hover">
                                                        <thead class="table-info">
                                                            <tr>
                                                                <?php 
                                                                if ($grado2 >= 0) {
                                                                    for ($i = $grado2; $i >= 0; $i--): ?>
                                                                        <th>x<sup><?= $i ?></sup></th>
                                                                    <?php endfor;
                                                                } else {
                                                                    for ($i = 0; $i >= $grado2; $i--): ?>
                                                                        <th>x<sup><?= $i ?></sup></th>
                                                                    <?php endfor;
                                                                }
                                                                ?>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <?php 
                                                                if ($grado2 >= 0) {
                                                                    for ($i = $grado2; $i >= 0; $i--): ?>
                                                                        <td>
                                                                            <input type="number" step="any" name="coef2[]" class="form-control" required>
                                                                        </td>
                                                                    <?php endfor;
                                                                } else {
                                                                    for ($i = 0; $i >= $grado2; $i--): ?>
                                                                        <td>
                                                                            <input type="number" step="any" name="coef2[]" class="form-control" required>
                                                                        </td>
                                                                    <?php endfor;
                                                                }
                                                                ?>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-md-6 offset-md-3">
                                        <label for="x" class="form-label">Valor de <strong>x</strong>:</label>
                                        <input type="number" step="any" id="x" name="x" class="form-control" required>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center mt-4 gap-3">
                                    <button type="submit" class="btn btn-primary px-4 py-2"><i class="bi bi-123"></i> Calcular</button>
                                    <button type="button" class="btn btn-danger px-4 py-2" onclick="window.location='index.php'"><i class="bi bi-arrow-return-left"></i> Volver</button>
                                </div>
                            </form>
                        <?php elseif ($paso === 3): ?>
                            <div class="fade-in">
                                <h5 class="mb-3 text-center result-label"><i class="bi bi-check-circle-fill"></i> Resultado:</h5>
                                <?php if (isset($resultado['error'])): ?>
                                    <div class="alert alert-danger text-center" role="alert">
                                        <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($resultado['error']) ?>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        <div class="mb-2"><span class="result-label">Polinomio 1:</span> <span class="result-value"><?= $resultado['pol1']->mostrarPolinomio() ?></span></div>
                                        <div class="mb-2"><span class="result-label">Polinomio 2:</span> <span class="result-value"><?= $resultado['pol2']->mostrarPolinomio() ?></span></div>
                                        <div class="mb-2"><span class="result-label">Suma:</span> <span class="result-value"><?= $resultado['polSuma']->mostrarPolinomio() ?></span></div>
                                        <hr>
                                        <div class="mb-2"><span class="result-label">P1(<?= $resultado['x'] ?>) =</span> <span class="result-value"><?= $resultado['pol1']->evaluar($resultado['x']) ?></span></div>
                                        <div class="mb-2"><span class="result-label">P2(<?= $resultado['x'] ?>) =</span> <span class="result-value"><?= $resultado['pol2']->evaluar($resultado['x']) ?></span></div>
                                        <div class="mb-2"><span class="result-label">Suma(<?= $resultado['x'] ?>) =</span> <span class="result-value"><?= $resultado['polSuma']->evaluar($resultado['x']) ?></span></div>
                                        <hr>
                                        <div class="mb-2"><span class="result-label">Derivada P1:</span> <span class="result-value"><?= $resultado['pol1']->obtenerDerivada()->mostrarPolinomio() ?></span></div>
                                        <div class="mb-2"><span class="result-label">Derivada P2:</span> <span class="result-value"><?= $resultado['pol2']->obtenerDerivada()->mostrarPolinomio() ?></span></div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <div class="d-flex justify-content-center mb-3">
                            <a href="index.php" class="btn btn-success px-4 py-2">Nuevo cálculo</a>
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