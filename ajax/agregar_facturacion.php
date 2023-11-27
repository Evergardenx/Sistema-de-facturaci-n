<?php
include('is_logged.php');
$session_id = session_id();

if (isset($_POST['id'])) {
    $id = $_POST['id'];
}
if (isset($_POST['cantidad'])) {
    $cantidad = $_POST['cantidad'];
}
if (isset($_POST['precio_venta'])) {
    $precio_venta = $_POST['precio_venta'];
}

$iva_10 = 0;
$iva_5 = 0;

require_once("../config/db.php");
require_once("../config/conexion.php");

if (!empty($id) && !empty($cantidad) && !empty($precio_venta)) {
    $insert_tmp = mysqli_query($con, "INSERT INTO tmp (id_producto, cantidad_tmp, precio_tmp, session_id) VALUES ('$id', '$cantidad', '$precio_venta', '$session_id')");
}

if (isset($_GET['id'])) {
    $id_tmp = intval($_GET['id']);
    $delete = mysqli_query($con, "DELETE FROM tmp WHERE id_tmp = '$id_tmp'");
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

    $sql = mysqli_query($con, "select * from products, tmp where products.id_producto = tmp.id_producto and tmp.session_id = '$session_id'");
    while ($row = mysqli_fetch_array($sql)) {
        $id_tmp = $row["id_tmp"];
        $codigo_producto = $row['codigo_producto'];
        $cantidad = $row['cantidad_tmp'];
        $nombre_producto = $row['nombre_producto'];
        $precio_venta = $row['precio_tmp'];

        $precio_venta_f = $precio_venta;

        $precio_total = $precio_venta * $cantidad;
        $precio_total_f = $precio_total;

        if ($row['tasa_iva'] == 0.10) {
            $subtotal_10 += $precio_total;
        } elseif ($row['tasa_iva'] == 0.05) {
            $subtotal_5 += $precio_total;
        }

        $sumador_total += $precio_total;
        ?>
        <tr>
            <td class='text-center'><?php echo $codigo_producto; ?></td>
            <td class='text-center'><?php echo $cantidad; ?></td>
            <td><?php echo $nombre_producto; ?></td>
            <td class='text-right'><?php echo $precio_venta_f; ?></td>
            <td class='text-right'><?php echo $precio_total_f; ?></td>
            <td class='text-center'><a href="#" onclick="eliminar('<?php echo $id_tmp ?>')"><i
                    class="glyphicon glyphicon-trash"></i></a></td>
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
    ?>
    <tr>
        <td class='text-right' colspan=4>TOTAL GRAVADAS (10)% $</td>
        <td class='text-right'><?php echo $subtotal_10; ?></td>
        <td></td>
    </tr>
    <tr>
        <td class='text-right' colspan=4>TOTAL GRAVADAS (5)% $</td>
        <td class='text-right'><?php echo $subtotal_5; ?></td>
        <td></td>
    </tr>
    <tr>
        <td class='text-right' colspan=4>TOTAL IVA (10)% $</td>
        <td class='text-right'><?php echo $iva_10; ?></td>
        <td></td>
    </tr>
    <tr>
        <td class='text-right' colspan=4>TOTAL IVA (5)% $</td>
        <td class='text-right'><?php echo $iva_5; ?></td>
        <td></td>
    </tr>
    <tr>
        <td class='text-right' colspan=4>TOTAL $</td>
        <td class='text-right'><?php echo number_format($total_factura, 2); ?></td>
        <td></td>
    </tr>
</table>