<?php
	$stare_pin = 0;
	include_once 'dbClass.php';
	include_once 'camera.php';
	
	$db =  new database();
	$rs= new camera();

	$camere = $rs->getAll();
		
		
	$date_time= getdate();
	$luna =($date_time['mon']<10) ? '0'.$date_time['mon'] : $date_time['mon'];
	$zi =($date_time['mday']<10) ? '0'.$date_time['mday'] : $date_time['mday'];
	$azi=$date_time['year'].'-'.$luna.'-'.$zi;  // an luna zi
	$date=$azi.' '.$date_time['hours'].':'.$date_time['minutes'].':'.$date_time['seconds']; // an luna zi ora min sec
	
			system("gpio mode 0 out"); 		
		//system("gpio mode 1 out");
		//system("gpio mode 2 out");
	
	
	foreach($camere as $camera){
		
		
			$nume_camera = $camera->get('nume');
			$pin = $camera->get('pin_out');
			$sursa = $camera->get('adr_senzor');
			$temp_setat= $camera->get('temp_set');
			$temp_camera= read_temp($sursa);
			$id_camera = $camera->get('id');
			
			
			
			exec("gpio read ".$pin , $stare_pin);
			$stare_pin = $stare_pin[0];
			
			
			echo 'camrea >>'.$nume_camera." temp cam: ".$temp_camera." temp set: ". $temp_setat.'<br>' ;
			echo "stare led ".$stare_pin;
			
			
			if($temp_setat >= ($temp_camera+0.2 ) ){
				
				echo "<br> temp set > temp camrea ";
				// aprinde ledul daca nu e deja aprins
				// pune in baza de date ora de cand s-a aprins
				
				// daca ledul e aprins treci peste
				if( !$stare_pin ) { // dsca ledul e stins
					
					echo "<br> aprinde ledul";
					
					set_pin($pin, 1); //aprinde led
					//verificam  daca este o noua zi ne uitam dca exista ceva  in bdd pt ziua de azi pt camere asta
					$sql='SELECT * FROM consum WHERE zi="'.$azi.'" and id_camera="'.$id_camera.'" '; //.. si camera e key 
					$data =$db->select('','',$sql);
						
					//echo ('<br>query verif sel dace e zi noua>> '.$db->query);
					if($data->rowCount() ==0) { // nu e nimic in bdd deci e zi noua 
						echo "aprinde led zi noua<br>";
						$param = array('id_camera'=>$id_camera,'camera'=>$nume_camera, 'timp_start'=>$date, 'zi'=>$azi);
						$db->insert('consum',$param);
						echo ('<br>update tabel '.$nume_camera.' ziua noua>> '.$db->query);
					}
					else { // ziua curenta doar face upate la timp de aprindere
						echo "<br>aprinde led zi curenta";
						$param=array('timp_start'=>$date, 'camera'=>$nume_camera);
						$db->update('consum',$param,'zi',$azi."' AND id_camera='".$id_camera);
						echo ('<br>update tabel '.$nume_camera.' ziua curenta>> '.$db->query);
						}
						
				}	
				else
				{			
					echo '<br>ledul '.$nume_camera.' e deja aprins'. $date;
				}
				
			}// end if tem camere mai mica ca cea setata
			
			
			if( $temp_setat  <= ($temp_camera-0.2) ) {
				
				echo '<br>temp set < temp camera';
				
				// doar daca ledul e aprins, el trebe sa se stinga
				if($stare_pin) { //trece din stare aprinsa in stare oprin si set bdd
				
					echo "<br>stinge ledul";
					set_pin($pin , 0); // stinge led	
					
					// setam baza de date
					// verificam caad s-a schiimbat ziua, dca e zi noua
					$sql='SELECT * FROM consum WHERE zi="'.$azi.'" and id_camera="'.$id_camera.'" '; //.. si camera e key 
					$data =$db->select('','',$sql);
						
					//	echo ('<br>query verif sel dace e zi noua>> '.$db->query);
					if($data->rowCount() ==0) 
					{
						echo "<br>stinge led zi noua";
						
						$ieri = date('Y-m-d', mktime(0,0,0, date('m'), date('d')-1, date('Y') ));
						//verificam daca exista ziua de ieri in baza de date
						// citim datele din bdd de ieri
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
							//echo '<br>';
							//print_r($timp_start); echo '<br>';
							print_r($timp_stop);  echo '<br>';
									
							//echo $ieri.' 23:59:59 <br>';
							//echo $data_temp[0]['timp_start'].'<br>';
							
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
							//echo '<br> update qsl>> '.$db->query; 
									
							//echo '<br> ieri: '.$ieri; 
							//echo '<br> pus in ieri durata>> '.$durata_functionare_zi;
							//echo '<br>timp stop: '.$timp_stop->data.' timp start> '.$timp_start->data.'dif timp: '.$dif_timp;
									
					// 2. creare rand nou pentru ziua de azi (pt ca el nu exista)
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
							$param = array('camera'=>$nume_camera, 'id_camera'=>$id_camera,'durata'=>$sec ,'ecarts'=>$db_ecart ,'zi'=>$azi);
							$db->insert('consum',$param);
							
									
									
								}
						} // end if led s-a stins in ziua urmatuare, if new day
						
						else {
							
							echo "<br stinge led zi curenta>";
							
						//daca ledul s-a stins in aceasi zi doar facem update le bdd	
							$ora_cutrenta = substr($date, 11); // ora curenta
							$r= date_parse($ora_cutrenta);
							$sec_curent = $r['hour']*3600 + $r['minute']*60 +$r['second'];
							
							$data_temp=array();
							foreach ($data as $tr)
							{
								array_push($data_temp,$tr);
							}
							//print_r($data_temp);
							
							$ora_aprindere = substr($data_temp[0]['timp_start'], 11); // ora curenta
							$r = date_parse($ora_aprindere);
							$sec_aprindere = $r['hour']*3600 + $r['minute']*60 +$r['second'];						
							
							//echo '<br>sec curente:'.$ora_cutrenta.' >> '.$sec_curent;
							//echo '<br> ces aprindere'.$ora_aprindere.' >> '.$sec_aprindere; 
							
							$dif_timp  =$sec_curent - $sec_aprindere;
							
							
							//echo 'diferente de timp:>> '.$dif_timp;
							
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
							
														
							$param=array('durata'=>$durata_functionare_zi, 'ecarts'=>$db_ecart, 'camera'=>$nume_camera);
							$db->update('consum',$param,'zi',$azi."' AND id_camera='".$id_camera);
							
							echo '<br>led '.$nume_camera.' stins zi curenta';
					
						} // end if led stins in zi curenta

					
					
				}
				else {
					echo  '<br>ledul e deja stins';
				}
				
		
			} // end if temp set < temp actuala
			
			
		
		
		
		
		
		
		
		
		}// end foreach camera
	
	
	
	
		function set_pin($nr_pin, $val_setare){
			system("gpio write ".$nr_pin." ".$val_setare); 
		}
	
	
	
	
		function read_temp($sursa){
	
		$file = $sursa;
		$lines = file($file);
	
		$temp = explode('=',$lines[1]);
		$temp = number_format($temp[1]/1000,1,'.','');
		
		return $temp;
		
		}
