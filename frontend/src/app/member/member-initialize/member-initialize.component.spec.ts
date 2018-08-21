import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { MemberInitializeComponent } from './member-initialize.component';

describe('MemberInitializeComponent', () => {
  let component: MemberInitializeComponent;
  let fixture: ComponentFixture<MemberInitializeComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ MemberInitializeComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(MemberInitializeComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
