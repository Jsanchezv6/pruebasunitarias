<?php
require_once "../conexion.php";
session_start();
if (isset($_GET['detalle'])) {
    $id = $_SESSION['idUser'];
    $datos = array();
    $detalle = mysqli_query($conexion, "SELECT d.*, p.nombre, p.precio, p.imagen FROM temp_pedidos d INNER JOIN productos p ON d.id_producto = p.id WHERE d.id_usuario = $id");
    while ($row = mysqli_fetch_assoc($detalle)) {
        $data['id'] = $row['id'];
        $data['nombre'] = $row['nombre'];
        $data['cantidad'] = $row['cantidad'];
        $data['precio'] = $row['precio'];
        $data['imagen'] = ($row['imagen'] == null) ? '../assets/img/default.png' : $row['imagen'];
        $data['total'] = $data['precio'] * $data['cantidad'];
        array_push($datos, $data);
    }
    echo json_encode($datos);
    die();
} else if (isset($_GET['delete_detalle'])) {
    $id_detalle = $_GET['id'];
    $query = mysqli_query($conexion, "DELETE FROM temp_pedidos WHERE id = $id_detalle");
    if ($query) {
        $msg = "ok";
    } else {
        $msg = "Error";
    }
    echo $msg;
    die();
} else if (isset($_GET['detalle_cantidad'])) {
    $id_detalle = $_GET['id'];
    $cantidad = $_GET['cantidad'];
    $query = mysqli_query($conexion, "UPDATE temp_pedidos set cantidad = $cantidad WHERE id = $id_detalle");
    if ($query) {
        $msg = "ok";
    } else {
        $msg = "Error";
    }
    echo $msg;
    die();
} else if (isset($_GET['procesarPedido'])) {
    $id_sucursal = $_GET['id_sucursal'];
    $id_user = $_SESSION['idUser'];
    $caja = $_GET['caja'];
    $observacion = $_GET['observacion'];
    $consulta = mysqli_query($conexion, "SELECT d.*, p.nombre FROM temp_pedidos d INNER JOIN productos p ON d.id_producto = p.id WHERE d.id_usuario = $id_user");
    $total = 0;
    while ($row = mysqli_fetch_assoc($consulta)) {
        $total += $row['cantidad'] * $row['precio'];
    }
    $insertar = mysqli_query($conexion, "INSERT INTO pedidos (id, id_sucursal, num_caja, total, observacion, id_usuario) VALUES ($id_sucursal, $caja, $total, '$observacion', $id_user)");
    $id_pedido = mysqli_insert_id($conexion);
    if ($insertar == 1) {
        //$insertarDet = 0;
        $consulta = mysqli_query($conexion, "SELECT d.*, p.nombre FROM temp_pedidos d INNER JOIN productos p ON d.id_producto = p.id WHERE d.id_usuario = $id_user");
        while ($dato = mysqli_fetch_assoc($consulta)) {
            $nombre = $dato['nombre'];
            $cantidad = $dato['cantidad'];
            $precio = $dato['precio'];
            $insertarDet = mysqli_query($conexion, "INSERT INTO detalle_pedidos (nombre, precio, cantidad, id_pedido) VALUES ('$nombre', $precio, $cantidad, $id_pedido)");
        }
        if ($insertarDet > 0) {
            $eliminar = mysqli_query($conexion, "DELETE FROM temp_pedidos WHERE id_usuario = $id_user");
            $sucursal = mysqli_query($conexion, "SELECT * FROM sucursal WHERE id = $id_sucursal");
            $resultSala = mysqli_fetch_assoc($sucursal);
            $msg = array('mensaje' => $resultSala['cajas']);
        }
    } else {
        $msg = array('mensaje' => 'error');
    }

    echo json_encode($msg);
    die();
} else if (isset($_GET['editarUsuario'])) {
    $idusuario = $_GET['id'];
    $sql = mysqli_query($conexion, "SELECT * FROM usuarios WHERE id = $idusuario");
    $data = mysqli_fetch_array($sql);
    echo json_encode($data);
    exit;
} else if (isset($_GET['editarProducto'])) {
    $id = $_GET['id'];
    $sql = mysqli_query($conexion, "SELECT * FROM productos WHERE id = $id");
    $data = mysqli_fetch_array($sql);
    echo json_encode($data);
    exit;
} else if (isset($_GET['finalizarPedido'])) {
    $id_sucursal = $_GET['id_sucursal'];
    $id_user = $_SESSION['idUser'];
    $caja = $_GET['caja'];
    $insertar = mysqli_query($conexion, "UPDATE pedidos SET estado='FINALIZADO' WHERE id_sucursal=$id_sucursal AND num_caja=$caja AND estado='PENDIENTE' AND id_usuario=$id_user");
    if ($insertar) {
        $sucursal = mysqli_query($conexion, "SELECT * FROM sucursal WHERE id = $id_sucursal");
        $resultSala = mysqli_fetch_assoc($sucursal);
        $msg = array('mensaje' => $resultSala['cajas']);
    } else {
        $msg = array('mensaje' => 'error');
    }

    echo json_encode($msg);
    die();
}
if (isset($_POST['regDetalle'])) {
    $id_producto = $_POST['id'];
    $id_user = $_SESSION['idUser'];
    $consulta = mysqli_query($conexion, "SELECT * FROM temp_pedidos WHERE id_producto = $id_producto AND id_usuario = $id_user");
    $row = mysqli_fetch_assoc($consulta);
    if (empty($row)) {
        $producto = mysqli_query($conexion, "SELECT * FROM productos WHERE id = $id_producto");
        $result = mysqli_fetch_assoc($producto);
        $precio = $result['precio'];
        $query = mysqli_query($conexion, "INSERT INTO temp_pedidos (cantidad, precio, id_producto, id_usuario) VALUES (1, $precio, $id_producto, $id_user)");
    } else {
        $nueva = $row['cantidad'] + 1;
        $query = mysqli_query($conexion, "UPDATE temp_pedidos SET cantidad = $nueva WHERE id_producto = $id_producto AND id_usuario = $id_user");
    }
    if ($query) {
        $msg = "registrado";
    } else {
        $msg = "Error al ingresar";
    }
    echo json_encode($msg);
    die();
}
