<template>
    <div>
        <div class="form-group" :class="setGrid(toolConfig.grid)">
            <label class="control-label" :class="mergeClass(useRequired(), labelGrid(toolConfig.labelGrid))" :for="eventName + '_' + toolConfig.name">
                {{ toolConfig.text|trans }}
            </label>
            <div :class="toolGrid(toolConfig.toolGrid)">
                <input :id="eventName + '_' + toolConfig.name" v-if="toolConfig.config.type == 'password'"
                       type="password" class="form-control" v-model="checkData" v-on:keyup="syncData"/>
                <input :id="eventName + '_' + toolConfig.name" v-if="toolConfig.config.type == 'email'"
                       type="email" class="form-control" v-model="checkData" v-on:keyup="syncData"/>

                <p class="help-block">{{ useHelp()|trans }}</p>
                <div class="errorMsg text-danger" v-if="noMatch">
                    <h5>
                        <i class="fa fa-warning"></i>
                        {{'form.label.edit.column.error.message.' + toolConfig.config.type|trans}}
                    </h5>
                </div>
                <div class="errorMsg text-danger" v-if="errorMsg != ''">
                    <h5>
                        <i class="fa fa-warning"></i>
                        {{ errorMsg|trans }}
                    </h5>
                </div>
            </div>

            <label class="control-label col-md-4 col-sm-4 col-xs-12" :class="useRequired()" :for="eventName + '_' + toolConfig.name + '2'">
                {{ 'form.label.edit.column.repeat_' + toolConfig.config.type |trans }}
            </label>
            <div class="col-md-8 col-sm-8 col-xs-12">
                <input :id="eventName + '_' + toolConfig.name + '2'" v-if="toolConfig.config.type == 'password'"
                       type="password" class="form-control" debounce="500" v-model="reCheckData"/>
                <input :id="eventName + '_' + toolConfig.name + '2'" v-if="toolConfig.config.type == 'email'"
                       type="email" class="form-control" debounce="500" v-model="reCheckData"/>

                <p class="help-block">{{ useHelp()|trans }}</p>
                <div class="errorMsg text-danger" v-if="noMatch">
                    <h5>
                        <i class="fa fa-warning"></i>
                        {{'form.label.edit.column.error.message.repeat_' + toolConfig.config.type|trans}}
                    </h5>
                </div>
                <div class="errorMsg text-danger" v-if="errorMsg != ''">
                    <h5>
                        <i class="fa fa-warning"></i>
                        {{ errorMsg|trans }}
                    </h5>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
  module.exports =
    mixins: [require "components/backendbase/mixins/formComponent.coffee"]
    mounted: () ->
        @$root.$on 'form.showError', @handleError
    watch:
      checkData: (newValue, oldValue) ->
        @validate newValue, @reCheckData
      reCheckData: (newValue, oldValue) ->
        @validate newValue, @checkData
    props: ['toolConfig']
    computed:
        data: () ->
            dataKey = @toolConfig.name
            if @toolConfig.deep == true
                node = dataKey.split '.'
                if @$store.getters.editSetting.dataRow[node[0]] != undefined
                    source = @$store.getters.editSetting.dataRow[node[0]][node[1]]
                if @$store.getters.editSetting.dataRow[node[0]] == undefined
                    source = undefined
            else
                source = @$store.getters.editSetting.dataRow[dataKey]
            return source
    methods:
      handleError: (response) ->
          for key of response
              if key == @toolConfig.name
                  @errorMsg = response[key]
                  return
          @errorMsg = ''
      validate: (value, validValue) ->
        @noMatch = false
        if value != validValue && validValue != ''
          @noMatch = true
      useRequired: () ->
        if @toolConfig.required == true
            return 'required'
        else
            return ''
      useHelp: () ->
        if @toolConfig.config != undefined
            if @toolConfig.config.help != undefined
                return @toolConfig.config.help
            else
                return ''
        else
            return ''
      syncData: () ->
        @$store.dispatch 'edit.syncData',
          key: @toolConfig.name
          deep: @toolConfig.deep
          value: @checkData
    data: () ->
        checkData: ''
        reCheckData: ''
        noMatch: false
        errorMsg: ''
        eventName: @$route.name
</script>

<style lang="sass?indentedSyntax" type="text/sass" scoped>

</style>