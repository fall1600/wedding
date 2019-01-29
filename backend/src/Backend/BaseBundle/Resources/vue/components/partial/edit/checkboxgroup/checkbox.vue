<template>
  <td class="td-checkbox">
    <div class="form-checkbox">
        <input type="checkbox" :id="data.id" v-model="realStatus" @change="updateCheckboxGroup()" />
      <label :for="data.id"></label>
    </div>
    <label :for="data.id" class="option-label">{{data.name|trans}}</label>
    <br><br>
  </td>
</template>

<script lang="babel!coffee" type="text/coffeescript">
module.exports =
  props: ['position', 'dataKey']
  data: () ->
    realStatus: false
  mounted: () ->
    @init()
  methods:
    init: () ->
      @setChecked()
    updateCheckboxGroup: () ->
      newData = @$store.getters.editSetting.dataRow[@dataKey]
      if @realStatus == true
        newData.push @checkboxGroup[@position.groupIndex]['child'][@position.optionIndex]
      if @realStatus == false
        for item, index in newData
          if item != undefined
            if item.id == @checkboxGroup[@position.groupIndex]['child'][@position.optionIndex].id
              newData.splice index, 1
      @$store.dispatch 'edit.syncData',
        key: @dataKey
        value: newData
    setChecked: () ->
      return if @checkedOptions == undefined
      for checked in @checkedOptions
        if checked.id == @data.id
          @realStatus = true
          return
      @realStatus = false
  computed:
    checkboxGroup: () ->
      @$store.getters.checkboxGroup
    data: () ->
      @checkboxGroup[@position.groupIndex]['child'][@position.optionIndex]
    checkedOptions: () ->
      @$store.getters.editSetting.dataRow[@dataKey]
  watch:
    checkedOptions:
      deep: true
      handler: () ->
        @setChecked()
</script>

<style lang="sass?indentedSyntax" type="text/sass" scoped>
@import '../assets/sass/base.sass'
.form-checkbox
  @extend %form-checkbox
  display: inline-block
.checkbox-table
  tbody
    tr
      td
        .option-label
          cursor: pointer
</style>