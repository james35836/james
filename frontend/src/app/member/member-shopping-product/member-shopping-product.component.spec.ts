import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { MemberShoppingProductComponent } from './member-shopping-product.component';

describe('MemberShoppingProductComponent', () => {
  let component: MemberShoppingProductComponent;
  let fixture: ComponentFixture<MemberShoppingProductComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ MemberShoppingProductComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(MemberShoppingProductComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
