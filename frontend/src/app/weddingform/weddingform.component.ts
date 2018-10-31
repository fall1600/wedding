import { Component, OnInit } from '@angular/core';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';

@Component({
  selector: 'app-weddingform',
  templateUrl: './weddingform.component.html',
  styleUrls: ['./weddingform.component.scss']
})
export class WeddingformComponent implements OnInit {

  constructor(
    private fb: FormBuilder,
    ) {}

  weddingForm = this.fb.group({
    fullname: ['', Validators.required],
    nickname: ['', Validators.required],
  });

  submitForm(): void {

  }

  ngOnInit() {
  }

}
