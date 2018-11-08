import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { WeddingphotoComponent } from './weddingphoto.component';

describe('WeddingphotoComponent', () => {
  let component: WeddingphotoComponent;
  let fixture: ComponentFixture<WeddingphotoComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ WeddingphotoComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(WeddingphotoComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
