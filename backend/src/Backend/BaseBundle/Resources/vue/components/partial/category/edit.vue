<template>
    <div class="modal fade" id="categoryEdit">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <span v-if="categoryEdit.status == 'create'">
                        建立分類
                    </span>

                    <span v-if="categoryEdit.status == 'update'">
                        更新分類
                    </span>
                </div>
                <div class="modal-body" v-if="data != undefined">
                    <div class="input-group">
                        <div class="input-group-addon" type="button">
                            <span>
                                名稱
                            </span>
                        </div>
                        <input type="text" class="form-control" v-model="category.name">
                    </div>

                    <div class="input-group">
                        <div class="input-group-addon" type="button">
                            <span>
                                狀態
                            </span>
                        </div>
                        <select class="form-control" v-model="category.status">
                            <option :value="true">啟用</option>
                            <option :value="false">停用</option>
                        </select>
                    </div>
                </div>

                <div v-if="useCustomize">
                    <component v-bind:is="component" v-for="component in useCustomize"></component>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-sm btn-success" @click="save()">確定儲存</button>
                    <button class="btn btn-sm btn-default" data-dismiss="modal">取消</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
module.exports =
    mounted: () ->
    props: ['useCustomize']
    data: () ->
        category:
            name: ''
            status: true
    methods:
        save: () ->
            if @category.name.trim() == ''
                @$store.dispatch 'alert',
                    style: 'error'
                    title: @$options.filters.trans 'category.form.reject.title'
                    message: @$options.filters.trans 'category.form.reject.message'
                return
            me = @
            if @categoryEdit.status == 'create'
                dataId = null
                target = @categoryEdit.id
            if @categoryEdit.status == 'update'
                dataId = @categoryEdit.id
                target = null
            data =
                id: dataId
                target: target
                name: @category.name
                status: @category.status

            new Promise (resolve, reject) ->
                me.$store.dispatch 'category.save', data
                resolve()
            .then () ->
                me.$root.$emit 'reRenderTree', data
                $('#categoryEdit').modal('hide')
    computed:
        categoryEdit: () ->
            return @$store.getters.categoryEdit
        categoryData: () ->
            return @$store.getters.categoryData
        data: () ->
            return @$store.getters.categoryData[@categoryEdit.id]
        categoryResponseId:() ->
            return @$store.getters.categoryResponseId
    watch:
        categoryResponseId: (value) ->
            return if value == ''
            @$root.$emit 'category.submit', value
        categoryEdit:
            deep: true
            handler: () ->
                $('#categoryEdit').modal('show')
                if @categoryEdit.status == 'create'
                    @category.name = ''
                    @category.status = true

                if @data != undefined && @categoryEdit.status == 'update'
                    @category.name = @data.name
                    @category.status = @data.status
</script>

<style lang="sass?indentedSyntax" type="text/sass">

</style>