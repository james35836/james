import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { MemberShoppingComponent } from './member-shopping.component';

describe('MemberShoppingComponent', () => {
  let component: MemberShoppingComponent;
  let fixture: ComponentFixture<MemberShoppingComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ MemberShoppingComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(MemberShoppingComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
