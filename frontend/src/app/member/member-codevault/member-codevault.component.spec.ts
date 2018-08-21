import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { MemberCodevaultComponent } from './member-codevault.component';

describe('MemberCodevaultComponent', () => {
  let component: MemberCodevaultComponent;
  let fixture: ComponentFixture<MemberCodevaultComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ MemberCodevaultComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(MemberCodevaultComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
