import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { MemberSlotComponent } from './member-slot.component';

describe('MemberSlotComponent', () => {
  let component: MemberSlotComponent;
  let fixture: ComponentFixture<MemberSlotComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ MemberSlotComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(MemberSlotComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
