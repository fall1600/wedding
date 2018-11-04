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
    attendence: ['', Validators.required],
    frends: ['', Validators.required],
    numPeople: ['1', Validators.required],
    numVegetarian: ['0', Validators.required],
    numBabyseat: ['0', Validators.required],
    phone: '',
    address: '',
    email: '',
    note: ''
  });

  _invitation:string = 'email' ;
  

  submitForm(): void {

  }

  ngOnInit() {
    console.log();
  }

  setInvitation(invitation) {
    this._invitation = invitation;
    if( invitation === 'address'){
      this.weddingForm.get('phone').reset();
    } else if ( invitation === 'phone'){
      this.weddingForm.get('address').reset();
    }
  }

  isShow(invitation) {
    if( this._invitation === 'sendBoth' || this._invitation === invitation) {
      return true
    } else {
      return false
    }
  }


}
