import { TestBed, inject } from '@angular/core/testing';

import { WeedingService } from './weeding.service';

describe('WeedingService', () => {
  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [WeedingService]
    });
  });

  it('should be created', inject([WeedingService], (service: WeedingService) => {
    expect(service).toBeTruthy();
  }));
});
