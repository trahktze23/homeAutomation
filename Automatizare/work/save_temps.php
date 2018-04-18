
<?php

		include_once '../clase/dbClass.php';
		include_once '../clase/camera.php';
				
		$db =  new database();
		
		//~ $camere=array('dormitor', 'sufragerie', 'bucatarie');

				$rs= new camera();
//~ $camere = camera::getAll();
		$camere = $rs->getAll();
		
		//~ print_r($camere);

		foreach($camere as $camera)
		{
			$data_temp=array();
			//echo 'ads';
			//~ if($camera=='dormitor')
				//~ $sursa='/sys/bus/w1/devices/28-000006b14b6a/w1_slave';
			//~ if($camera=='sufragerie')
				//~ $sursa='/sys/bus/w1/devices/28-00000621033b/w1_slave';
			//~ if($camera=='bucatarie')
				//~ $sursa='/sys/bus/w1/devices/28-000006b10889/w1_slave';	
			
			$sursa = $camera->get('adr_senzor');
			$temp_actual = read_temp($sursa);
		
			$date= getdate();

			$date=$date['year'].'-'.$date['mon'].'-'.$date['mday'].' '.$date['hours'].':'.$date['minutes'].':'.$date['seconds'];

			echo 'data >> '.$date. '  camera >> '.$camera->get('nume').'   temp >> '.$temp_actual;
			echo'<br>';
			
			$param=array('camera'=>$camera->get('nume'),'id_camera'=>$camera->get('id'), 'temp'=>$temp_actual, 'data'=>$date);
			
			$db->insert('temperaturi',$param);
				

		}

			
		function read_temp($sursa){
	
		$file = $sursa;
		$lines = file($file);
	
		$temp = explode('=',$lines[1]);
		$temp = number_format($temp[1]/1000,1,'.','');
		
		return $temp;
		
		}
		
		
?>
