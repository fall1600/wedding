import { environment } from './../environments/environment';
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';


@Injectable()
export class WeddingService {

  constructor(private httpClient: HttpClient) { }

  nameBunus = [
    // {"name":"","message":""},
    {"name":"Chia","message":"Hi Chia Hi ChiaHi ChiaHi ChiaHi ChiaHi ChiaHi ChiaHi ChiaHi ChiaHi ChiaHi ChiaHi ChiaHi Chia"},
    {"name":"鄭而璞","message":"阿璞 感謝一直以來有你相伴 真心希望你愛情與事業都能找到自己喜歡的那條路"},
    {"name":"許庭蓉","message":"許庭蓉 感謝一直以來有你相伴 希望你能想出來對你而言最棒的平衡點"},
    {"name":"鍾任鴻","message":"老人 真的很感謝有你這個好朋友, "},
    {"name":"聶簡建安","message":""},
    {"name":"陳柏翰","message":""},
    {"name":"蕭雅文","message":"雅文, 很可惜時間對不上沒辦法請你當伴娘 但還是很開心你的祝福唷！"},
    {"name":"何佳穎","message":"爆點！ 感謝你情義相挺的願意當我的伴娘！"},
    {"name":"王怡人","message":"怡人, 很喜歡聽你講你旅遊的所見所聞, 總是讓我同感喜悅和興奮！"},
    {"name":"曾璞","message":"曾璞, 一直很想感謝你點出了我的站姿, 震撼的意識到30年來站姿不對, 真的很感謝你 ！"},
    {"name":"謝瑜珊","message":""},
    {"name":"王珣","message":""},
    {"name":"賈雯婷","message":"嗲嗲 很開心能與你一起出去玩 就算結婚我們還是可以常常約出去玩唷！！"},
    {"name":"賈秀華","message":"親愛的姑姑, 你的"},
    {"name":"賈寶海","message":"寶海叔叔, 感謝你在我的成長過程中給我很多中肯的人生建議, 期待你們一家一起來玩"},
    {"name":"賈寶山","message":"寶山叔叔, 感謝博涵來當招待喲！"},
    {"name":"賈博涵","message":"感謝你情義相挺來當我的招待喲！"},
    {"name":"賈寶龍","message":"寶龍叔叔, 期待你們一家來參加唷！"},
    {"name":"凃財勝","message":"財勝舅舅, 你跟小珠阿姨和兩個小帥哥一定要來參加唷 !"},
    {"name":"鄭益勝","message":"Ｏpen 很感謝有你這個有情有義的好朋友的一路相伴 很希望你能來參加"},
    {"name":"蔡元勛","message":"元元, 很開心你來填, "},
    {"name":"楊茜予","message":"Black, 知道你一直都很忙碌, 真心期待有空時能約出來一起玩"},
    {"name":"李思緯","message":"小五, 下次辦畫畫的活動時希望還能找我 我很期待下次能參加到！"},
    {"name":"盧延毫","message":"延毫, 我一直很崇拜你散發的優雅氣質 "},
    {"name":"盧盈君","message":"158的盈君, 真的很喜歡跟你一起玩 我已經下訂switch了 找機會一起玩"},
    {"name":"王書函","message":"書函, 感謝你老是傳很無腦的梗圖, 吃梗補梗 哈哈哈哈哈"},
    {"name":"蔡易達","message":"達叔 真的可開心你來玩！謝謝你的祝福！"},
    {"name":"余澤生","message":"生生 希望你學業與事業都越來越順利"},
    {"name":"余慈詠","message":"詠詠 真的很少跟你見面, 但真心希望我們有空能一起約易約出去玩"},
    {"name":"蔡欣蓓","message":"心貝, "},
    {"name":"方傑","message":"親愛的番茄外星人ＸＤ, 真心感謝你有點時間總是為我解惑, 說不定之後我也能幫你解惑唷"},
    {"name":"","message":""},
    {"name":"","message":""},
    {"name":"","message":""},
    {"name":"","message":""},
  ];


  getNameBonus(name) {
    const messageData = this.nameBunus.filter( v => {
      return v.name === name;
    });

    return messageData.length === 1 ? messageData[0].message : `很幸運與${name} 你的這段緣份 ,希望你能來一起同歡`;
  }

  postWeddingForm(body) {
    const url = environment.apiurl + '/api/invitations';
    // console.warn(body);
    return this.httpClient.post(url, body);
  }
}
