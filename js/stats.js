var frequency;
$(document).ready(function() {
   frequency = new Highcharts.Chart({
      chart: {
         renderTo: 'chart',
         plotBackgroundColor: null,
         plotBorderWidth: null,
         plotShadow: false
      },
      title: {
         text: 'Top twenty words by frequency'
      },
      tooltip: {
         formatter: function() {
            return '<b>'+ this.point.name +'</b>: '+ this.y +' times';
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
                  return '<b>'+ this.point.name +'</b>: '+ this.y +' times';
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
});

