/*Top Customer*/
function ic_cr_pie_chart_top_product(response){
	try{
		var data = [[]];
		
		jQuery.each(response, function(k, v) {
		
			data[0].push([ v.product_name,parseInt(v.total_amount)]);
		});
		
		var plot1 = jQuery.jqplot ('top_product_pie_chart', data, 
		{ 
		  seriesDefaults: {
			// Make this a pie chart.
			renderer: jQuery.jqplot.PieRenderer, 
			rendererOptions: {
			  // Put data labels on the pie slices.
			  // By default, labels show the percentage of the slice.
			  showDataLabels: true
			}
		  }, 
		  legend: { show:true, location: 'e' }
		}
	  );		
	}
	catch(e){
	alert(e.message);
	}
}

/*Today Order Count*/
function ic_cr_today_order_count(response){
	 var data = [];
		
		jQuery.each(response, function(k, v) {
			s2 = [ parseInt(v.total_count)]
		});
	plot3 = jQuery.jqplot('today_order_count_meter_gauge',[s2],{
       seriesDefaults: {
           renderer: jQuery.jqplot.MeterGaugeRenderer,
           rendererOptions: {
               min: 0,
               max: 100,
               intervals:[20,40, 60, 80, 100],
               intervalColors:['#FF0000', '#cc6666', '#E7E658', '#93b75f','#66cc66']
			 }
       }
   });
}

/*Last 7 Days Sales Order*/
function ic_cr_Last_7_days_sales_order_amount(response){
	try{
		var data = [[]];
		jQuery.each(response, function(k, v) {
		
			data[0].push([ v.order_date,parseInt(v.total_amount)]);
			
		});
		
	  var plot1 = jQuery.jqplot('last_7_days_sales_order_amount', data, {
		title:'Last 7 days Sales Amount',
		
		seriesDefaults: { 
			showMarker:true,
			pointLabels: { show:true, location:'s', edgeTolerance: -20} 
		  },
		axes:{
			  xaxis:{
				renderer:jQuery.jqplot.DateAxisRenderer,
					pad:1.2,
				  tickOptions:{
					formatString:'%b&nbsp;%#d'
				  }
			  },
			  yaxis:{
				  	pad:1.2,
				tickOptions:{
				  formatString:'$%.2f'
				}
			  }
			},
			highlighter: {
			  show: false,
			   yvalues: 4,
			 
			},
			cursor: {
			  show: true,
			  tooltipLocation:'s'
			}
	  });

		}catch(e){
		alert(e.message);
	}
}

jQuery(document).ready(function($){
							//	alert("5");
	var data1 = {"action":"ic_cr_action_comman","graph_by_type":"top_product"}
	$.ajax({
		type: "POST",	   
     	data: data1,
	  	async: false,
      	url: ajax_object.ajaxurl,
      	dataType:"json",
      	success: function(response) {
		//	alert(JSON.stringify(response));
			if(response.length > 0)
			ic_cr_pie_chart_top_product(response);
      	},
	  	error: function(jqXHR, textStatus, errorThrown) {
  			alert(jqXHR.responseText);
			alert(textStatus);
			alert(errorThrown);
		 }
    });
						
	var data2 = {"action":"ic_cr_action_comman","graph_by_type":"today_order_count"}
	$.ajax({
		type: "POST",	   
     	data: data2,
	  	async: false,
      	url: ajax_object.ajaxurl,
      	dataType:"json",
      	success: function(response) {
			if(response.length > 0)
			ic_cr_today_order_count(response);
      	},
	  	error: function(jqXHR, textStatus, errorThrown) {
  			alert(jqXHR.responseText);
			alert(textStatus);
			alert(errorThrown);
		 }
    });
	
	var data3 = {"action":"ic_cr_action_comman","graph_by_type":"Last_7_days_sales_order_amount"}
	$.ajax({
		type: "POST",	   
     	data: data3,
	  	async: false,
      	url: ajax_object.ajaxurl,
      	dataType:"json",
      	success: function(response) {
			//alert("a1");
			//alert(JSON.stringify(response));
			if(response.length > 0)
			ic_cr_Last_7_days_sales_order_amount(response);
      	},
	  	error: function(jqXHR, textStatus, errorThrown) {
  			alert(jqXHR.responseText);
			alert(textStatus);
			alert(errorThrown);
		 }
    });
});
