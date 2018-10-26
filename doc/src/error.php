<?php
/**
 * @apiDefine HttpBadRequestError
 * @apiError  HttpBadRequestError 400 請求參數錯誤
 * @apiErrorExample {json} HttpBadRequestError
 *     HTTP/1.1 400 Bad Request
 *     {
 *       "error":  "error ..."
 *     }
 */
/**
 * @apiDefine UnauthorizedError
 * @apiError  UnauthorizedError   401 授權失敗錯誤
 * @apiErrorExample {json} UnauthorizedError
 *     HTTP/1.1 401 Unauthorized
 *     {
 *       "error":  "Unauthorized Token"
 *     }
 */
/**
 * @apiDefine HttpForbiddenError
 * @apiError  HttpForbiddenError  403 拒絕請求(Token 錯誤)
 * @apiErrorExample {json} HttpForbiddenError
 *     HTTP/1.1 403 Forbidden
 *     {
 *       "error":  "Access deny"
 *     }
 */
/**
 * @apiDefine HttpNotFoundError
 * @apiError  HttpNotFoundError   404 資源無法存取錯誤
 * @apiErrorExample {json} HttpNotFoundError
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error":  "not found"
 *     }
 */
/**
 * @apiDefine HttpConflictError
 * @apiError  HttpConflictError   409 其他錯誤
 * @apiErrorExample {json} HttpConflictError
 *     HTTP/1.1 409 Conflict
 *     {
 *       "error":  "conflict error"
 *     }
 */
