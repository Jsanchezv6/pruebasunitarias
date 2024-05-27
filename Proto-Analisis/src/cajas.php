<?php
session_start();
if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 3) {
    $id = $_GET['id_sucursal'];
    $cajas = $_GET['cajas'];
    include_once "includes/header.php";
?>
    <div class="card">
        <div class="card-header text-center">
            Cajas
        </div>
        <div class="card-body">
            <div class="row">
                <?php
                include "../conexion.php";
                $query = mysqli_query($conexion, "SELECT * FROM sucursal WHERE id = $id");
                $result = mysqli_num_rows($query);
                if ($result > 0) {
                    $data = mysqli_fetch_assoc($query);
                    if ($data['cajas'] == $cajas) {
                        $item = 1;
                        for ($i = 0; $i < $cajas; $i++) {
                            $consulta = mysqli_query($conexion, "SELECT * FROM pedidos WHERE id_sucursal = $id AND num_caja = $item AND estado = 'PENDIENTE'");
                            $resultPedido = mysqli_fetch_assoc($consulta);
                ?>
                            <div class="col-md-3">
                                <div class="card card-widget widget-user">
                                    <!-- Add the bg color to the header using any of the bg-* classes -->
                                    <div class="widget-user-header bg-<?php echo empty($resultPedido) ? 'success' : 'danger'; ?>">
                                        <h3 class="widget-user-username">CAJA</h3>
                                        <h5 class="widget-user-desc"><?php echo $item; ?></h5>
                                    </div>
                                    <div class="widget-user-image">
                                        <img class="img-circle elevation-2" src="../assets/img/caja.png" alt="User Avatar">
                                    </div>
                                    <div class="card-footer">
                                        <div class="description-block">
                                            <?php if (empty($resultPedido)) {
                                                echo '<a class="btn btn-outline-info" href="pedido.php?id_sucursal=' . $id . '&caja=' . $item . '">Atender</a>';
                                            } else {
                                                echo '<a class="btn btn-outline-success" href="finalizar.php?id_sucursal=' . $id . '&caja=' . $item . '">Finalizar</a>';
                                            } ?>

                                        </div>
                                        <!-- /.row -->
                                    </div>
                                </div>
                            </div>

                <?php $item++;
                        }
                    }
                } ?>
            </div>
        </div>
    </div>
<?php include_once "includes/footer.php";
} else {
    header('Location: permisos.php');
} ?>