import { Injectable } from '@angular/core';
import { Observable } from 'rxjs/internal/Observable';
import swal from 'sweetalert';
// declare var swal: any;
import { from } from 'rxjs';


@Injectable({
  providedIn: 'root'
})
export class SweetalertService {

  constructor() { }

  // Example
  // private sweetalertService: SweetalertService;
  // this.sweetalertService.confirm('Are you sure to insert this record?').subscribe(result => {
  //   if (result) {
  //     // codes..
  //   } else {
  //     // codes..
  //   }
  // });

  // Alert after the action succeed
  succeed(text?: string): Observable<any> {
    const successTitle = 'data.successTitle';
    const successComplete = 'data.successComplete';
    const swalOption = {
      title: successTitle,
      text: text || successComplete,
      icon: 'success',
      buttons: {
        ok: {
          text: 'data.ok',
          value: true
        }
      }
    } as Partial<any>;
    return  from(swal(swalOption));
  }

  // Alert after an error occurred
  error(titile?: string, text?: string): Observable<any> {
    const errorTitle = 'data.errorTitle';
    const errorDefault = 'data.errorUnknown';
    const swalOption = {
      title: titile || errorTitle,
      text: text || errorDefault,
      icon: 'error',
      buttons: {
        ok: {
          text: 'data.ok',
          value: true
        }
      }
    } as Partial<any>;
    return  from(swal(swalOption));
  }

  // Alert specific message to user. parameter 'text' is required
  inform(text?: string): Observable<any> {
    const infoTitle = 'data.infoTitle';
    const swalOption = {
      title: infoTitle,
      text: text,
      icon: 'info',
      buttons: {
        ok: {
          text: 'data.ok',
          value: true
        }
      }
    } as Partial<any>;
    return  from(swal(swalOption));
  }

  // Alert warning message to user. parameter 'text' is required
  warning(text: string): Observable<any> {
    const warningTitle = 'data.warningTitle';
    const swalOption = {
      title: warningTitle,
      text: text,
      icon: 'warning',
      buttons: {
        confirm: 'data.ok',
        cancel : 'data.cancel',
      }
    } as Partial<any>;
    return  from(swal(swalOption));
  }

  // Confirm the previous action. Return true if confirmed, otherwise null.
  confirm(title?: string, text?: string): Observable<any> {
    const confirmTitle = 'data.confirmTitle';
    const confirmRecover = 'data.confirmRecover';
    const swalOption = {
      title: title || confirmTitle,
      text: text || confirmRecover,
      icon: 'warning',
      buttons: {
        confirm: 'data.ok',
        cancel : 'data.cancel'
      }
    } as Partial<any>;
    return  from(swal(swalOption));
  }
}
