import { environment } from './../environments/environment';
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';


@Injectable()
export class WeddingService {

  private _baseUrl: string = null;

  constructor(private httpClient: HttpClient) {
    this._baseUrl = environment.apiurl;
  }

  nameBunus = [
    // {"name":"","message":""},
    {"name":"Chia","message":"Hi Chia Hi ChiaHi ChiaHi ChiaHi ChiaHi ChiaHi ChiaHi ChiaHi ChiaHi ChiaHi ChiaHi ChiaHi Chia"},
    {"name":"鄭而璞","message":"阿璞 感謝一直以來有你相伴 真心希望你愛情與事業都能找到自己喜歡的那條路"},
    {"name":"許庭蓉","message":"許庭蓉 感謝一直以來有你相伴 希望你能想出來對你而言最棒的平衡點"},
    {"name":"鍾任鴻","message":"老人 真的很感謝有你這個好朋友！ 衷心希望你新的環境是更好 且好很多！"},
    {"name":"聶簡建安","message":"聶簡 很開心有話就說的好朋友 真心祝福你能越來越順心！"},
    {"name":"陳柏翰","message":"姐姐 有去花蓮時, 會去找你玩唷ＸＤ"},
    {"name":"蕭雅文","message":"雅文, 很可惜時間對不上沒辦法請你當伴娘 但還是很開心你的祝福唷！"},
    {"name":"何佳穎","message":"爆點！ 感謝你情義相挺的願意當我的伴娘！"},
    {"name":"王怡人","message":"怡人, 很喜歡聽你講你旅遊的所見所聞, 總是讓我同感喜悅和興奮！"},
    {"name":"曾璞","message":"曾璞, 一直很想感謝你點出了我的站姿, 震撼的意識到30年來站姿不對, 真的很感謝你 ！"},
    {"name":"謝瑜珊","message":"緣份的起點: 娘娘與謝太太, 歡迎來玩唷！"},
    {"name":"王珣","message":"緣份的起點: 娘娘與謝太太, 歡迎來玩唷！"},
    {"name":"賈雯婷","message":"嗲嗲 很開心能與你一起出去玩 就算結婚我們還是可以常常約出去玩唷！！"},
    {"name":"賈秀華","message":"親愛的姑姑, 你的"},
    {"name":"賈寶海","message":"寶海叔叔, 感謝你在我的成長過程中給我很多中肯的人生建議, 期待你們一家一起來玩"},
    {"name":"賈寶山","message":"寶山叔叔, 感謝博涵來當招待喲！"},
    {"name":"賈博涵","message":"感謝你情義相挺來當我的招待喲！"},
    {"name":"賈寶龍","message":"寶龍叔叔, 期待你們一家來參加唷！"},
    {"name":"凃財勝","message":"財勝舅舅, 你跟小珠阿姨和兩個小帥哥一定要來參加唷 !"},
    {"name":"鄭益勝","message":"Ｏpen 很感謝有你這個有情有義的好朋友的一路相伴 很希望你能來參加"},
    {"name":"蔡元勛","message":"元元, 有空來台北一定要找我玩唷! "},
    {"name":"楊茜予","message":"Black, 知道你一直都很忙碌, 真心期待有空時能約出來一起玩"},
    {"name":"李思緯","message":"小五, 下次辦畫畫的活動時希望還能找我 我很期待下次能參加到！"},
    {"name":"盧延毫","message":"延毫, 我一直很崇拜你散發的優雅氣質 "},
    {"name":"盧盈君","message":"158的盈君, 你知道我典禮當天一定會185的 希望你能來見證一下 哈哈哈~"},
    {"name":"王書函","message":"書函, 感謝你老是傳很無腦的梗圖, 跟散播歡樂唷XD"},
    {"name":"蔡易達","message":"達叔 真的很開心你來玩！謝謝你的祝福！"},
    {"name":"余澤生","message":"生生 希望你學業與事業都越來越順利"},
    {"name":"余慈詠","message":"詠詠, 聽說你養了一隻哈利~ 希望有機會可以帶出來一起玩唷！"},
    {"name":"蔡欣蓓","message":"心貝, 在台中時的那段時光承蒙照顧了!衷心感謝! 祝你日子越過越順心!"},
    {"name":"方傑","message":"番茄外星人ＸＤ, 真心感謝你有點時間總是為我解惑, 說不定之後我也能幫你解惑唷"},
    {"name":"李俊佑","message":"柚子安安 祝福你遇到興趣相投的良緣!"},
    {"name":"","message":""},
    {"name":"","message":""},
    {"name":"","message":""},
    {"name":"魏秉輝","message":"秉冰安安, 感謝在基隆的日子有你照顧<3, 真懷念以前玩社團的日子, 祝你在潛水行業闖出一番事業"},
    {"name":"江政勳","message":"學長阿玫, 真巧你今年也結婚, 也祝你們生... 生活中充滿神的恩典 :p"},
    {"name":"馬楊陞","message":"嗨馬克思馬, 記得到時候穿帥帥來吃喜酒嘿, 祝你在事業跟感情都能開花結果"},
    {"name":"馬尚彬","message":"老師好, 謝謝在校期間老師的指導. 因為工作忙我比較少找老師聚餐, 多多包含啦 :p"},
    {"name":"楊安","message":"安安好久不見, 感謝你以前的照顧, 回想起來還是覺得在系辦的日子挺不錯的呢"},
    {"name":"陳芝珊","message":"芝珊姐好久不見, 感謝你以前的照顧, 回想起來還是覺得在系辦的日子挺不錯的呢"},
    {"name":"張瑾瑜","message":"Sonia姐好久不見, 感謝你以前的照顧, 回想起來還是覺得在系辦的日子挺不錯的呢"},
    {"name":"陳怡穎","message":"YY姐, 祝你在感情跟事業都能開花結果唷. 懷念以前JNTY 一起午餐的時光"},
    {"name":"林育立","message":"Tony, 祝你在感情跟事業都能開花結果唷. 懷念以前JNTY 一起午餐的時光"},
    {"name":"賴昱行","message":"Noah, 祝你在感情跟事業都能開花結果唷. 懷念以前JNTY 一起午餐的時光"},
    {"name":"蘇士哲","message":"Ricky是我哥XD 工作上受你照顧非常多, 非常感謝. 歡迎參加我的喜宴"},
    {"name":"Ricky","message":"Ricky是我哥XD 工作上受你照顧非常多, 非常感謝. 歡迎參加我的喜宴"},
    {"name":"鍾帛諺","message":"嗨板橋, 歡迎參加我的喜宴, 到時別忘了找我去喝你的喜酒"},
    {"name":"Paul","message":"嗨Paul, 歡迎參加我的喜宴, 到時別忘了找我去喝你的喜酒"},
    {"name":"Bubble","message":"嗨Bubble, 歡迎參加我的喜宴, 記得理性飲酒阿 XDD"},
    {"name":"葉品妤","message":"品妤好久不見了, 歡迎參加我的喜宴"},
    {"name":"簡煒航","message":"大蟒蛇兜兄安安, 雖然你不愛喜宴式的相聚, 不過還是誠摯地邀請你參加"},
    {"name":"黃冠傑","message":"Ben!! 好久不見啦, 到時換你別忘了找我去喝你的喜酒哦"},
    {"name":"劉羿辰","message":"臭小雞雞, 感謝以前你的凱瑞, 還有幹話滿天飛, 也謝謝你一口答應協助我的婚宴, 到時再來喝阿"},
    {"name":"王靖淳","message":"小黑安安, 歡迎來玩"},
    {"name":"李昭緯","message":"昭緯, 歡迎來玩, 理性飲酒嘿"},
    {"name":"蘇裕群","message":"林北抽哥, 歡迎來玩, 什麼時候換你阿?"},
    {"name":"陳穎正","message":"小雞雞正, 歡迎來玩, 到時換你別忘了找我去喝你的喜酒哦"},
    {"name":"許子豪","message":"豪哥安安, 好久不見啦, 到時換你別忘了找我去喝你的喜酒哦"},
    {"name":"蔡佳蓉","message":"采姐安安, 歡迎來玩~ 祝你早日娶個好先生"},
    {"name":"吳孟倫","message":"孟倫安安, 歡迎來玩~ 祝你在感情跟事業都能開花結果唷"},
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
    // console.log(url);
    return this.httpClient.post(url, body);
  }

  async getReCaptchaKey() {
    try {
      const ret = await this.httpClient
        .get(this._composeEndpoint('/api/config/recaptcha_site_key'))
        .toPromise();
      return ret as { config: string };
    } catch (error) {
      console.error(error);
      return { config: '' };
    }
  }

  private _composeEndpoint(endpoint: string) {
    return `${this._baseUrl}${endpoint}`;
  }

}
