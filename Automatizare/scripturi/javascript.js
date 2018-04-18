

jQuery(document).ready(function(){
	
	var azi =new Date();
	var data = (azi.getFullYear()).toString()+'-'+(azi.getMonth()+1).toString()+'-'+ (azi.getDate()).toString();
	
	var camera='Dormitor';
	jQuery('#set_temp').spinner('option','step',0.1);
	jQuery('#set_temp').spinner('option','min',10);
	jQuery('#set_temp').spinner('option','max',35);
	
	
	
	jQuery('#date').datepicker({
		//~ altFormat:'yy-mm-dd',
		//~ dateFormat:'yy-mm-dd',		
		});

	jQuery('#date').datepicker('option', 'altFormat', 'yy-mm-dd');	
	jQuery('#date').datepicker('option', 'dateFormat', 'yy-mm-dd');	
	jQuery('#date').datepicker({defaultDate:data});
	
	//~ jQuery('#date').datepicker({}).bind('change', function(){
				//~ var minValue = jQuery(this).val();
				//~ minValue =$.datepicker.paseDate('yy=mm=dd', minValue);
				//~ minValue.setDate(minValue.getDate()+1);
				//~ console.log(date)
		//~ 
		//~ 
		//~ 
		//~ 
		//~ });
	

	jQuery('.leftmenu_main').click(function(){
		camera=jQuery(this).attr('id');      // setarare camera curenta in momentul apasari pe ea
		//console.log(camera);
		getTemp(camera);
		geTempSetata(camera);
		console.log(jQuery('#date').datepicker().val());
		
		var datapick = jQuery('#date').datepicker().val(); // data culeasa de la datepicker
		getMedii(camera,datapick);

		});	
		
	setInterval(function(){getTemp(camera);},3000);    // cere/citeste temp la 3 secunde
	
		
	jQuery('#send_temp').click(function(){   // setare temperatura
			
		var temp =jQuery('#set_temp').spinner('value');
		setTemp(temp, camera);
		geTempSetata(camera);
		//console.log(temp);		
		});


	jQuery('#log_out').click(function(){
			
			jQuery.ajax({
			url:"server.php",
			data:{logOut:0},
			methode:"GET",
			success:function(){
				window.location.href='index.php';
				
				}
			});
					
		});

	//~ jQuery('#date').datepicker({
		//~ 
				//~ onSelect: function(value, date){
						//~ console.log('TTTTTTTTTTTTTTT>>>  ', this);
								//~ }
		//~ 
				//~ });
			




	
	}); // end on ready function
	
	
	
	function getMedii(camera, date)
	{
		var graf={date:date, cam:camera};
		set_temp=JSON.stringify(graf);
		var ret='';
		//~ console.log('get grafi ');
			jQuery.ajax({
			url:"server.php",
			data:{getMedii:graf},
			methode:"GET",
			success:function(data){
				
				//~ console.log(data);
				//~ return data;
				ret=data;
				}
			});
		
		
		setTimeout(function(){
			
			console.log(ret);
			makeGraf(ret);
			return ret;
			
			},600 );
	}
	
	
	
	function makeGraf(data)
	{
		
	console.log(data);
	var dat= JSON.parse(data);
	console.log(dat[0]);
		for (proprety in dat[0])
		{var key = proprety;}
		
		console.log(key);
		
		console.log(dat[0][key]);
		
			 var chart = new CanvasJS.Chart("grafic", 
			 {
				width:700, 
				height:300,
				theme:'theme3',

				 
              data: [
              {
                  type: "column",
                  showInLegend: true,
                  legendText:'Temp medie '+key,
                  dataPoints: dat[0][key],
              },
              
              {
                  type: "column",
                  showInLegend: true,
                  legendText:'Temp medie exterior',
                  dataPoints: dat[1]['exterior'],
              },
              
              {
                  type: "line",
                  showInLegend: true,
                  legendText:'Putere consumata pe ora',
                  dataPoints: dat[2]['consum'],
              },
              
              
              
              
              ]
          }
          
          
          
          
          );
 
           chart.render();
		
		
		
		
	}
	
	
	
	
	
	
	
	
	function geTempSetata(camera)
	{
		jQuery.ajax({
			url:"server.php",
			data:{getTempSetat:camera},
			methode:"GET",
			success:function(data){
				
				jQuery('#set_temp').spinner('value',data);
				}
			});
		
		
		
	}
	
	
	function getTemp(camera)  // cerere ajax catre server pt servire tempetatura
	{
		jQuery.ajax({
			url:"server.php",
			data:{cam:camera},
			methode:"GET",
			success:function(data){
				console.log(data);
				jQuery('#temp > span').html(data);  // schimba val temp in pagina
				jQuery('#camera > span').html(camera); // schimba numele camerei in pag
				
				}
			});
	};
	
	function setTemp(val_temp, camera){  // trimite temperatura sisetat si camera la server pt 
										 //stocare in baza de date
		
		var set_temp={temp:val_temp, cam:camera};
		set_temp=JSON.stringify(set_temp);
		console.log(set_temp);
		
		jQuery.ajax({
			url:"server.php",
			data:{setTemp: set_temp},
			methode:"GET",
			
			});
		
		
		};
		
		
	
		
