jQuery(document).ready(function(){
			
			jQuery('.nav-menu li a').addClass('btn btn-info btn-xs');
			
			jQuery('.first_page_content').on('click','.cam',function(){
				jQuery('.first_page_content').hide();
				jQuery('.modify').show();
				getLists( jQuery(this).find(".id_camera").text() );
				jQuery(".modify input#nume").val(jQuery(this).find(".camera").text());
				jQuery('.modify input#temp_set').val(jQuery(this).find(".temp_set").text() );
				//jQuery('.modify select#senzor_id').val(jQuery(this).find(".id_senzor").text());
				jQuery('.modify input.id_camer_mod').val(jQuery(this).find(".id_camera").text());
				//jQuery('.modify select#pin_out').val(jQuery(this).find(".pin_out").text());
		
				});
			
			jQuery('#add_new').click(function(){
				getLists('');
				jQuery('.first_page_content').hide();
				jQuery('.modify').show().find('input, select').val('');
				jQuery('div.modify a.save').addClass('new');
			
				});
				
				
			window.setInterval(function(){
				
				refreshData();
				}, 5000);	
			
				jQuery('div.modify a').click(function(){
					if(jQuery(this).hasClass('new')) {
						action="add_new_camera";
						jQuery(this).removeClass('new');
						}
						
					else if(jQuery(this).hasClass('save'))
						action="save_camera";
					else if(jQuery(this).hasClass('delete'))
						action="delete_camera";
					else if(jQuery(this).hasClass('cancel')){
						action='';	
						jQuery('.first_page_content, .modify').toggle();
					}
							

					if(action !=''){
						var inputs = getInputs();
						inputs.action = action;
						jQuery.ajax({
							url:"service.php",
							data:inputs,
							methode:"GET",
							success:function(result){
								refreshData();
								jQuery('.first_page_content, .modify').toggle();
							
											
											} // end function result
							});
						
						
						
					}

					
					});
			
			
			
			
			});
			
	function getLists(id_camera){
		jQuery.ajax({
			url:"service.php",
			data:{
				action:'getLists',
				id_camera:id_camera 
				},
			methode:"GET",
			success:function(result){
				//console.log(result);
				result = JSON.parse(result);
				console.log(result);
				var ids = result.ids;
				var pins = result.pins;
				jQuery('select#senzor_id').empty();
				jQuery('select#pin_out').empty();
				var select_ids ='';		var select_pins='';
				for(var id in ids){
					if (ids[id]  == result.selected_senzor_id ){
						select_ids +='<option selected>'+ids[id]+'</option>';
						
					} 
					else {
						select_ids +='<option>'+ids[id]+'</option>';
						
					}
				} 
									
				for(var pin in pins){
					if (pins[pin] == result.selected_pin)
						select_pins +='<option selected>'+pins[pin]+'</option>';
					else
						select_pins +='<option>'+pins[pin]+'</option>';
				}	
				jQuery('#senzor_id').append(select_ids)	;
				jQuery('select#pin_out').append(select_pins);
	
			}// end function result
		});
		
		
		
		
		
		}		
	
	
	
	function getInputs(){
		var result={
			nume_camera:jQuery(".modify input#nume").val(),
			temp_set: jQuery('.modify input#temp_set').val(),
			id_senzor: jQuery('.modify select#senzor_id').val(),
			id_camera: jQuery('.modify input.id_camer_mod').val(),
			pin_out: jQuery('.modify select#pin_out').val(),
			
			};
		return result;
		}
		
		
	function load_temp(){
		
		jQuery('.camera').each(function(){
			var inputs={};
				inputs.action = 'load_temp';
				inputs.cam_id= jQuery(this).find('.id_camera').text();
				
				console.log(jQuery(this));
				
					jQuery.ajax({
						url:"service.php",
						data:inputs,
						methode:"GET",
						success:function(){
											}
							});
			
			
			
			
			
			
			});
		
		
		
		
		}	
	
	function refreshData(){
		inputs={action:'refresh'}
		jQuery.ajax({
					url:"service.php",
					data:inputs,
					methode:"GET",
					success:function(result){
						result=  JSON.parse(result);
						
						var html='';
						for(data in result){
							var nume = result[data].nume;
							var temp_set = result[data].temp_set;
							var temp_act = result[data].temp_act;
							var id_senzor = result[data].id_senzor;
							var id_camera = result[data].id_camera;
							var pin_out = result[data].pin_out;
							var in_function = (result[data].in_function) ? 'in_function' : '';
							
							html += '<div class="col-sm-4 '+in_function+' cam">	';
							html += 	'<div>';
							html += 		'<div class="camera">'+nume+' </div>   ';
							html+= 			'<div class="temp_curenta"> '+temp_act+' </div>';
							html += 		'<div class="temp_set"> '+temp_set+' </div>';
							html+=  		'<div class="id_camera" style="display:none;">'+id_camera+'</div>'; 
							html += 		'<div class="id_senzor" style="display:none;">'+id_senzor+'</div>';
							html+= 			'<div class="pin_out" style="display:none;">'+pin_out+'</div>';	
							html += 	'</div>';    
							html += '</div>';
							
							
							
							
							//jQuery('.first_page_content').append('	<div class="col-sm-4 '+in_function+' cam">	<div> <div class="camera">'+nume+' </div>    <div class="temp_curenta"> '+temp_act+' </div> <div class="temp_set"> '+temp_set+' </div>  <div class="id_camera" style="display:none;">'+id_camera+'</div>  <div class="id_senzor" style="display:none;">'+id_senzor+'</div>  <div class="pin_out" style="display:none;">'+pin_out+'</div>	</div>    </div>');		
						}
						//console.log(html);
						jQuery('.first_page_content').html('');
						jQuery('.first_page_content').html(html);
					}
		});
		
		
		
		
		
		}
	
	
