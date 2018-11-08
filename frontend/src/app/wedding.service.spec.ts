import { TestBed, inject } from '@angular/core/testing';

import { WeddingService } from './wedding.service';

describe('WeddingService', () => {
  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [WeddingService]
    });
  });

  it('should be created', inject([WeddingService], (service: WeddingService) => {
    expect(service).toBeTruthy();
  }));
});
