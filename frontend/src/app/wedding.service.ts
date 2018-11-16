import { environment } from './../environments/environment';
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';


@Injectable()
export class WeddingService {

  constructor(private httpClient: HttpClient) { }

  nameBunus = [
    // {"name":"","message":""},
    {"name":"Chia","message":"Hi Chia Hi ChiaHi ChiaHi ChiaHi ChiaHi ChiaHi ChiaHi ChiaHi ChiaHi ChiaHi ChiaHi ChiaHi Chia"},
    {"name":"鄭而璞","message":""},
    {"name":"許庭蓉","message":""},
    {"name":"鍾任鴻","message":""},
    {"name":"聶簡建安","message":""},
    {"name":"陳柏翰","message":""},
    {"name":"蕭雅文","message":""},
    {"name":"何佳穎","message":""},
    {"name":"王怡人","message":""},
    {"name":"曾璞","message":""},
    {"name":"謝瑜珊","message":""},
    {"name":"王珣","message":""},
    {"name":"賈雯婷","message":""},
    {"name":"賈秀華","message":"親愛的姑姑, 你的"},
    {"name":"賈寶海","message":"寶海叔叔, 感謝你在我的成長過程中給我很多中肯的人生建議, 期待你們一家一起來玩"},
    {"name":"賈寶山","message":"寶山叔叔, 感謝博涵來當招待喲！"},
    {"name":"賈博涵","message":"感謝你情義相挺來當我的招待喲！"},
    {"name":"賈寶龍","message":"寶龍叔叔, 期待你們一家來參加唷！"},
    {"name":"凃財勝","message":"財勝舅舅, 你跟小珠阿姨和兩個小帥哥一定要來參加唷 !"},
    {"name":"鄭益勝","message":""},
    {"name":"蔡元勛","message":""},
    {"name":"楊茜予","message":""},
    {"name":"林羽柔","message":""},
    {"name":"蕭語婕","message":""},
    {"name":"李思緯","message":""},
    {"name":"李思偉","message":""},
    {"name":"盧延毫","message":""},
    {"name":"盧盈君","message":""},
    {"name":"王書函","message":""},
    {"name":"蔡凱婷","message":""},
    {"name":"黃易達","message":""},
    {"name":"賴","message":""},
    {"name":"余澤生","message":""},
    {"name":"余慈詠","message":""},
    {"name":"","message":""},
    {"name":"","message":""},
    {"name":"","message":""},
    {"name":"","message":""},
    {"name":"","message":""},
    {"name":"","message":""},
  ];


  getNameBonus(name) {
    const messageData = this.nameBunus.filter( v => {
      return v.name === name;
    });

    return messageData.length === 1 ? messageData[0].message : '';
  }

  postWeddingForm(body) {
    const url = environment.apiurl + '/api/invitations';
    // console.warn(body);
    return this.httpClient.post(url, body);
  }
}
