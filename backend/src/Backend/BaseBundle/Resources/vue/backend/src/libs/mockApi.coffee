import faker from "faker"
faker.locale = "zh_TW"

export default (api) ->
#  登入
  api.login = (data) ->
    new Promise (resolve, reject) ->
      window.setTimeout () ->
        result = []
        result.push
          token: faker.random.number()
        errors = []
        errors.push
          code: 1
          message: 'nonactivated'
        resolve result
#        reject errors
      , 100

  return api