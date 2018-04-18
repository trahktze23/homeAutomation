<?php
include_once 'clase/login.php';
$login  =  new login();
if(!$login->CheckLogin())
{
	$login->RedirectToURL("../index.php");
	exit;
}
	require_once("clase/dbClass.php");
	require_once("clase/camera.php");
	//include_once 'camera.php';
?>
<!DOCTYPE html>
<html>
<!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width">

	<title> Home Automation  </title>
 	<meta charset="utf-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- jQuery libraries -->
	<script src='librarii/jquery-1.11.3.js'></script>  <!-- jQuery local    -->
	<script src='librarii/jquery-ui/jquery-ui.min.js'>  </script>
	<link rel="stylesheet" href="librarii/jquery-ui/jquery-ui.css" />

	<link rel="stylesheet" href="css/style.css" />
	<script src="scripturi/page.js"></script>

	<link rel="stylesheet" href="librarii/bootstrap.min.css">
	<script src="librarii/bootstrap.min.js"></script>
</head>


<body>



	<div id="page" class="container_page">

		<div class="header">

		</div>  <!-- end header-->

		<!-- meniu -->
		<div id="menu" class="well well-sm menu">
					<ul>
						<li>  	<a class="btn btn-info btn-xs" > Home </a>   </li>
						<li> 	<a class="btn btn-info btn-xs" > Vara/Iarna </a>  </li>
						<li>	<a id="add_new" class="btn btn-info btn-xs" >Add New</a></li>
						<li> 	<a class="btn btn-info btn-xs"  href="logout.php"> LOGOUT </a>  <li>
					</ul>
		</div>
		<!-- #menu -->


		<!--  page  content -->
		<div class="content">

<?
	$camera=new camera('');
	$camere= $camera->getAll();

	//print_r($camere);

	$html='';
	for($i=0; $i<count($camere); $i++){
			$div_row='';
			$end_row='';
		$status_led=array();
		exec("gpio read ".$camere[$i]->get('pin_out') , $status_led);
		//print_r($status_led);

		$in_function = ($status_led[0] ==0) ? '' : 'in_function';

		$html.=$div_row.'
			<div class="col-sm-4 '.$in_function.' cam">
		    	<div>
			      <div class="camera"> '.$camere[$i]->get('nume').' </div>
			      <div class="temp_curenta"> '.read_temp($camere[$i]->get('adr_senzor')).' </div>
			      <div class="temp_set"> '.$camere[$i]->get('temp_set').' </div>
			      <div class="id_camera" style="display:none;">'.$camere[$i]->get('id').'</div>
			      <div class="id_senzor" style="display:none;">'.$camere[$i]->get('id_senzor').'</div>
		    	  <div class="pin_out" style="display:none;">'.$camere[$i]->get('pin_out').'</div>

		    	</div>
		    </div>'.$end_row;
		}
		echo '<div class="first_page_content container">'.$html.'</div>';
	?>

<div class="container modify">
	  <div class="col-sm-8">

			<div class="form-group">
				    <label for="nume">Camera:</label>
				    <input type="text" class="form-control" id="nume"/>

					<label for="temp_set">Temperatura:</label>
				    <input type="text" class="form-control" id="temp_set"/>

				    <label for="senzor_id">ID senzor:</label>


				    <input type="hidden" class="id_camer_mod"/>
				    <select class="form-control" id="senzor_id">


			<?php
						$ids= scandir('/sys/bus/w1/devices');
						//print_r($ids);
						foreach($ids as $id){
							if($id !='' && $id !='w1_bus_master1'  && $id !='.' && $id !='..'){
								echo '<option>'.$id.'</option>';
							}
						}

			?>

					</select>


				<label for="pin_out"> PIN out</label>


				 <select class="form-control" id="pin_out">


			<?php
					for($i=0; $i <=26 ; $i++){
						echo '<option>'.$i.'</option>';
					}

			?>

					</select>




			</div>
				<a  href="#" class="btn save btn-primary">Save</a>
				<a  href="#" class="btn delete btn-primary">Delete</a>
				<a  href="#" class="btn cancel btn-primary">Cancel</a>
	</div>
</div>








	</div> <!-- end content page -->
</body>

</html>







<?php
	function read_temp($sursa){
			$file = $sursa;
			$lines = file($file);

			$temp = explode('=',$lines[1]);
			$temp = number_format($temp[1]/1000,1,'.','');
		return $temp;
		}

 ?>
