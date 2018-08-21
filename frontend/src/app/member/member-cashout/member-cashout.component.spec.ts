import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { MemberCashoutComponent } from './member-cashout.component';

describe('MemberCashoutComponent', () => {
  let component: MemberCashoutComponent;
  let fixture: ComponentFixture<MemberCashoutComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ MemberCashoutComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(MemberCashoutComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
