import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';

@Injectable()
export class WeddingService {

  constructor() { }

  nameBunus = [
    // {"name":"","message":""},
    {"name":"Chia","message":"Hi Chia Hi ChiaHi ChiaHi ChiaHi ChiaHi ChiaHi ChiaHi ChiaHi ChiaHi ChiaHi ChiaHi ChiaHi Chia"},
    {"name":"Zoe","message":"HI Zoe"},
  ];


  getNameBonus(name) {
    const messageData = this.nameBunus.filter( v => {
      return v.name === name;
    });

    return messageData.length === 1 ? messageData[0].message : '';
  }
}
