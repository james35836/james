import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { MemberCheckoutComponent } from './member-checkout.component';

describe('MemberCheckoutComponent', () => {
  let component: MemberCheckoutComponent;
  let fixture: ComponentFixture<MemberCheckoutComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ MemberCheckoutComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(MemberCheckoutComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
