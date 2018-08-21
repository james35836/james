import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-test',
  templateUrl: './test.component.html',
  styleUrls: ['./test.component.scss']
})
export class TestComponent implements OnInit 
{
	model     = new Test('Testing');
	submitted = false;

	constructor() { }

	ngOnInit() 
	{

	}

	onSubmit()
	{
		this.submitted = true;
	}

	get diagnostic() { return JSON.stringify(this.model); }
}


export class Test 
{

  constructor(
    public param_1: string
  ) {  }

}