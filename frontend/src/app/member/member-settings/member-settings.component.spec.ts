import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { MemberSettingsComponent } from './member-settings.component';

describe('MemberSettingsComponent', () => {
  let component: MemberSettingsComponent;
  let fixture: ComponentFixture<MemberSettingsComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ MemberSettingsComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(MemberSettingsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
