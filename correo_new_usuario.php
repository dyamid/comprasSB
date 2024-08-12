<?php
require_once('conexion/db.php');
//$infomacion_codi="dyamid";
//consulta de persona destinatario 

   $query_RsPersona = "SELECT USUALOG CODIGO,
								  USUAROL ROL_ID,
								  ROLNOMB ROL_NOMBRE,
								  PERSNOMB NOMBRE,
								  PERSAPEL APELLIDO,
								  PERSUSUA USUARIO,
								  PERSEST ESTADO,
								  AREANOMB AREA,
								  POANOMB POA,
								  USUAPASSBK CLAVE,
								  PERSCORR CORREO
							FROM PERSONAS,
								 USUARIOS,
								 ROLES,
								 AREA_POA,
								 POA,
								 AREA
						 WHERE   PERSUSUA = USUALOG
						 AND     ROLCODI=USUAROL
						 AND     USUACODI= ARPOIDUS
						 AND     AREAID=ARPOIDAR 
						 AND     POACODI=ARPOIDPO
						 AND     USUALOG ='".$infomacion_codi."'";
	$RsPersona = mysqli_query($conexion,$query_RsPersona) or die(mysqli_error($conexion));
	$row_RsPersona = mysqli_fetch_array($RsPersona);
    $totalRows_RsPersona = mysqli_num_rows($RsPersona);

	$cuerpo="
<style type='text/css'>
<!--
.style7 {font-weight: bold}
.style12 {font-family: Arial, Helvetica, sans-serif; font-size: 14px; }
.style18 {
	font-size: 18px;
	font-weight: bold;
}
-->
</style>

<!DOCTYPE html>
<html>
	
	<head>
	
	<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
</head>
<table width='100%'>
	<tr>
		<td width='613'>
		<style type='text/css'>
		<!--
		.style1 {
			font-size: 50px;
			font-weight: bold;
		}
		.style4 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 14px; }
		.style7 {font-size: 18px}
		-->
		</style>
		<table width='100%' border='0'>
		  <tr>
			<td width='41%'></td>
			<td width='59%'>&nbsp;</td>
		  </tr>
		  <tr>
			<td colspan='2'><span class='style1'><h2>Reciba un cordial saludo de parte de la Corporaci&oacute;n Colegio San Bonifacio de las lanzas:<h2></span></td>
		  </tr>
		 </table>
		<p class='style7'><strong>BIENVENIDO ".$row_RsPersona['NOMBRE']." AL PORTAL DE COMPRAS,  le informamos que sus datos para ingresar a la plataformas son : 
          <BR><br>
		 USUARIO:".$row_RsPersona['CODIGO']." <br>
		 CLAVE:".$row_RsPersona['CLAVE']."
        <strong></p>
		<p class='style7'><strong>Para realizar la solicitud de  sus requerimientos 
		pueden hacerlo ingresando al siguiente link:</strong></p>
		<p><a href='http://compras.sanboni.edu.co'>compras.sanboni.edu.co</a></p>
		
		";
			
		$cuerpo = $cuerpo.
		"
		</td>
	</tr>
</table>

</html>
	

";

require 'includes/PHPMailer/PHPMailerAutoload.php';

$mail = new PHPMailer;

//$mail->SMTPDebug = 3;                               // Activación de la salida de depuración detallada

$mail->isSMTP();                                      // Gestor de correo configurado para utilizar SMTP
$mail->Host = 'smtp.gmail.com';                       // Especifique los servidores principales y de respaldo SMTP
$mail->SMTPAuth = true;                               // Habilitar la autenticación SMTP
$mail->Username = 'compras@sanboni.edu.co';           // nombre de usuario SMTP
$mail->Password = 'Sanboni2022';                     // contraseña SMTP 
$mail->SMTPSecure = 'ssl';                            // Habilitar el cifrado TLS, `también ssl` aceptado
$mail->Port = 465;                                    // Puerto TCP para conectarse a

$mail->From = 'compras@sanboni.edu.co';
$mail->FromName = 'compras.sanboni.edu.co';

$dirigido =$row_RsPersona['CORREO'];

$mail->addAddress($dirigido, $row_RsPersona['NOMBRE']);     // Añadir un destinatario
$mail->addAddress('');     		             // Nombre es opcional
$mail->addReplyTo('', 'Information');
$mail->addCC('');
$mail->addBCC('');

$mail->WordWrap = 50;                                 // Set palabra envolver 50 caracteres
$mail->addAttachment('');   			      // Añadir archivos adjuntos
$mail->addAttachment('', '');    		     // nombre opcional
$mail->isHTML(true);                                  // Formato de correo electrónico Conjunto de HTML

$mail->Subject = 'Hola, '.$row_RsPersona['NOMBRE'].' BIENVENIDO-PORTAL DE COMPRAS';
$mail->Body    = $cuerpo;
$mail->AltBody = $cuerpo;

if(!$mail->send()) {
    echo 'El mensaje no pudo ser enviado.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'El Mensaje ha Sido Enviado';
}