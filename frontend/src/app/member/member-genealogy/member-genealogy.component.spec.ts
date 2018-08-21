import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { MemberGenealogyComponent } from './member-genealogy.component';

describe('MemberGenealogyComponent', () => {
  let component: MemberGenealogyComponent;
  let fixture: ComponentFixture<MemberGenealogyComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ MemberGenealogyComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(MemberGenealogyComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
