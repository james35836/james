import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { MemberEarningComponent } from './member-earning.component';

describe('MemberEarningComponent', () => {
  let component: MemberEarningComponent;
  let fixture: ComponentFixture<MemberEarningComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ MemberEarningComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(MemberEarningComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
