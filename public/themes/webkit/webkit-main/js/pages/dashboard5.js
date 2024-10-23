//[Dashboard Javascript]

//Project:	WebkitX Admin - Responsive Admin Template
//Primary use:   Used only for the main dashboard (index.html)


$(function () {

  'use strict';
	
	var options = {
          series: [{
          name: 'Net Profit',
          data: [44, 55, 57, 56, 61, 58, 63, 66, 72, 77, 82, 90]
        }, {
          name: 'Revenue',
          data: [76, 85, 101, 98, 87, 105, 91, 88, 100, 95, 105, 120]
        }],
          chart: {
          type: 'bar',
		  foreColor:"#bac0c7",
          height: 350,
			  toolbar: {
        		show: false,
			  }
        },
        plotOptions: {
          bar: {
            horizontal: false,
            columnWidth: '40%',
			startingShape: 'flat',
            endingShape: 'rounded',
          },
        },
        dataLabels: {
          enabled: false,
        },
		grid: {
			show: false,			
		},
        stroke: {
          show: true,
          width: 2,
          colors: ['transparent']
        },
		colors: ['#6993ff', '#e1f0ff'],
        xaxis: {
          categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
			
        },
        yaxis: {
          
        },
		 legend: {
      		show: true,
			position: 'bottom',
      		horizontalAlign: 'center', 
		 },
        fill: {
          opacity: 1
        },
        tooltip: {
          y: {
            formatter: function (val) {
              return "$ " + val + " thousands"
            }
          },
			marker: {
			  show: false,
		  },
        }
        };

        var chart = new ApexCharts(document.querySelector("#recent_trend"), options);
        chart.render();
	
		
	
		var options = {
        series: [{
          name: 'Net Profit',
          data: [240, 230, 480, 200, 480, 250, 380]
        }, {
          name: 'Revenue',
          data: [230, 480, 190, 390, 220, 450, 500]
        }],
        chart: {
			foreColor:"#bac0c7",
          height: 350,
          type: 'line',
			toolbar: {
        		show: false,
			  },
          zoom: {
            enabled: false
          },
			dropShadow: {
				enabled: true,
				enabledOnSeries: undefined,
				top: 20,
				left: 0,
				blur: 15,
				color: '#162d67',
				opacity: 0.20
			},
        },
		colors: ['#6993ff', '#e1f0ff'],
        dataLabels: {
          enabled: false,
        },		
		 legend: {
      		show: true,
			position: 'bottom',
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
			show: false,
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
          categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
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
	
	
	var options = {
          series: [44, 55, 41, 17],
			labels: ['Social', 'Email', 'New Join', 'Active'],
          chart: {
			  width: 250,
			  type: 'donut',
			},
		dataLabels: {
    		enabled: false,
		},
		  plotOptions: {
			pie: {
			  donut: {
				size: '80%',
				  labels: {
				  show: true,
				  name: {
				  },
				  value: {
				  }
				}
			  },
			}
		  },
		colors: ['#6993ff', '#1bc5bd', '#8950fc', '#ffa800'],
        responsive: [{
          breakpoint: 480,
          options: {
            chart: {
              width: 200
            },
            legend: {
              show: false
            }
          }
        }],
		
            legend: {
              show: false
            }
        };

        var chart = new ApexCharts(document.querySelector("#charts_widget_3_chart"), options);
        chart.render();
	
	var options = {
		  chart: {
			height: 190,
			type: "radialBar"
		  },

		  series: [67],
			colors: ["#671efb"],
		  plotOptions: {
			radialBar: {
			  hollow: {
				margin: 15,
				size: "70%"
			  },

			  dataLabels: {
				showOn: "always",
				name: {
				  offsetY: -10,
				  show: false,
				  color: "#888",
				  fontSize: "13px"
				},
				value: {
				  color: "#111",
				  fontSize: "30px",
				  show: true
				}
			  }
			}
		  },

		  stroke: {
			lineCap: "round",
		  },
		  labels: ["Progress"]
		};

		var chart = new ApexCharts(document.querySelector("#revenue5"), options);

		chart.render();
	
	
}); // End of use strict
