var ShareGraphprojProf = (function(){
	return {
		Read : function(){

			var 		
			authShare =$('#projProfileinputval').val() != ''? parseInt($('#projProfileinputval').val()):0,
			issueShare = $('#projProfileinputval0').val() != '' ? parseInt($('#projProfileinputval0').val()):0,
			founderShare = $('#projProfileinputval3').val() != '' ? parseInt($('#projProfileinputval3').val()):0,
			investorShare = $('#projProfileinputval2').val() != '' ? parseInt($('#projProfileinputval2').val()):0,
			offerShare = $('#projProfileinputval1').val() != '' ? parseInt($('#projProfileinputval1').val()):0,
			unAuthShare = $('#projProfileinputval4').val() != '' ? parseInt($('#projProfileinputval4').val()):0;
			outstandingOpt = $('#projProfileinputval5').val() != '' ? parseInt($('#projProfileinputval5').val()):0;

			var fixer = function(val){
				return ( val * 100 ) .toFixed(1);
			}

			return {
			    founderShare : founderShare,
				previousInvestorShare : investorShare,
				thisOfferingShare : offerShare,
				authorizeShare : authShare,
				outstandingOpt : outstandingOpt,
				issuedOutShare : (founderShare + investorShare + offerShare),
				unissuedShare : authShare - (founderShare + investorShare + offerShare),
				previousIssue : (founderShare + investorShare),
				outColor : fixer(outstandingOpt/(authShare - (founderShare + investorShare + offerShare))),
				issuedNoutPer: (founderShare + investorShare + offerShare) > authShare ? fixer((founderShare + investorShare + offerShare) / authShare) :100
			};
		},
		Update : function(){
			var data = this.Read();

			if(data.issuedNoutPer > 100)
				$('.projProfileErrorMessage').show();
			else
				$('.projProfileErrorMessage').hide();
			
			$('#projProfileinputval0').val(data.issuedOutShare);
			$('#projProfileinputval4').val(data.unissuedShare);

			if(data.outstandingOpt > data.unissuedShare)
				$('#proj-outstanding_options').val("0");
		},

		Percentage : function(){
			var data = this.Read();
			
			var fixer = function(val){
				return ( val * 100 ) .toFixed(1);
			}
			return {
				founderSharePer : fixer(data.founderShare / data.issuedOutShare),
				previousInvestorSharePer : fixer(data.previousInvestorShare / data.issuedOutShare),
				thisOfferingSharePer : fixer(data.thisOfferingShare / data.issuedOutShare),
				unissuedSharePer : fixer(data.unissuedShare / data.authorizeShare),
				issueWidthPer : fixer(data.issuedOutShare / data.authorizeShare),
				PrevIssueMarginPer : fixer(data.unissuedSharePer / data.authorizeShare),
				PrevIssueWidthPer : fixer(data.previousIssue / data.authorizeShare),
			}
		}
	}
})();

var chartprojProf = null;
$(function () {

	Highcharts.setOptions({
        lang: {
            thousandsSep: ','
        }
    });

    chartprojProf = new Highcharts.Chart({
        chart: {
            type: 'pie',
            renderTo: 'projProfilecontainer',
            plotBackgroundColor: null,
            plotBorderWidth: 0,
            plotShadow: false,
         	margin: [0, 0, 0, 0],
	        spacingTop: 0,
	        spacingBottom: 0,
	        spacingLeft: 0,
	        spacingRight: 0,
	        backgroundColor: "#f0f0f0",
	        // height: 100
	        width:200,
	        height: 200,
        },/*
        title: {
            text: null
        },*/

        title: {
            text: 'Total Issued and<br/>Outstanding Shares/Units<br/>with this offering <br/>30,000<br/>100%',
            align: 'center',
            verticalAlign: 'middle',
            fontSize: '9px',
            textAlign: 'center',
            y: -20
        },
        credits: {                                                                                                                                               
	      enabled: false                                                                                                                                         
	    },
	    exporting: {                                                                                                                                             
	      enabled: false                                                                                                                                         
	    },
        legend: {
            enabled: false,
        },
        plotOptions: {

	        pie: {
	        	size:'100%',
                dataLabels: {
                    enabled: true,
                    distance: -5,
                    shadow: false,
                    style: {
                        fontWeight: 'bold',
                        color: '#5c666b',
                        // textShadow: '0px 1px 2px black'

                    }
                },
                startAngle: 0,
                endAngle: 360,
                // center: ['50%']
            }
        },
        colors: [ 
			    {
			        linearGradient: { x1: 1, x2: 0, y1: 1, y2: 0 },
			        stops: [
			            [0, '#f4cc08'],
			            [1, '#f4cc08']
			        ]
			    },
			    {
			        linearGradient: { x1: 1, x2: 0, y1: 1, y2: 0 },
			        stops: [
			            [0, '#2dca33'],
			            [1, '#2dca33']
			        ]
			    },
			    {
			    	// radialGradient: { cx: 0.5, cy: 0.3, r: 0.7 },
			        linearGradient: { x1: 1, y1: 1, x2: 0, y2: 1 },
			        stops: [
			            [0.3, '#d58181'],
			            [0.5, '#cecece']
			        ]
			    },
			    {
			        linearGradient: { x1: 1, x2: 0, y1: 1, y2: 0 },
			        stops: [
			            [1, '#5591c7'],
			            [0, '#5591c7']
			        ]
			    },
			    ],
		tooltip: {
            valueSuffix: ' %',
            enabled: false
        },
        series: [{
        	type: 'pie',
            name: 'Total Issued Share',
            innerSize: '80%',
            data: [
            	{
		            name: 'Previous Investor(s) Share/Unit',
            		y:10000,
            		dataLabels: {
	                    shadow: false,
	                    useHTML: true,
	                    formatter: function() {
	                    	var percentage = ShareGraphprojProf.Percentage().previousInvestorSharePer;
	                        return this.percentage > 4 ? "<p style='text-align:center;'>"+percentage+"%</p>" : "";
	                    },
	                    style: {
	                        fontWeight: 'bold'
	                    }
	                },
            	},
            	{
		            name: 'This Offering',
            		y:10000,
            		dataLabels: {
	                    shadow: false,
	                    useHTML: true,
	                    formatter: function() {
	                    	var percentage = ShareGraphprojProf.Percentage().thisOfferingSharePer;
	                        return this.percentage > 4 ? "<p style='text-align:center;'>"+percentage+"%</p>" : "";
	                    },
	                    style: {
	                        fontWeight: 'bold'
	                    }
	                },
            	},
            	{
		            name: 'Authorized Unissued Share',
            		y:10000,
            		dataLabels: {
            			enabled: false,
	                    shadow: false,
	                    useHTML: true,
	                    formatter: function() {
	                    	var percentage = ShareGraphprojProf.Percentage().founderSharePer;
	                        return this.percentage > 4 ? "<p style='text-align:center;'>Authorized Unissued<br/>Share/Units<br/>"+Highcharts.numberFormat(this.y,0)+"</p>" : "";
	                    },
	                    style: {
	                        fontWeight: 'bold',
	                    }
	                },
            	},
            	{
		            name: 'Founder(s) Share/Unit',
            		y:10000,
            		dataLabels: {
	                    shadow: false,
	                    useHTML: true,
	                    formatter: function() {
	                    	var percentage = ShareGraphprojProf.Percentage().founderSharePer;
	                        return this.percentage > 4 ? "<p style='text-align:center;'>"+percentage+"%</p>" : "";
	                    },
	                    style: {
	                        fontWeight: 'bold'
	                    }
	                },
            	}],
            	states: {
	                hover: {
	                    enabled: false
	                }
	            },
	            //showInLegend:false
        }]
    });
});

var ChartUpdateprojProf = function(){
	var data = ShareGraphprojProf.Read(),
		percentage = ShareGraphprojProf.Percentage();

	if(data.issuedNoutPer > 100)
		chartprojProf.series[0].setData([0,0,0,0]);
	else
		chartprojProf.series[0].setData([parseInt(data.previousInvestorShare), parseInt(data.thisOfferingShare), parseInt(data.unissuedShare), parseInt(data.founderShare)]);
			
	var cstop0 = 0.3, cstop1 = 0.5 ;
	//console.log(data.outColor);
	cstop1 = (parseInt(data.outColor)/100) + 0.1;
	cstop0 = cstop1 - 0.3;

	 chartprojProf.series[0].update({colors: [ 
			    {
			        linearGradient: { x1: 1, x2: 0, y1: 1, y2: 0 },
			        stops: [
			            [0, '#f4cc08'],
			            [1, '#f4cc08']
			        ]
			    },
			    {
			        linearGradient: { x1: 1, x2: 0, y1: 1, y2: 0 },
			        stops: [
			            [0, '#2dca33'],
			            [1, '#2dca33']
			        ]
			    },
			    {
			    	// radialGradient: { cx: 0.5, cy: 0.3, r: 0.7 },
			        linearGradient: { x1: 1, y1: 1, x2: 0, y2: 1 },
			        stops: [
			            [cstop0, '#d58181'],
			            [cstop1, '#cecece']
			        ]
			    },
			    {
			        linearGradient: { x1: 1, x2: 0, y1: 1, y2: 0 },
			        stops: [
			            [1, '#5591c7'],
			            [0, '#5591c7']
			        ]
			    },
			    ],});
	//console.log(chart.series[0].data[2].color.stops[0][0]);
	//console.log(chart.series[0].data[2].color.stops[1][0]);
	
	chartprojProf.setTitle({text: 'Total Issued and<br/>Outstanding Shares/Units<br/>with this offering <br/>'+numberWithCommas(data.issuedOutShare)+'<br/>'+data.issuedNoutPer+'%'});

	$(".projProfilefshareLegend").html(numberWithCommas(data.founderShare));
	$(".projProfileishareLegend").html(numberWithCommas(data.previousInvestorShare));
	$(".projProfiletoshareLegend").html(numberWithCommas(data.thisOfferingShare));
	$(".projProfileaishareLegend").html(numberWithCommas(data.unissuedShare));
	$(".projProfileooshareLegend").html(numberWithCommas(data.outstandingOpt));

}

$(function () {
	ShareGraphprojProf.Update();
	ChartUpdateprojProf();
});