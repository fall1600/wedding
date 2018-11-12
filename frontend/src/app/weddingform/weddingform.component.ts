import { WeddingService } from './../wedding.service';
import { Component, OnInit } from '@angular/core';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';
import { SweetalertService } from '../sweetalert.service';

@Component({
  selector: 'app-weddingform',
  templateUrl: './weddingform.component.html',
  styleUrls: ['./weddingform.component.scss']
})
export class WeddingformComponent implements OnInit {

  constructor(
    private fb: FormBuilder,
    private weddingService: WeddingService,
    private sweetalertService: SweetalertService
  ) { }


  _hasMessage = true;
  _nameBonusMessage;
  _invitation = 'email';

  weddingForm = this.fb.group({
    fullname: ['Zoe', Validators.required],
    nickname: 0,
    attendence: ['taipei', Validators.required],
    frends: ['male', Validators.required],
    numPeople: ['1', Validators.required],
    numVegetarian: ['0', Validators.required],
    numBabyseat: ['0', Validators.required],
    phone: ['', Validators.required],
    address: '',
    email: '',
    note: ''
  });




  submitForm(): void {
    this.sweetalertService.confirm('Are you sure to insert this record?').subscribe(result => {
      if (result) {
        // codes..
      } else {
        // codes..
      }
    });
  }

  ngOnInit() {
    console.log();
  }

  setInvitation(invitation) {
    this._invitation = invitation;
    if (invitation === 'address') {
      this.weddingForm.get('phone').reset();
    } else if (invitation === 'phone') {
      this.weddingForm.get('address').reset();
    }
  }

  isShow(invitation) {
    if (this._invitation === 'sendBoth' || this._invitation === invitation) {
      return true;
    } else {
      return false;
    }
  }

  showNameBonus() {
    const msg = this.weddingService.getNameBonus(this.weddingForm.get('fullname').value);
    console.log(msg);
    if (msg.length !== 0) {
      this._hasMessage = true;
      const playtimes = this.weddingForm.get('nickname').value;
      this.weddingForm.get('nickname').setValue(playtimes + 1);
      this._nameBonusMessage = msg;
    } else {
      this._hasMessage = false;
      this._nameBonusMessage = '';
    }
  }

  checkValid(v) {
    const fc = this.weddingForm.get(v);
    return (fc.invalid) && (fc.dirty || fc.touched);
  }


}
