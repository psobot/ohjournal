var frequency, length;
$(document).ready(function() {
   frequency = new Highcharts.Chart({
      chart: {
         renderTo: 'frequency',
         plotBackgroundColor: null,
         plotBorderWidth: null,
         plotShadow: false
      },
      title: {
         text: 'Most-used words (longer than 5 letters)'
      },
      tooltip: {
		   formatter: function() {
			  return '"<b>'+ this.point.name +'</b>" used '+ this.y +' times';
		   }
      },
      plotOptions: {
         pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
               enabled: true,
               color: '#000000',
               connectorColor: '#000000',
               formatter: function() {
                  return '"<b>'+ this.point.name +'</b>" used '+ this.y +' times';
               }
            }
         }
      },
       series: [{
         type: 'pie',
         name: 'Browser share',
         data: frequency 
      }]
   });
	ratings = new Highcharts.Chart({
      chart: {
         renderTo: 'ratings',
		 zoomType: 'x',
         defaultSeriesType: 'line',
      },
      title: {
         text: 'Rating of Entries',
      },
	  legend: {enabled: false},
      yAxis: {
         title: {
            text: 'Rating'
         },
		 min: -100,
		 max: 100,
         plotLines: [{
            value: 0,
            width: 1,
            color: '#808080'
         }]
      },
	  xAxis: {
	  	type: "datetime",
		maxZoom: 14 * 24 * 3600000
	  },
	  tooltip:{
		formatter: function(){
			return "<strong>"+this.y+"%</strong> on "+Highcharts.dateFormat("%b %e, %Y", this.x);
		}
	  },
      series: [{
	  		name: "Rating",
			data: ratings,
            marker: {
                enabled: false
            },
            point: {
                events: {
                    click: function() {
                        location.href = "read#"+Highcharts.dateFormat("%Y-%m-%d", this.x);
                    }
                }
            }			
		}]
   });
	length = new Highcharts.Chart({
      chart: {
         renderTo: 'length',
		 zoomType: 'x',
         defaultSeriesType: 'line',
      },
      title: {
         text: 'Entry Length',
      },
	  legend: {enabled: false},
      yAxis: {
         title: {
            text: 'Number of words in entry'
         },
		 min: 0,
         plotLines: [{
            value: 0,
            width: 1,
            color: '#808080'
         }]
      },
	  xAxis: {
	  	type: "datetime",
		maxZoom: 14 * 24 * 3600000
	  },
	  tooltip:{
		formatter: function(){
			return "<strong>"+this.y+"</strong> words written on "+Highcharts.dateFormat("%b %e, %Y", this.x);
		}
	  },
      series: [{
	  		name: "Words written",
			data: lengths,
            marker: {
                enabled: false
            },
            point: {
                events: {
                    click: function() {
                        location.href = "read#"+Highcharts.dateFormat("%Y-%m-%d", this.x);
                    }
                }
            }			
		}]
   });
	entriesPerMonth = new Highcharts.Chart({
      chart: {
         renderTo: 'entriesPerMonth',
         defaultSeriesType: 'area'
      },
      title: {
         text: 'Entries Per Month'
      },
      xAxis: {
		 type: 'datetime',
      },
      yAxis: {
         title: {
            text: 'Number of Entries'
         },
      },
	  legend: {enabled: false},
      plotOptions: {
         area: {
            lineColor: '#ffffff',
            lineWidth: 1,
            marker: {
               lineWidth: 1,
               lineColor: '#ffffff'
            }
         }
      },
	  tooltip:{
		formatter: function(){
			return "<strong>"+this.y+"</strong> entries written in "+Highcharts.dateFormat("%B %Y", this.x)+"<br>over <strong>"+this.point.options.possible+"</strong> days ("+Math.round((this.y/this.point.options.possible) * 100)+"%).";
		}
	  },
      series: [{name: "Entries per Month", data: entriesPerMonth}]
   });
});

