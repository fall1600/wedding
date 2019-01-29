<template>
    <div class="tree-container">
        <div v-if="startRender">
            <!--{{ categoryData }}-->
            <!--<br><br>-->

            <!--categoryConfig: {{ categoryConfig }}-->
            <!--<br><br>-->

            <div id="tree"></div>
            <edit :useCustomize="useCustomize"></edit>
        </div>
    </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
require 'jqtree'
module.exports =
    props: ['useCustomize']
    mounted: () ->
        @$root.$on 'reRenderTree', @createNewNode
        @convertData()
    updated: () ->
        @convertData()
    data: () ->
        treeData: []
    methods:
        createNewNode: () ->
            $('#tree').tree('loadData', @treeData)
        convertData: () ->
            return if @treeData.length != 0
            @treeData = @formatTree()
            @renderTree()
        # 轉換tree的格式
        formatTree: () ->
            newFormat = []
            for key of @categoryData
                if @categoryData[key].parent == undefined
                    newFormat.push @categoryData[key]
            return newFormat
        renderTree: () ->
            me = @
            $('#tree').tree({
                data: @treeData
                autoOpen: true
                dragAndDrop: true
                closedIcon: $("<i class='fa fa-chevron-right'></i>")
                openedIcon: $("<i class='fa fa-chevron-down'></i>")
                onCreateLi: (node, $li) ->
                    # 如果有使用config設定
                    if me.categoryConfig != undefined
                        if node.level >= me.categoryConfig.levelLimit
                            createBtn = ""
                        else
                            createBtn = "<button class='create btn btn-primary btn-xs' data-node-id=#{node.id}><i class='fa fa-plus' data-node-id=#{node.id}></i> 新增</button>"
                    else
                        createBtn = "<button class='create btn btn-primary btn-xs' data-node-id=#{node.id}><i class='fa fa-plus' data-node-id=#{node.id}></i> 新增</button>"
                    editBtn = "<button class='edit btn btn-default btn-xs' data-node-id=#{node.id}><i class='fa fa-pencil-square-o' data-node-id=#{node.id}></i> 編輯</button>"
                    deleteBtn = "<button class='delete btn btn-danger btn-xs' data-node-id=#{node.id}><i class='fa fa-trash-o' data-node-id=#{node.id}></i> 刪除</button>"
                    tools = "<div class='tools-bar btn-group'>#{createBtn} #{editBtn} #{deleteBtn}</div> "
                    $li.find('.jqtree-element').append(tools)

                    if node.status == false
                        $li.find('.jqtree-title').css({ color: 'gray'})

                    if node.is_leaf == true
                        if node.status == true
                            icon = "<span class='status-icon text-info'><i class='fa fa-file-o'></i></span>"
                        if node.status == false
                            icon = "<span class='status-icon' style='color: gray'><i class='fa fa-file-o'></i></span>"
                        $li.find('.jqtree-title').prepend(icon)
                    if node.is_leaf == false
                        if node.status == true
                            icon = "<span class='status-icon text-info'><i class='fa fa-folder'></i></span>"
                        if node.status == false
                            icon = "<span class='status-icon' style='color: gray'><i class='fa fa-folder'></i></span>"
                        $li.find('.jqtree-title').prepend(icon)
            })

            $('#tree').tree('loadData', @treeData)

            # 清除事件
            $('#tree').unbind 'tree.click'
            $('#tree').unbind 'tree.move'
            $('#tree').off 'click', '.create'
            $('#tree').off 'click', '.edit'
            $('#tree').off 'click', '.delete'
            $('#tree').bind 'tree.click', (event) ->
                node = event.node
                me.$store.dispatch 'category.getChildren', node.id
                me.treeData = []
            $('#tree').bind 'tree.move', (event) ->
                moveId = event.move_info.moved_node.id
                targetId = event.move_info.target_node.id
                position = event.move_info.position
                moveLevel = $('#tree').tree('getNodeById', moveId).getLevel()
                targetLevel = $('#tree').tree('getNodeById', targetId).getLevel()

                if me.categoryConfig != null && me.categoryConfig != undefined
                    if position == 'inside' && targetLevel > me.categoryConfig.levelLimit
                        me.$store.dispatch 'alert',
                            style: 'error'
                            title: '移動失敗'
                            message: '移動失敗'
                        me.$store.dispatch 'category.reload.tree'
                        return
                data =
                    move: moveId
                    target: targetId
                    position: event.move_info.position
                me.$store.dispatch 'category.move', data
            $('#tree').on 'click', '.create', (e) ->
                e.stopPropagation()
                id = $(e.target).data('node-id')
                data =
                    id: id
                    status: 'create'
                me.$store.dispatch 'category.edit', data

            $('#tree').on 'click', '.edit', (e) ->
                e.stopPropagation()
                id = $(e.target).data('node-id')
                data =
                    id: id
                    status: 'update'
                me.$store.dispatch 'category.edit', data

            $('#tree').on 'click', '.delete', (e) ->
                e.stopPropagation()
                id = $(e.target).data('node-id')

                if confirm '確定要刪除?'
                    me.$store.dispatch 'category.delete', id
    watch:
        categoryMutateFinished:
            deep: true
            handler: () ->
                $('#tree').tree('loadData', @formatTree())
        categoryData:
            deep: true
            handler: () ->
                $('#tree').tree('loadData', @formatTree())
    computed:
        startRender: () ->
            return @$store.getters.categoryStartRender
        categoryEdit: () ->
            return @$store.getters.categoryEdit
        categoryData: () ->
            return @$store.getters.categoryData
        categoryModel: () ->
            return @$store.getters.categoryModel
        categoryConfig: () ->
            return @$store.getters.categoryConfig
        categoryMutateFinished: () ->
            return @$store.getters.categoryMutateFinished
    components:
        'edit': require 'components/backendbase/partial/category/edit.vue'
</script>
<style src="jqtree/jqtree.css"></style>
<style lang="sass?indentedSyntax" type="text/sass">
    .jqtree-element
        height: 30px
        &:hover
            .tools-bar
                display: inline-block
        .tools-bar
            display: none
            margin-left: 30px
        .jqtree-toggler
            .fa
                display: inline-block
                width: 20.56px
                text-align: center
    .jqtree-title
        .status-icon
            margin-right: 5px
</style>