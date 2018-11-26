import { WeddingService } from './../wedding.service';
import { Component, OnInit } from '@angular/core';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';
import swal from 'sweetalert2';
import { Router } from '@angular/router';
import { error } from '@angular/compiler/src/util';

@Component({
  selector: 'app-weddingform',
  templateUrl: './weddingform.component.html',
  styleUrls: ['./weddingform.component.scss']
})
export class WeddingformComponent implements OnInit {

  constructor(
    private fb: FormBuilder,
    private weddingService: WeddingService,
    private _router: Router
  ) { }

  numAttends = Array.from({ length: 10 }, (v, k) => k);
  numVegs = Array.from({ length: 5 }, (v, k) => k);
  numBabys = Array.from({ length: 5 }, (v, k) => k);
  _hasMessage = false;
  _nameBonusMessage;

  weddingForm = this.fb.group({
    name: ['', Validators.required],
    nickname: 0,
    attend: ['', Validators.required],
    known_from: ['', Validators.required],
    number_of_people: [1, Validators.required],
    number_of_vegetarian: [0, Validators.required],
    number_of_baby_seat: [0, Validators.required],
    phone: ['', Validators.required],
    address: '',
    email: '',
    note: '',
    way: ['', Validators.required],
  });


  submitForm(): void {
    Object.keys(this.weddingForm.controls).forEach(key => {
      this.weddingForm.get(key).markAsTouched();
    });
    const body = Object.assign(this.weddingForm.getRawValue());
    if (this.weddingForm.valid) {
      ['way'].forEach(key => {
         delete body[key];
       });

      this.weddingService.postWeddingForm(body).subscribe(res => {
        // console.log(res);
        swal({
          title: '送出成功!',
          text: `我們收到${this.weddingForm.get('name').value} 你的祝福囉!謝謝你!`,
          type: 'success',
          confirmButtonText: 'OK'
        });
        this._router.navigate(['/tks']);
      },
      err => {
        Object.keys(err.error).forEach( (key) => {
          const alertMsg = err.error[key];
          swal({
            title: '格式錯誤',
            text: `${alertMsg}`,
            type: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#dd3333',
          });

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


  attendence() {
    if (this.weddingForm.get('attend').value === 'no' || this.weddingForm.get('attend').value === 'blessing') {
      return false;
    } else {
      return true;
    }
  }

  isShow(invitationWay) {
    const way = this.weddingForm.get('way').value;
    if (way === 'sendBoth' || way === invitationWay) {
      this.weddingForm.get(invitationWay).setValidators(Validators.required);
      return true;
    } else {
      this.weddingForm.get(invitationWay).reset();
      this.weddingForm.get(invitationWay).setValidators(null);
      return false;
    }
  }

  showNameBonus() {
    const name = this.weddingForm.get('name').value;
    if (name) {
      const msg = this.weddingService.getNameBonus(name);

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
  }

  checkValid(v) {
    const fc = this.weddingForm.get(v);
    return (fc.invalid) && (fc.dirty || fc.touched);
  }

}
