<?php
include('is_logged.php');
$id_factura = $_SESSION['id_factura'];
$numero_factura = $_SESSION['numero_factura'];
if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
}
if (isset($_POST['cantidad'])) {
    $cantidad = intval($_POST['cantidad']);
}
if (isset($_POST['precio_venta'])) {
    $precio_venta = round(floatval($_POST['precio_venta']), 2);
}

$iva_10 = 0;
$iva_5 = 0;

/* Connect To Database */
require_once("../config/db.php");
require_once("../config/conexion.php");

if (!empty($id) && !empty($cantidad) && !empty($precio_venta)) {
    $insert_tmp = mysqli_query($con, "INSERT INTO detalle_factura (numero_factura, id_producto, cantidad, precio_venta) VALUES ('$numero_factura', '$id', '$cantidad', '$precio_venta')");
}
if (isset($_GET['id'])) {
    $id_detalle = intval($_GET['id']);
    $delete = mysqli_query($con, "DELETE FROM detalle_factura WHERE id_detalle = '$id_detalle'");
}
?>

<table class="table">
    <tr>
        <th class='text-center'>CODIGO</th>
        <th class='text-center'>CANT.</th>
        <th>DESCRIPCION</th>
        <th class='text-right'>PRECIO UNIT.</th>
        <th class='text-right'>PRECIO TOTAL</th>
        <th></th>
    </tr>
    <?php
    $sumador_total = 0;
    $subtotal_10 = 0;
    $subtotal_5 = 0;
    $sql = mysqli_query($con, "select * from products, facturas, detalle_factura where facturas.numero_factura = detalle_factura.numero_factura and  facturas.id_factura = '$id_factura' and products.id_producto = detalle_factura.id_producto");
    while ($row = mysqli_fetch_array($sql)) {
        $id_detalle = $row["id_detalle"];
        $codigo_producto = $row['codigo_producto'];
        $cantidad = $row['cantidad'];
        $nombre_producto = $row['nombre_producto'];
        $tasa_iva = $row['tasa_iva'];

        $precio_venta = round(floatval($row['precio_venta']), 2);
        $precio_total = $precio_venta * $cantidad;
        $precio_total = round($precio_total, 2);
        $sumador_total += $precio_total;

        if ($tasa_iva == 0.10) {
            $subtotal_10 += $precio_total;
        } elseif ($tasa_iva == 0.05) {
            $subtotal_5 += $precio_total;
        }
        ?>
        <tr>
            <td class='text-center'><?php echo $codigo_producto; ?></td>
            <td class='text-center'><?php echo $cantidad; ?></td>
            <td><?php echo $nombre_producto; ?></td>
            <td class='text-right'><?php echo number_format($precio_venta, 2); ?></td>
            <td class='text-right'><?php echo number_format($precio_total, 2); ?></td>
            <td class='text-center'><a href="#" onclick="eliminar('<?php echo $id_detalle ?>')"><i class="glyphicon glyphicon-trash"></i></a></td>
        </tr>
    <?php
    }

    $subtotal_10 = number_format($subtotal_10, 2, '.', '');
    $subtotal_5 = number_format($subtotal_5, 2, '.', '');
    
    $iva_10 = number_format($iva_10, 2, '.', '');
    $iva_5 = number_format($iva_5, 2, '.', '');
    
        if (is_numeric($subtotal_10)) {
            $iva_10 = $subtotal_10 * 0.10;
        }
    
        if (is_numeric($subtotal_5)) {
            $iva_5 = $subtotal_5 * 0.05;
        }
    
        $total_factura = $sumador_total + $iva_10 + $iva_5;
    $update = mysqli_query($con, "update facturas set total_venta = '$total_factura' where id_factura = '$id_factura'");
    ?>
    <tr>
        <td class='text-right' colspan=4>TOTAL GRAVADAS (10)% $</td>
        <td class='text-right'><?php echo number_format($subtotal_10, 2); ?></td>
        <td></td>
    </tr>
    <tr>
        <td class='text-right' colspan=4>TOTAL GRAVADAS (5)% $</td>
        <td class='text-right'><?php echo number_format($subtotal_5, 2); ?></td>
        <td></td>
    </tr>
    <tr>
        <td class='text-right' colspan=4>TOTAL IVA (10)% $</td>
        <td class='text-right'><?php echo number_format($iva_10, 2); ?></td>
        <td></td>
    </tr>
    <tr>
        <td class='text-right' colspan=4>TOTAL IVA (5)% $</td>
        <td class='text-right'><?php echo number_format($iva_5, 2); ?></td>
        <td></td>
    </tr>
    <tr>
        <td class='text-right' colspan=4>TOTAL $</td>
        <td class='text-right'><?php echo number_format($total_factura, 2); ?></td>
        <td></td>
    </tr>
</table>
