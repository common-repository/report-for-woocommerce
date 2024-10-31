// JavaScript Document
jQuery(function($){
	
    //$("#order_report").tablesorter(); 
	//$("#order_report").tablesorter();
	
	//alert(woo_report_object.woo_report_ajaxurl);
	$( "#frm_woo_report" ).submit(function( event ) {
		$.ajax({
			url:woo_report_object.woo_report_ajaxurl,
			data: $(this).serialize(),
			success:function(response) {
				//alert(JSON.stringify(response))
				$("._woo_report_content").html(response);
				$("#order_report").trigger("update");
				$("#order_report").trigger("appendCache");
				$("#order_report").tablesorter();					
			},
			error: function(response){
				console.log(response);
				alert(JSON.stringify(response));
				//alert("e");
			}
		}); 
		return false; 
	});
	
	$("#frm_woo_report").trigger("submit");
});