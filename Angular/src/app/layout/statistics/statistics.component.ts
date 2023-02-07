import { Component, OnInit } from '@angular/core';
import { GuestService } from '../../shared/services/guest.service';
import { FormGroup, Validators, FormBuilder } from '@angular/forms';
import { DatePipe } from '@angular/common'

@Component({
	selector: 'app-statistics',
	templateUrl: './statistics.component.html',
	styleUrls: ['./statistics.component.scss']
})
export class StatisticsComponent implements OnInit {
	filterOptions: Array<any> = [
		{'value' : 'last-7-days', 'label' :'Last 7 days'},
		{'value' : 'last-month', 'label' :'Last month'},
		{'value' : 'last-30-days', 'label' :'Last 30 days'},
		{'value' : 'current-month', 'label' :'Current month'},
		{'value' : 'select-custom-period', 'label' :'Select custom period'}
	];

	customPeriod = false;
	weeklyCheckoutTable = [];

	dateRangeForm: FormGroup;

	startDate = null;
	endDate = null;
	interval;

	public lineChartData: Array<any> = [];
	public lineChartPercentData: Array<any> = [];

	public lineChartLabels: Array<any> = [];

	public filterValue: any;

	minDate = new Date();

	maxDate = new Date();

	public lineChartColors: Array<any> = [
		{
			// Ameniti blue
			backgroundColor: 'rgb(65, 114, 204, 0.2)',
			borderColor: 'rgba(65, 114, 204,1)',
			pointBackgroundColor: 'rgba(65, 114, 204,1)',
			pointBorderColor: '#fff',
			pointHoverBackgroundColor: '#fff',
			pointHoverBorderColor: 'rgba(65, 114, 204,1)'
		},
	];
	validationMessages = {
		'startDate': {
			'required': 'Start date is a Required Field.',
		},
		'endDate': {
			'required': 'End date is a Required Field.',
		}
	}
	loading: boolean;
	error: any = { isError: false, errorMessage: '' };

	constructor(
		private guestService: GuestService,
		private fb: FormBuilder,
		private datePipe: DatePipe
	) {
		this.filterValue = this.filterOptions[0].value;

	}

	ngOnInit() {
		this.createForm();
		this.fetchStatistics();
		this.getStatisticsTable()

		// this.interval = setInterval(() => { 
        //     this.fetchStatistics(); 
        // }, 5000);
	}

	createForm() {
		this.dateRangeForm = this.fb.group({
			startDate: ['', Validators.required],
			endDate: ['', Validators.required],
		});
	}

	fetchStatistics() {
		let currentUser = JSON.parse(localStorage.getItem('currentUser'));

		this.guestService.getStatistics(currentUser.hotel_id, this.filterValue, this.startDate, this.endDate).then((data: any) => {
			this.lineChartLabels = data.chartLabels;
			this.lineChartData = data.chartData;
			this.lineChartPercentData = data.chartPercentData;
			this.loading = false;
		}).catch((error) => {
			console.log(error.error.message)
		})
	}

	getStatisticsTable() {
		let currentUser = JSON.parse(localStorage.getItem('currentUser'));

		this.guestService.getStatisticsTable(currentUser.hotel_id).then((data: any) => {
			this.weeklyCheckoutTable = data.weeklyCheckoutTable;
		}).catch((error) => {
			console.log(error.error.message)
		})
	}

	selectFilterOption(value) {
		this.customPeriod = false;
		this.filterValue = value;
		if (value == "select-custom-period") {
			this.customPeriod = true;
		} else {
			this.lineChartLabels = [];
			this.lineChartData = [];
			this.lineChartPercentData = [];
			this.fetchStatistics();
		}
	}

	filterByCustomDates() {
		if (this.dateRangeForm.invalid) {
			return;
		}
		this.startDate = this.datePipe.transform(this.dateRangeForm.value.startDate, "yyyy-MM-dd");
		this.endDate = this.datePipe.transform(this.dateRangeForm.value.endDate, "yyyy-MM-dd");
		this.loading = true;

		this.lineChartLabels = [];
		this.lineChartData = [];
		this.lineChartPercentData = [];
		this.fetchStatistics();
	}
}
