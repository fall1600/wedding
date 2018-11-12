import { WeddingService } from './../wedding.service';
import { Component, OnInit } from '@angular/core';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';
import swal from 'sweetalert2';

@Component({
  selector: 'app-weddingform',
  templateUrl: './weddingform.component.html',
  styleUrls: ['./weddingform.component.scss']
})
export class WeddingformComponent implements OnInit {

  constructor(
    private fb: FormBuilder,
    private weddingService: WeddingService,
  ) { }


  _hasMessage = false;
  _nameBonusMessage;
  _invitation = 'email';

  weddingForm = this.fb.group({
    name: ['', Validators.required],
    nickname: 0,
    attend: ['taipei', Validators.required],
    known_from: ['male', Validators.required],
    number_of_people: ['1', Validators.required],
    number_of_vegetarian: ['0', Validators.required],
    number_of_baby_seat: ['0', Validators.required],
    phone: ['', Validators.required],
    address: '',
    email: '',
    note: ''
  });


  submitForm(): void {
    Object.keys(this.weddingForm.controls).forEach(key => {
      this.weddingForm.get(key).markAsTouched();
    });

    if (this.weddingForm.valid) {
      this.weddingService.postWeddingForm(this.weddingForm.value).subscribe(res => {
        console.log(res);
        swal({
          title: '送出成功!',
          text: `我們收到${this.weddingForm.get('name').value} 你的祝福囉!謝謝你!`,
          type: 'success',
          confirmButtonText: 'OK'
        });
      });
    } else {
      swal({
        title: '資料未填',
        text: `請填寫必填欄位`,
        type: 'error',
        confirmButtonText: 'OK',
        confirmButtonColor: '#dd3333',
      });
    }
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
