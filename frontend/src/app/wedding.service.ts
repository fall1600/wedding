import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';

@Injectable()
export class WeddingService {

  constructor(private httpClient: HttpClient) { }

  nameBunus = [
    // {"name":"","message":""},
    {"name":"Chia","message":"Hi Chia"},
    {"name":"Zoe","message":"HI Zoe"},
  ];


  getNameBonus(name) {
  const messageData = this.nameBunus.filter( v => {
      return v.name === name;
    });
    console.log( messageData);
    return messageData;
  }

  postWeddingForm(body) {
    const url = 'http://localhost:8000/api/invitations';
    console.warn(body);
    return this.httpClient.post(url, body);
  }
}
