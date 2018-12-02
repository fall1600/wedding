<?php
/**
 * @api {POST} /invitations  邀請函-建立
 * @apiName CreateAInvitation
 * @apiGroup Invitation
 * @apiParam {string} name 姓名
 * @apiParam {string} [nickname] 暱稱
 * @apiParam {string} phone 聯絡電話
 * @apiParam {integer} [number_of_people=1] 出席人數
 * @apiParam {integer} [number_of_vegetarian=0] 素食人數 (必須小於等於出席人數)
 * @apiParam {integer} [number_of_baby_seat=0] 兒童座椅數量
 * @apiParam {string} [address] 喜帖地址 (實體喜帖, 有填就寄)
 * @apiParam {string} [email] 喜帖email (電子喜帖, 有填就寄)
 * @apiParam {string} attend 出席意願
 * <ul>
 * <li>no (不出席)</li>
 * <li>taipei (出席台北場)</li>
 * <li>chiayi (出席嘉義場)</li>
 * <li>both (出席兩場)</li>
 * <li>blessing (禮到人未到)</li>
 * <li>notsure (尚未確定)</li>
 * </ul>
 * @apiParam {string} known_from 男方或女方的親友
 * <ul>
 * <li>male (男方)</li>
 * <li>female (女方)</li>
 * </ul>
 * @apiParam {string} [note] 祝福的話
 * @apiParam {string} recaptcha recaptcha token
 * @apiParamExample 參數範例
 * {
 *    "name": "fall1600",
 *    "nickname": "小費勿",
 *    "phone": "0988555666",
 *    "number_of_people": 2,
 *    "number_of_vegetarian": 0,
 *    "number_of_baby_seat": 0,
 *    "address": "民族西路296號",
 *    "email": "fall1600@gmail.com",
 *    "attend": "taipei",
 *    "known_from": "male",
 *    "note": "新婚快樂 <3",
 *    "recaptcha": "asdfsafdsfffdsfadsfdsafdfjskfjsalfsa"
 * }
 * @apiSuccessExample {json} 成功回傳範例
 * HTTP/1.1 200 OK
 * Invitation
 * @apiUse InvitationResponse
 * @apiUse HttpBadRequestError
 */

/**
 * @apiDefine InvitationResponse
 * @apiSuccessExample Invitation
 * {
 *    "name": "fall1600",
 *    "nickname": "小費勿",
 *    "phone": "0988555666",
 *    "number_of_people": 2,
 *    "number_of_vegetarian": 0,
 *    "number_of_baby_seat": 0,
 *    "address": "民族西路296號",
 *    "email": "fall1600@gmail.com",
 *    "attend": "taipei",
 *    "known_from": "male",
 *    "note": "新婚快樂 <3"
 */
