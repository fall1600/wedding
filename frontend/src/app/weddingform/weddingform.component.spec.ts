import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { WeddingformComponent } from './weddingform.component';

describe('WeddingformComponent', () => {
  let component: WeddingformComponent;
  let fixture: ComponentFixture<WeddingformComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ WeddingformComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(WeddingformComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
