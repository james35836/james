import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { MemberTimelineComponent } from './member-timeline.component';

describe('MemberTimelineComponent', () => {
  let component: MemberTimelineComponent;
  let fixture: ComponentFixture<MemberTimelineComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ MemberTimelineComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(MemberTimelineComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
