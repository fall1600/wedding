<?php
/**
 * @apiDefine PhotoObjectResponse
 * @apiSuccessExample Photo
 *     {
 *       "origin":  "xxx.jpg",
 *       "large":  "xxx.jpg",
 *       "middle":  "xxx.jpg",
 *       "small":  "xxx.jpg",
 *       "tiny":  "xxx.jpg",
 *     }
 */

/**
 * @apiDefine PhotoListResponse
 * @apiSuccess {Object} PhotoList 圖片路徑物件
 * @apiSuccess {Photo} PhotoList.master 主要顯示照片
 * @apiSuccess {Photo[]} PhotoList.photos 照片列表
 * @apiSuccess {Object} Photo 照片物件
 * @apiSuccess {string} Photo.origin 原始尺寸照片路徑
 * @apiSuccess {string} Photo.large  Large 尺寸照片路徑
 * @apiSuccess {string} Photo.middle  middle 尺寸照片路徑
 * @apiSuccess {string} Photo.small   small 尺寸照片路徑
 * @apiSuccess {string} Photo.tiny   tiny 尺寸照片路徑
 * @apiSuccessExample PhotoList
 *     {
 *       "master":  Photo,
 *       "photos": Photo[]
 *     }
 * @apiSuccessExample Photo
 *     {
 *       "origin":  "xxx.jpg",
 *       "large":  "xxx.jpg",
 *       "middle":  "xxx.jpg",
 *       "small":  "xxx.jpg",
 *       "tiny":  "xxx.jpg",
 *     }
 */

/**
 * @apiDescription
 * 1. 照片上傳需使用 POST Multipart 編碼
 * @api {POST} /photo/{name}  上傳照片
 * @apiUse loginRole
 * @apiName PhotoUpload
 * @apiGroup Photo
 * @apiParam {string} name 縮圖設定
 *                           <table>
 *                             <tr>
 *                                <th>類型</th>
 *                                <th>name</th>
 *                             </tr>
 *                             <tr>
 *                                <td>預設縮圖</td>
 *                                <td>default</td>
 *                             </tr>
 *                             <tr>
 *                                <td>大頭貼</td>
 *                                <td>avatar</td>
 *                             </tr>
 *                          </table>
 * @apiUse Authorization
 * @apiUse UnauthorizedError
 * @apiUse HttpBadRequestError
 * @apiUse PhotoObjectResponse
 * @apiSuccessExample {json} 成功回傳範例
 *     HTTP/1.1 200 OK
 *     Photo
 */
