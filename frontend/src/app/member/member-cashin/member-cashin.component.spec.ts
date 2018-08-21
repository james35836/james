import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { MemberCashinComponent } from './member-cashin.component';

describe('MemberCashinComponent', () => {
  let component: MemberCashinComponent;
  let fixture: ComponentFixture<MemberCashinComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ MemberCashinComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(MemberCashinComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
