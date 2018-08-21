import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { CutoffComponent } from './cutoff.component';

describe('CutoffComponent', () => {
  let component: CutoffComponent;
  let fixture: ComponentFixture<CutoffComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ CutoffComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(CutoffComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
