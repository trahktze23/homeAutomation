<?php

include_once 'dbClass.php';


class camera{
	protected $id='';
	protected $database_table="camere";
	protected $temp_set=0;
	protected $id_senzor='';
	protected $adr_senzor='';
	protected $nume='';
	protected $pin_out='';
	
	
	
	public function __construct($id=''){
		
		if($id!=''){
			$db1= new database();	
			$data_temp=array();
			$sql='SELECT * FROM '.$this->database_table.' WHERE id="'.$id.'" ';
			
			$data =$db1->select('','',$sql);
					
			
				foreach ($data as $tr){
					array_push($data_temp,$tr);
				}
				$camera= $data_temp[0];
				
			
				foreach($camera as $key=>$value){
					if(is_string($key)){
						$this->set($key,$value);
						}
				}		
				
		}
	}
	
	public function get($proprety){
		return $this->$proprety;
		}
	
	
	public function set($proprety,$val){
		$this->$proprety=$val;	
		return $this->$proprety;		
		}
	
	public function delete(){
		$db =  new database();
		$db->delete($this->database_table, 'id', $this->id);
		
		}
	
	
	public function getAll(){ // returneaza totatemaerere  din baza de date
		$camere = array();
		$db =  new database();
		$rows=$db->select($this->database_table,'id');
		foreach ($rows as $tr){
				array_push($camere,new camera($tr['id']));	
				}
		return $camere;
		
		}
	
	

	public function save(){    // daca exista id camera pune modificari in baza de date (update) daca nu  face insert
		$db =  new database();
		
		//print_r($this);
		if($this->id){ 
		$param=array(
				'nume'=>trim($this->nume),
				'temp_set'=>trim($this->temp_set),
				'id_senzor'=>$this->id_senzor, 
				'pin_out'=>$this->pin_out,
				'adr_senzor'=>'/sys/bus/w1/devices/'.$this->id_senzor.'/w1_slave'
					);
		
		$db->update($this->database_table, $param, 'id', $this->id);
		}
		else
			$this->addNew();
	}
	
	
	public function addNew(){
		$db =  new database();
		$param=array(
				'nume'=>trim($this->nume),
				'temp_set'=>trim($this->temp_set),
				'id_senzor'=>$this->id_senzor,
				'pin_out'=>$this->pin_out,
				'adr_senzor'=>'/sys/bus/w1/devices/'.$this->id_senzor.'/w1_slave'
				);
		$db->insert( $this->database_table, $param);		
		
		
		}
	
	
}




?>
