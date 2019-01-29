class Time
  constructor: () ->
  toString: (dateString, type) ->
    t = new Date(dateString)
    y = t.getFullYear()
    m = t.getMonth()+1
    d = t.getDate()
    h = t.getHours()
    i = t.getMinutes()
    s = t.getSeconds()
    m = "0#{m}" if m < 10
    d = "0#{d}" if d < 10
    h = "0#{h}" if h < 10
    i = "0#{i}" if i < 10
    s = "0#{s}" if s < 10

    switch type
      when 'yyyy-mm-dd'
        return "#{y}-#{m}-#{d}"
      else
        return "#{y}-#{m}-#{d} #{h}:#{i}:#{s}"
time = new Time()
export default time