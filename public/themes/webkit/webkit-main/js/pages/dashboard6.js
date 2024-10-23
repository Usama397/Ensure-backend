//[Dashboard Javascript]

//Project:	WebkitX Admin - Responsive Admin Template
//Primary use:   Used only for the main dashboard (index.html)


$(function () {

  'use strict';
	
	new ApexCharts(document.querySelector("#chart-1"), {
        series: [{ data: [25, 66, 41, 89, 63, 25, 44, 12, 36, 9, 54, 89, 63, 25, 44] }],
        chart: { type: "bar", height: 75, sparkline: { enabled: !0 } },
        colors: ["#8950fc"],
        plotOptions: { bar: { columnWidth: "40%" } },
        labels: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
        xaxis: { crosshairs: { width: 1 } },
        tooltip: {
            fixed: { enabled: !1 },
            x: { show: !1 },
            y: {
                title: {
                    formatter: function (e) {
                        return "";
                    },
                },
            },
            marker: { show: !1 },
        },
    }).render();
	
	
	new ApexCharts(document.querySelector("#chart-2"), {
		 series: [{
          name: 'Net Profit',
          data: [44, 55, 57, 56, 61, 58, 63, 60, 66]
        }, {
          name: 'Revenue',
          data: [76, 85, 101, 98, 87, 105, 91, 114, 94]
        }],
        chart: { type: "bar", height: 75, sparkline: { enabled: !0 } },
        colors: ['#f64e60', '#1bc5bd'],
        plotOptions: { bar: { columnWidth: "40%" } },
        labels: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
        xaxis: { crosshairs: { width: 1 } },
        tooltip: {
            fixed: { enabled: !1 },
            x: { show: !1 },
            y: {
                title: {
                    formatter: function (e) {
                        return "";
                    },
                },
            },
            marker: { show: !1 },
        },
    }).render();
	
	
	
	new ApexCharts(document.querySelector("#chart-3"), {
		 series: [{
          name: 'Net Profit',
          data: [44, 55, 47, 80, 61, 95, 63, 85, 66]
        }, {
          name: 'Revenue',
          data: [76, 40, 90, 60, 87, 58, 91, 74, 94]
        }],
        chart: { type: "line", height: 140, sparkline: { enabled: !0 } },
		stroke: {
          width: 2,
          curve: 'smooth'
        },
		markers: {
          size: 4,
          colors: ["#ffffff"],
          strokeColors: ['#ffa800', '#8950fc'],
          strokeWidth: 2,
          hover: {
            size: 7,
          }
        },
        colors: ['#ffa800', '#8950fc'],
        plotOptions: { bar: { columnWidth: "40%" } },
        labels: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
        xaxis: { crosshairs: { width: 1 } },
        tooltip: {
            fixed: { enabled: !1 },
            x: { show: !1 },
            y: {
                title: {
                    formatter: function (e) {
                        return "";
                    },
                },
            },
            marker: { show: !1 },
        },
    }).render();
	
	
	var options = {
        series: [{
          name: 'Net Profit',
          type: 'line',
          data: [43, 45, 36, 30, 41, 35, 54, 52, 59, 36, 39, 43]
        }, {
          name: 'Revenue',
		  type: 'area',
          data: [23, 25, 16, 10, 31, 15, 34, 32, 39, 16, 19, 23]
        }],
        chart: {
			foreColor:"#bac0c7",
          height: 422,
          type: 'line',
			toolbar: {
        		show: false,
			  },
          zoom: {
            enabled: false
          },
        },
		colors: ['#f64e60', '#ede6fb'],
        dataLabels: {
          enabled: false,
        },		
		 legend: {
      		show: true,
			position: 'top',
      		horizontalAlign: 'center', 
		 },
        stroke: {
          	show: true,
			curve: 'smooth',
			lineCap: 'butt',
			colors: undefined,
			width: 4,
			dashArray: 0, 
        },
        grid: {
			show: true,
			borderColor: '#f7f7f7', 
          row: {
            colors: ['transparent'], // takes an array which will be repeated on columns
            opacity: 0
          },			
		  yaxis: {
			lines: {
			  show: true,
			},
		  },
        },
        xaxis: {
          categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
		  labels: {
			show: true,        
          },
          axisBorder: {
            show: true
          },
          axisTicks: {
            show: true
          },
          tooltip: {
            enabled: true,        
          },
        },
        yaxis: {
          labels: {
            show: true,
            formatter: function (val) {
              return val + "K";
            }
          }
        
        },
      };
      var chart = new ApexCharts(document.querySelector("#charts_widget_2_chart"), options);
      chart.render();
	
	
}); // End of use strict
