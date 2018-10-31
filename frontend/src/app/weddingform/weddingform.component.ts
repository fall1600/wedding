import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-weddingform',
  templateUrl: './weddingform.component.html',
  styleUrls: ['./weddingform.component.scss']
})
export class WeddingformComponent implements OnInit {

  constructor(
    // private fb: FormBuilder,
    ) {}

  // weddingForm = this.fb.group({
  //   vendorNo: '',
  //   vendorType: ['D', Validators.required],
  // });

  submitForm(): void {

  }

  ngOnInit() {
  }

}
