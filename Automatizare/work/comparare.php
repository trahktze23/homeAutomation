<?php


	//system("gpio mode 0 out");
	//system("gpio write 0 0");

		include_once '../clase/dbClass.php';// 1 am inclus clasa 
		include_once '../clase/camera.php';

		$db =  new database();// 2 am apelat constructorul adica am realizat conexiunea am crat obiectul db
		system("gpio mode 0 out"); 		
		system("gpio mode 1 out");
		system("gpio mode 2 out");
		$data_temp=array();// am creat un array

		$camera='dormitor';
		$camera_led=array('dormitor'=>2, 'sufragerie'=>1 , 'bucatarie'=>0);
		
		$date_time= getdate();
		
		$luna =($date_time['mon']<10) ? '0'.$date_time['mon'] : $date_time['mon'];
		$zi =($date_time['mday']<10) ? '0'.$date_time['mday'] : $date_time['mday'];
		$azi=$date_time['year'].'-'.$luna.'-'.$zi;
		$date=$azi.' '.$date_time['hours'].':'.$date_time['minutes'].':'.$date_time['seconds'];
		
print_r($luna);
				//~ $date= date('Y-m-d H:i:s');
				//~ echo $date;
				
		//for( i = 0; i<sizeof($camera_led), i++)

		$rs= new camera();
//~ $camere = camera::getAll();
		$camere = $rs->getAll();
		
	//print_r($camere);
		foreach($camere as $camera)

//echo '>>>>>>>>>>>>>>>> '; print_r($rs->getAll());
		//~ foreach($camera_led as $key=>$value)// camera => idcamera
		{
			
			$key = $camera->get('nume');
			$value = $camera->get('pin_out');
			$sursa = $camera->get('adr_senzor');
			$temp_setat= $camera->get('temp_set');
			$id_camera = $camera->get('id');
			
echo( '<br>**********<br>	');		
			$status_led=array();

			$data_temp=array();
			//echo 'ads';
			//~ if($key=='dormitor')
				//~ $sursa='/sys/bus/w1/devices/28-000006b14b6a/w1_slave';
			//~ if($key=='sufragerie')
				//~ $sursa='/sys/bus/w1/devices/28-00000621033b/w1_slave';
			//~ if($key=='bucatarie')
				//~ $sursa='/sys/bus/w1/devices/28-000006b10889/w1_slave';	
			
			
			
	//~ echo $key.'=> '.$value;
			
			$temp_actual = read_temp($sursa);
	
			
		
			//~ $sql='SELECT * FROM setari WHERE tip="temp" and owner="'.$key.'" ';
			//~ $data =$db->select('','',$sql);
			//~ 
			//~ //echo $sql.'<br>';
				//~ 
			//~ foreach ($data as $tr){
				//~ array_push($data_temp,$tr);
			//~ }
			//~ $temp_setat= $data_temp[0]['val'];
		
	//	print_r($temp_setat);
	//	echo '<br><br>';
	echo 'temp in camrea '.$key.' este '.$temp_actual.' vs temp set >> '.$temp_setat.'<br>';	
			//echo $key.' temp set>> '.$temp_setat.'- temp actual >>'.$temp_actual ;
			
			if($temp_setat >= ($temp_actual+ 0.2) ) //  aprinde led
				{
					$id_cam =$value;
		echo '<br>camera >> '.$key;
					exec("gpio read ".$id_cam , $status_led);
					if($status_led[0]== 0)// doar daca ledul e stins il aprinde si pune in baza de date
					{
						system("gpio write ".$value." 1");  // aprinde ledul daca era stins
						echo '<br>aprinde led';
						echo $azi;
						// daca este o noua zi
						$sql='SELECT * FROM consum WHERE zi="'.$azi.'" and id_camera="'.$id_camera.'" '; //.. si camera e key 
						$data =$db->select('','',$sql);
						
						echo ('<br>query verif sel dace e zi noua>> '.$db->query);
						if($data->rowCount() ==0)  // if new day
						{
							$param = array('id_camera'=>$id_camera,'camera'=>$key, 'timp_start'=>$date, 'zi'=>$azi);
							$db->insert('consum',$param);
							
						}
						else // ziua curenta doar face upate la timp de aprindere
						{
							$param=array('timp_start'=>$date, 'camera'=>$key);
							$db->update('consum',$param,'zi',$azi."' AND id_camera='".$id_camera);
							echo ('<br>update tabel '.$key.' ziua curenta>> '.$db->query);
						}
						
					}	
					else
					{			
							echo '<br>ledul '.$key.' e deja aprins'. $date;
					}
					
						
					//echo 'led'.$value.' pe 1 ';
//echo 'temp set: '.$temp_setat. '>= temp actual: '.$temp_actual; 
		echo ' ~~~~~~~~~';			
				}
	
			if($temp_setat <= ($temp_actual- 0.2 ) ) //  stinge led
				{
		echo '^^^&&&&^';			
					exec("gpio read ".$value, $status_led);
					if($status_led[0]== 1)  // daca ledul era aprins
					{
						system("gpio write ".$value." 0"); // aprinde ledul
						
						$sql='SELECT * FROM consum WHERE zi="'.$azi.'" and id_camera="'.$id_camera.'" '; //.. si camera e key 
						$data =$db->select('','',$sql);
						
						echo ('<br>query verif sel dace e zi noua>> '.$db->query);
						if($data->rowCount() ==0) 
						{
								echo '<br>led '.$key.' stins zi noua';
								$ieri = date('Y-m-d', mktime(0,0,0, date('m'), date('d')-1, date('Y') ));
								//verificam daca exista ziua de ieri in baza de date
								$sql='SELECT * FROM consum WHERE zi="'.$ieri.'" and id_camera="'.$id_camera.'" '; 
								$data =$db->select('','',$sql);
								if($data->rowCount() ==1) // daca exista inregistrari din ziua de ieri 
								{
									$data_temp= array();
									foreach ($data as $tr)
									{
										array_push($data_temp,$tr);
									}
									echo '<br> zi noua exista ziua de ieri>>= '.$ieri;
									print_r($data_temp);
															
									$timp_stop= new DateTime($ieri.' 23:59:59');
									$timp_start = new DateTime($data_temp[0]['timp_start']);
									
									//~ $dif_timp =  strtotime(strval($timp_stop->data)) - strtotime(strval($timp_start->data))  ;
									echo '<br>';
									print_r($timp_start); echo '<br>';
									print_r($timp_stop);  echo '<br>';
									
									echo $ieri.' 23:59:59 <br>';
									echo $data_temp[0]['timp_start'].'<br>';
									$str1 = $timp_start->date; echo $str1.'<br>';
									$str2 = $timp_stop->date; echo $str2.'<br>';
									
									$strval1 = strval($str1); echo $strval1.'<br>';
									$strval2 = strval($str2); echo $strval2.'<br>';
									
									$start = strtotime($strval1); echo $start.'<br>';
									$stop = strtotime($strval2); echo $stop.'<br>';
									
									$dif_timp = $stop -$start;
									$durata_functionare_zi  = $data_temp[0]['durata'] + $dif_timp;
									
									
									$ecarts =$data_temp[0]['ecarts'];
									if ($ecarts =='') // daca nu exista arrayul
									{
										$ec = array();
										array_push($ec, array('start'=>substr($data_temp[0]['timp_start'],11),'stop'=>'23:59:59'));
										$db_ecart = json_encode($ec);	
									}
									else{
										$ecarts = json_decode($ecarts);
										array_push($ecarts, array('start'=>substr($data_temp[0]['timp_start'],11),'stop'=>'23:59:59'));
										$db_ecart = json_encode($ecarts);	
									}
							
									
									
									$param=array('durata'=>$durata_functionare_zi, 'ecarts'=>$db_ecart, 'camera'=>$key);
									$db->update('consum',$param,'zi',$ieri."' AND id_camera='".$id_camera);
									echo '<br> update qsl>> '.$db->query; 
									
									echo '<br> ieri: '.$ieri; 
									echo '<br> pus in ieri durata>> '.$durata_functionare_zi;
									echo '<br>timp stop: '.$timp_stop->data.' timp start> '.$timp_start->data.'dif timp: '.$dif_timp;
									
									// creare rand nou pentru ziua de azi (pt ca el nu exista)
									// scadem de la ora curenta pana la ora 00:00:00 = durata de func a led pt noua zi
									
									$ora = substr($date, 11);
									$r = date_parse($ora);
									print_r($r);
									$sec = $r['hour']*3600 + $r['minute']*60 +$r['second'];
									
									
									$durata_functionare_zi =  $data_temp[0]['durata'] + $dif_timp;
					
										$ec = array();
										array_push($ec, array('start'=>'00:00:00','stop'=>$ora));
										$db_ecart = json_encode($ec);	
																
									
									
									//~ ,'timp_start'=>$date
									$param = array('camera'=>$key, 'id_camera'=>$id_camera,'durata'=>$sec ,'ecarts'=>$db_ecart ,'zi'=>$azi);
									$db->insert('consum',$param);
							
									
									
								}
						} // if new day
						
						else  // ledul se stinge in aceasi zi, doar se updateaza tabelul
						
						{
							$ora_cutrenta = substr($date, 11); // ora curenta
								$r= date_parse($ora_cutrenta);
								$sec_curent = $r['hour']*3600 + $r['minute']*60 +$r['second'];
							
							$data_temp=array();
							foreach ($data as $tr)
							{
								array_push($data_temp,$tr);
							}
							print_r($data_temp);
							
							$ora_aprindere = substr($data_temp[0]['timp_start'], 11); // ora curenta
								$r = date_parse($ora_aprindere);
								$sec_aprindere = $r['hour']*3600 + $r['minute']*60 +$r['second'];						
							
							echo '<br>sec curente:'.$ora_cutrenta.' >> '.$sec_curent;
							echo '<br> ces aprindere'.$ora_aprindere.' >> '.$sec_aprindere; 
							
							$dif_timp  =$sec_curent - $sec_aprindere;
							
							
							echo 'diferente de timp:>> '.$dif_timp;
							
							$durata_functionare_zi =  $data_temp[0]['durata'] + $dif_timp;
							$ecarts =$data_temp[0]['ecarts'];
							if ($ecarts =='') // daca nu exista arrayul
							{
								$ec = array();
								array_push($ec, array('start'=>$ora_aprindere,'stop'=>$ora_cutrenta));
								$db_ecart = json_encode($ec);	
							}
							else{
								$ecarts = json_decode($ecarts);
								array_push($ecarts, array('start'=>$ora_aprindere,'stop'=>$ora_cutrenta));
								$db_ecart = json_encode($ecarts);	
							}
							
														
							$param=array('durata'=>$durata_functionare_zi, 'ecarts'=>$db_ecart, 'camera'=>$key);
							$db->update('consum',$param,'zi',$azi."' AND id_camera='".$id_camera);
							
							echo 'led '.$key.' stins zi curenta';
						
						
						
						
						
						}
						
						// face adunarea
						
						
						// update in baza de date la ziua respectiva pe camera respectiva
						
					}
					else
					{
					echo '<br>ledul '.$key.' este deja stins';	
					}
					
					
					//echo 'led'.$value.' pe 0 ';
//echo 'temp set: '.$temp_setat. '< temp actual: '.$temp_actual; 
				}	
		} // end foreach camera	
		
/*
	pt executare functie de timp
	functie bash
	
	* * * * * ~/functie.sh
	
	functie.sh:
	(sleep 30 &&  comanda_terminal)&
	(sleep 60 &&  comanda_terminal)&
 sudo /usr/bin/wget 192.168.0.220/comparare.php


*/
		
	

		function read_temp($sursa){
	
		$file = $sursa;
		$lines = file($file);
	
		$temp = explode('=',$lines[1]);
		$temp = number_format($temp[1]/1000,1,'.','');
		
		return $temp;
		
		}
	

?>
