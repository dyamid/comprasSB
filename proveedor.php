<?php
require_once ('conexion/db.php');

$codigo_proveedor = "";
if (isset($_GET['cod_prov']) && $_GET['cod_prov'] != '') {
	$codigo_proveedor = $_GET['cod_prov'];
}

if ($codigo_proveedor != '') {
	$query_RsLista_prov = "SELECT R.REQUCORE AS REQUERIMIENTO_CODIGO,
							R.REQUCODI AS REQUERIMIENTO,
							DR.DEREPROV AS DETALLE_ID_PROVEEDOR,
							DR.DEREDESC AS DETALLE_DESC,
							DR.DERECOOC AS DETALLE_ORDEN,
							P.PROVNOMB AS PROVEEDOR_NOMBRE,
							DR.DERECOOC AS ID_ORDEN,
							F.FIRMCONS AS ID_FIRMA,
							OC.ORCOFIRM AS ORDEN_FIRMA,
							DF.DEFADETA AS FACTURA

						FROM REQUERIMIENTOS R
						JOIN DETALLE_REQU DR ON DR.DEREREQU = R.REQUCODI
						JOIN PROVEEDORES P ON DR.DEREPROV = P.PROVCODI
						JOIN ORDEN_COMPRA OC ON DR.DERECOOC = OC.ORCOCONS
						JOIN FIRMAS F ON OC.ORCOFIRM = F.FIRMCONS
						LEFT JOIN DETALLE_FACTURA DF ON DR.DERECONS = DF.DEFADETA

						WHERE P.PROVCODI = '" . $codigo_proveedor . "';

				
					";
	$RsLista_prov = mysqli_query($conexion, $query_RsLista_prov) or die(mysqli_error($conexion));
	$row_RsLista_prov = mysqli_fetch_array($RsLista_prov);
	$totalRows_RsLista_prov = mysqli_num_rows($RsLista_prov);

}

if ($codigo_proveedor != '') {
	$query_RsPOA = " SELECT PO.POANOMB AS POA_NOM,
							PD.PODENOMB AS SUBPOA_NOM
					FROM REQUERIMIENTOS R,
						DETALLE_REQU DR,
						PROVEEDORES P,
						POA PO,
						POADETA PD
					WHERE P.PROVCODI = '" . $codigo_proveedor . "'
					AND DR.DEREREQU = R.REQUCODI
					AND DR.DEREPROV = P.PROVCODI
					AND DR.DEREPOA = PO.POACODI
					AND PD.PODECODI = DR.DEREPOA
    ";
	$RsPOA = mysqli_query($conexion, $query_RsPOA) or die(mysqli_error($conexion));
	$row_RsPOA = mysqli_fetch_array($RsPOA);
	$totalRows_RsPOA = mysqli_num_rows($RsPOA);
}


?>

<!DOCTYPE html>
<html>
<title>Proveedores</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta charset="UTF-8">
<link rel="stylesheet" type="text/css" href="css/estilo_solicitud.css" />


<style type="text/css">
	body {
		background: #fff;

	}

	#menu {
		/*background:#26B826;*/
		/*background:#4C954B;*/
		height: 50px;
		font-size: 13px;
		margin-top: -10px;
		border-radius: 13px;
	}

	#menu_proveedores {
		/* background:#26B826; */
		padding-top: 15px;
		color: #FDF8F8;
		font-weight: bold;
	}

	#menu_proveedores li {
		display: inline;
		padding-left: 15px;
		padding-right: 15px;
		padding-top: 10px;
		padding-bottom: 10px;
		background: #4C954B;
		border-radius: 13px;
		list-style-type: none;
	}

	#menu_proveedores a {
		width: 260px;
		/*background:#ff0000;*/
		text-decoration: none;
		color: #ffffff;
	}

	#menu_proveedores a:hover {
		color: #000000;
	}

	#menu_proveedores li:hover {
		background: #99F199;
		font-size: 13px;
		border-radius: 13px;
	}
</style>
<div style="margin-bottom: 10px;font-weight: bold;font-size: 15px">
	PROVEEDOR: <?php echo ($row_RsLista_prov['PROVEEDOR_NOMBRE']); ?>
</div>
<table class="tablalistado" cellspacing="2" border="0">
	<tr class="SLAB trtitle">
		<td>REQUERIMIENTO</td>
		<td>DESCRIPCION DETALLE</td>
		<td width="150">POA</td>
		<td width="150">SUB POA</td>
		<td>ORDEN</td>
		<td>FACTURA</td>

	</tr>
	<?php
	if ($totalRows_RsLista_prov > 0) {
		$j = 0;
		do {
			$j++;
			$estilo = "SB";
			if ($j % 2 == 0) {
				$estilo = "SB2";
			}
			?>
			<tr class="<?php echo ($estilo); ?>">
				<td> <a href="./home.php?page=solicitud&codreq=<?php echo ($row_RsLista_prov['REQUERIMIENTO']); ?>"
						target="_back"><?php echo ($row_RsLista_prov['REQUERIMIENTO_CODIGO']); ?></a></td>
				<td class='text-justify'><?php echo ($row_RsLista_prov['DETALLE_DESC']); ?></td>
				<td class='text-justify'><?php echo ($row_RsPOA['POA_NOM']); ?></td>
				<td class='text-justify'><?php echo ($row_RsPOA['SUBPOA_NOM']); ?></td>
				<td>
					<a target="_blank"
						href="O.php?codprov=<?php echo ($row_RsLista_prov['DETALLE_ID_PROVEEDOR']); ?>&codcomp=<?php echo ($row_RsLista_prov['ID_ORDEN']); ?>&%=2&f=<?php echo ($row_RsLista_prov['ORDEN_FIRMA']); ?>"><?php echo ($row_RsLista_prov['DETALLE_ORDEN']); ?></a>
				</td>
				<td><?php echo ($row_RsLista_prov['FACTURA']); ?> </td>
			</tr>
			<?php
		} while ($row_RsLista_prov = mysqli_fetch_array($RsLista_prov));
	}
	?>
</table>
<button type="button" onclick="tableToCSV()">
	Exportar CSV
</button>

</html>
<script>
	function tableToCSV() {

		// Variable to store the final csv data
		let csv_data = [];

		// Get each row data
		let rows = document.getElementsByTagName('tr');
		for (let i = 0; i < rows.length; i++) {

			// Get each column data
			let cols = rows[i].querySelectorAll('td,th');

			// Stores each csv row data
			let csvrow = [];
			for (let j = 0; j < cols.length; j++) {

				// Check if the cell contains an <a> element
				let aElement = cols[j].querySelector('a');
				if (aElement) {
					// Get the text content of the <a> element
					csvrow.push(aElement.textContent.trim());
				} else {
					// Get the text content of the cell
					csvrow.push(cols[j].textContent.trim());
				}
			}

			// Combine each column value with comma
			csv_data.push(csvrow.join(","));
		}

		// Combine each row data with new line character
		csv_data = csv_data.join('\n');

		// Call this function to download csv file  
		downloadCSVFile(csv_data);
	}

	function downloadCSVFile(csv_data) {
		// Create a Blob object with the CSV data
		let csvBlob = new Blob([csv_data], { type: 'text/csv' });

		// Create a link element
		let downloadLink = document.createElement('a');

		// Set the download attribute with a filename
		downloadLink.download = 'proveedor.csv';

		// Create a URL for the Blob and set it as the href attribute
		downloadLink.href = window.URL.createObjectURL(csvBlob);

		// Append the link to the document body and trigger a click to start the download
		document.body.appendChild(downloadLink);
		downloadLink.click();

		// Remove the link from the document
		document.body.removeChild(downloadLink);
	}

</script>