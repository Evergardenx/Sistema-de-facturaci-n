<?php
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] !== 1) {
    header("location: ../../login.php");
    exit;
}

/* Connect To Database */
include("../../config/db.php");
include("../../config/conexion.php");
$session_id = session_id();
$sql_count = mysqli_query($con, "SELECT * FROM tmp WHERE session_id = '" . $session_id . "'");
$count = mysqli_num_rows($sql_count);
if ($count == 0) {
    echo "<script>alert('No hay insumos agregados a la factura')</script>";
    echo "<script>window.close();</script>";
    exit;
}

// Consulta para obtener el próximo número de factura
$sql = "SELECT MAX(numero_factura) AS max_numero FROM facturas";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_assoc($result);

if ($row['max_numero'] === null) {
    // No hay facturas en la base de datos, comienza desde un número base
    $numero_factura = 1;
} else {
    // Obtén el número más alto y suma 1 para la próxima factura
    $numero_factura = (int) $row['max_numero'] + 1;
}

require_once(dirname(__FILE__) . '/../html2pdf.class.php');

// Variables por GET
$id_cliente = intval($_GET['id_cliente']);
$id_vendedor = intval($_GET['id_vendedor']);
$condiciones = mysqli_real_escape_string($con, strip_tags($_REQUEST['condiciones'], ENT_QUOTES));

try {
    ob_start();
    include(dirname(__FILE__) . '/res/factura_html.php');
    $content = ob_get_clean();

    // Initialize HTML2PDF
    $html2pdf = new HTML2PDF('P', 'LETTER', 'es', true, 'UTF-8', array(0, 0, 0, 0));
    $html2pdf->pdf->SetDisplayMode('fullpage');
    $html2pdf->writeHTML($content, isset($_GET['vuehtml']));

    // Generate and output the PDF
    $html2pdf->Output('Factura.pdf');
} catch (HTML2PDF_exception $e) {
    echo $e;
    exit;
}
?>