<template>
    <div class="col-md-11 date-picker-container">
        <input type="text" class="form-control has-feedback-left date-picker" placeholder="First Name" aria-describedby="inputSuccess2Status3">
        <span class="fa fa-calendar-o form-control-feedback left" aria-hidden="true"></span>
        <span id="inputSuccess2Status3" class="sr-only">(success)</span>
    </div>
</template>

<style src="gentelella/vendors/bootstrap-daterangepicker/daterangepicker.css"></style>
<script lang="babel!coffee" type="text/coffeescript">
require 'gentelella/vendors/moment/moment.js'
require 'gentelella/vendors/bootstrap-daterangepicker/daterangepicker.js'

module.exports =
    props: ['column', 'position']
    mounted: () ->
        @setDate()
    watch:
        date: () ->
            @setDate()
    methods:
        # 設定該元件的時間
        setDate: () ->
            me = @

            $(@$el).find('.date-picker').daterangepicker
                singleDatePicker: true
                startDate: @column
                locale:
                    format: 'YYYY/MM/DD'

            # change event
                (start, end, label) ->
                    date = end.toISOString().split('T')[0]

                    me.$store.dispatch 'list.syncData',
                        row: me.position.row
                        key: me.position.key
                        value: date
</script>

<style lang="sass?indentedSyntax" type="text/sass" scoped>
    .date-picker-container
        input
            width: 150px
</style>