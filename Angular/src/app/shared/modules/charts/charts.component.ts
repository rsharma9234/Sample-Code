import { Component, OnInit, Input } from '@angular/core';

@Component({
    selector: 'app-charts',
    templateUrl: './charts.component.html',
    styleUrls: ['./charts.component.scss']
})
export class ChartsComponent implements OnInit {
    @Input() title: string;
    @Input() lineChartData: Array<any>;
    @Input() lineChartLabels: Array<any>;
    @Input() lineChartColors: Array<any>;

    // lineChart
    public lineChartOptions: any = {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            yAxes: [{
                ticks: {
                    suggestedMin: 0,
                    suggestedMax: 50
                }
            }]
        },
        tooltips: {
            enabled: true,
            mode: 'single',
            callbacks: {
                label: function(tooltipItem, data) {
                    var label = data.datasets[tooltipItem.datasetIndex].label
                    var datasetLabel = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                    
                    if(label == "Checkouts (%)"){
                        return 'Checkouts: ' + datasetLabel.toFixed(2) + '%';
                    }else{
                        return 'Checkouts: ' + datasetLabel;
                    }                    
                }
            }
          },
    };
    
    public lineChartLegend: boolean = false;
    public lineChartType: string = 'line';

    // events
    public chartClicked(e: any): void {
        // console.log(e);
    }

    public chartHovered(e: any): void {
        // console.log(e);
    }

    constructor() {}

    ngOnInit() {}
}
