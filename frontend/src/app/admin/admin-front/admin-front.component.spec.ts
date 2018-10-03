import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { AdminFrontComponent } from './admin-front.component';

describe('AdminFrontComponent', () => {
  let component: AdminFrontComponent;
  let fixture: ComponentFixture<AdminFrontComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ AdminFrontComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AdminFrontComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
