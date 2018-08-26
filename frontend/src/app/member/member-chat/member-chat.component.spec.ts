import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { MemberChatComponent } from './member-chat.component';

describe('MemberChatComponent', () => {
  let component: MemberChatComponent;
  let fixture: ComponentFixture<MemberChatComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ MemberChatComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(MemberChatComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
