<script type="text/javascript">
function insert()
{
alert('Nota A�adida');
}
</script>

<?php
session_start();
error_reporting (E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED & ~E_WARNING);
set_include_path('../../common/0.12-rc12/src/' . PATH_SEPARATOR . get_include_path());
require_once($_SERVER['DOCUMENT_ROOT'] . '/common/library/functions.php');
include 'Cezpdf.php';
$nota=$_POST['nota'];

							$output_dir = $_SERVER['DOCUMENT_ROOT'] . "/cvs/";

							class Creport extends Cezpdf{
								function Creport($p,$o){
									$this->__construct($p, $o,'none',array());
								}
							}
$id_ac=$_GET["id_b"];
$id_aco=$_GET["id_bb"];
$id =unserialize($_SESSION["id"]);
$id_o =unserialize($_SESSION["id_o"]);
$custom_elements =unserialize($_SESSION["custom"]);
$n_custom_elements = count($custom_elements);
if(strlen($id_ac)>0){$actual=$id[$id_ac];$ida=$id_ac;}
if(strlen($id_aco)>0){$actual=$id_o[$id_aco];$ida=$id_aco;}
$i=0;
foreach ($id_o as $valor){
if($valor == $actual){
$ind_a=$i;
$h=$i-1;
$ind_p=$h;
$j=$i+1;
$ind_n=$j;
}
$i++;
}
$enlace = connectDB();
	$output_dir = $_SERVER['DOCUMENT_ROOT'] . "/cvs/";
$enlace = mysqli_connect("localhost", "root", "", "PRJ2014001");

if (mysqli_connect_errno()) {
    printf("Fall la conexin: %s\n", mysqli_connect_error());
    exit();
}
if ($_GET[reportType] == "full_report"){
$consulta = "SELECT * from cVitaes where nie like '$actual'" ;
}
if ($_GET[reportType] == "blind_report"){
$consulta = "SELECT `postalCode`,`country`,`province`,`city`,`mobile`,`mail`,`drivingType`,`drivingDate`,`language`,`langLevel`,`occupation`,`studyType`,`studyName`,`userLogin`  from cVitaes where nie like '$actual'" ;
}
if ($n_custom_elements>0){
$consulta = "SELECT ";
foreach( $custom_elements as $value ) {
    $consulta = $consulta."`".$value."`,";
}
$consulta = $consulta."`userLogin`";
$consulta = $consulta." from cVitaes where nie like '$actual'" ;
}
if ($resultado = mysqli_query($enlace, $consulta)) {
while ($fila = $resultado->fetch_assoc()) {
		$pdf = new Cezpdf('A4'); // Seleccionamos tipo de hoja para el informe
		$pdf->selectFont('fonts/Helvetica.afm'); //seleccionamos fuente a utilizar
		$id[$fila['id']] = $fila['nie'];
		if ($fila['sex']==0){ $fila['sex'] = "hombre"; }
		if ($fila['sex']==1){ $fila['sex'] = "mujer"; }

	
									while (list($clave, $valor) = each($fila)) {
											echo "<b>$clave</b> $valor<br>";
											$pdf->ezText("<b>$clave</b> $valor");
											
									}
									if (strlen($nota)>0){$pdf->ezText("\n\nNOTA:\n\n$nota");
									}
									$documento_pdf = $pdf->ezOutput();
									chdir($output_dir);
									$pdf_file_name = "";
									$pdf_file_name = $fila['userLogin'];
									$nf=$pdf_file_name.".pdf";
									$fichero = fopen($nf,'wb') or die ("No se abrio $nf") ;
									fwrite ($fichero, $documento_pdf);
									fclose ($fichero);
}
}
echo "NOTA <br>";
echo "<form name=formu id=formu action=viewCV.php?id_b=".$ida."&reportType=".$_GET[reportType]." method=post enctype=multipart/form-data>";
echo "<textarea name=nota rows=5 cols=40></textarea><br>";
echo "<input type=submit name=enviar value=Insertar Nota onclick=\"insert();\"/><br>";
if(strlen($id_o[$ind_n])>0)
//echo "<a href=visualizacv.php?id_bb=$ind_n>SIGUIENTE</a><br>";
echo "<a href=viewCV.php?id_bb=$ind_n>SIGUIENTE</a><br>";
if(strlen($id_o[$ind_p])>0)
//echo "<a href=visualizacv.php?id_bb=$ind_p>PREVIO </a><br>";
echo "<a href=viewCV.php?id_bb=$ind_p>PREVIO </a><br>";
$_SESSION["id_o"] = serialize($id_o);
$_SESSION["id"] = serialize($id);
?>
