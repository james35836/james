import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-dashboard',
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.scss'],

})

export class DashboardComponent implements OnInit 
{
  	constructor() 
  	{ 

  	}

 	 ngOnInit() 
  	{

  		
  	}
  	
	visitChartData =  {
	  chartType: 'ColumnChart',
	  dataTable: [
	    ['Visitors', 'Visitors per Month'],
	    ['January',  0],
	    ['February', 250],
	    ['March',  	 300],
	    ['April',	 0],
	    ['May',    	 350],
	    ['June',     325],
	    ['July',     0],
	  ],
	  options: {'title': 'Visitors','height': 500},
	};

	 memberChartData =  {
	  chartType: 'ColumnChart',
	  dataTable: [
	    ['New Members', 'New members per Month'],
	    ['January',  200],
	    ['February', 350],
	    ['March',  	 100],
	    ['April',	 500],
	    ['May',    	 100],
	    ['June',     500],
	    ['July',     50],
	  ],
	  options: {'title': 'New Members', 'height': 500},
	};

	salesDataChart =  {
	  chartType: 'ColumnChart',
	  dataTable: [
	    ['Sales', 'Sales per Month'],
	    ['January',  10],
	    ['February', 10],
	    ['March',  	 10],
	    ['April',	 10],
	    ['May',    	 10],
	    ['June',     10],
	    ['July',     10],
	  ],
	  options: {'title': 'Sales','height': 500},
	};
}
