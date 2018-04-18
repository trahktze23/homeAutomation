<?php
// SERVICIILE  ~~ baza de date inspecial >> camere temperaturi citire dare

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);
//echo $_SERVER['QUERY_STRING'];
	require_once("clase/camera.php");

	parse_str($_SERVER['QUERY_STRING'], $qs);
	$serv = new service();
	
	//print_r($qs);
	$serv->{$qs['action']}($qs);

	class service{
		public function __construct(){}
		
		//~ public function test($qs){

			//~ }
		
		public function save_camera($qs){
			//print_r($qs);
			if(isset($qs['id_camera']) &&  $qs['id_camera']!=''){
				$camera = new camera($qs['id_camera']);
				
				if(isset($qs['nume_camera']) &&  $qs['nume_camera']!='')
					$camera->set('nume' ,$qs['nume_camera']);
				
				if(isset($qs['temp_set']) &&  $qs['temp_set']!='')
					$camera->set('temp_set' ,$qs['temp_set']);
					
				if(isset($qs['pin_out']) &&  $qs['pin_out']!='')
					$camera->set('pin_out' ,$qs['pin_out']);	
					
				
				if(isset($qs['id_senzor']) &&  $qs['id_senzor']!=''){
					$camera->set('id_senzor' ,$qs['id_senzor']);
				}
				
				//print_r($camera);
				
				$camera->save();
			}
			
		}// end action save camera
		
		public function delete_camera($qs){
			if(isset($qs['id_camera']) &&  $qs['id_camera']!=''){
				$camera = new camera($qs['id_camera']);
										
				$camera->delete();
			}
		}// end action delete camera
		//~ 
		//~ 
		public function add_new_camera($qs){
				$camera = new camera();
				
				if(isset($qs['nume_camera']) &&  $qs['nume_camera']!='')
					$camera->set('nume' ,$qs['nume_camera']);
				
				if(isset($qs['temp_set']) &&  $qs['temp_set']!='')
					$camera->set('temp_set' ,$qs['temp_set']);
				
				if(isset($qs['id_senzor']) &&  $qs['id_senzor']!=''){
					$camera->set('id_senzor' ,$qs['id_senzor']);
				//	$camera->set('adr_senzor' ,$qs['id_senzor']);
				}
				
				$camera->addNew();
						
		} 
		
		//~ 
		public function refresh(){
				$cm = new camera();
				$camere= $cm->getAll();
			
				$result=array();
			
				foreach($camere as $camera){
					
					$status_led=array();
					exec("gpio read ".$camera->get('pin_out') , $status_led);
					$in_function = ($status_led[0] ==0) ? false : true;
					
					array_push($result, array(
						'nume'=>$camera->get('nume'),
						'temp_set'=> $camera->get('temp_set'),
						'temp_act'=>$this->read_temp($camera->get('adr_senzor')),
						'id_camera'=> $camera->get('id'),
						'id_senzor'=> $camera->get('id_senzor'),
						'pin_out' => $camera->get('pin_out'),
						'in_function'=>$in_function //
					));
				}
				print_r(json_encode($result) );
			} // end of refresh function
		//~ 
		public function read_temp($sursa){
			$file = $sursa;
			$lines = file($file);
		
			$temp = explode('=',$lines[1]);
			$temp = number_format($temp[1]/1000,1,'.','');
			return $temp;
		}
		
		
		public function getLists($qs){
			$id_camera = '';
			if(isset($qs['id_camera']) &&  $qs['id_camera']!='')
				$id_camera= trim($qs['id_camera']);
			//echo ' id camera >>> '.$id_camera;
			//print_r($qs);
			//echo '<br>';
			$list_pins=array();
			$list_ids=array();
			//~ 
			$ids= scandir('/sys/bus/w1/devices');
			foreach($ids as $id){
				if($id !='' && $id !='w1_bus_master1'  && $id !='.' && $id !='..'){
				array_push($list_ids, $id);
				}
			}
			for($i=0; $i <=26 ; $i++){
				array_push($list_pins, $i);
			} 
						//~ 
			$cm = new camera();
			$camere= $cm->getAll();	
			$selected_pin ='';
			$selected_senzor_id ='';	
			foreach($camere as $camera){

				if($camera->get('id') == $id_camera){
					//echo 'dddd '.$camera->get('id');
					$selected_pin = $camera->get('pin_out'); 
					$selected_senzor_id =  $camera->get('id_senzor'); 
					
					}
				if($camera->get('id') != $id_camera){
					$nr_pin_camera = $camera->get('pin_out');
					$id_sensor_camera  = $camera->get('id_senzor');
					$index =array_search($nr_pin_camera, $list_pins); 
					if ($index >=0 )
						unset($list_pins[$index]);
					
					$index =array_search($id_sensor_camera, $list_ids); 
					if ($index >=0 )
						unset($list_ids[$index]);
					}
				} // end foreach camera	
			//~ 
			$return_result = array('ids'=> $list_ids , 'pins'=> $list_pins  ,'selected_pin'=> $selected_pin, 'selected_senzor_id'=> $selected_senzor_id );
			print_r(json_encode($return_result));
			
			
			} // end getList function
		
		
		}







?>
