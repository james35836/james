import { Component, OnInit, AfterViewInit } from '@angular/core';
import * as $ from 'jquery';

@Component({
  selector: 'app-member-genealogy',
  templateUrl: './member-genealogy.component.html',
  styleUrls: ['./member-genealogy.component.scss']
})
export class MemberGenealogyComponent implements OnInit {

  constructor() { }

  ngOnInit() 
  {
  	
  }

  ngAfterViewInit()
  {
  	this.scrollCenter();
  }

  scrollCenter()
  {
  	var outerContent = $('.drag-scroll-content');
    var innerContent = $('.drag-scroll-content .tree');

    outerContent.scrollLeft((innerContent.width() - outerContent.width()) / 2);
  }

}
