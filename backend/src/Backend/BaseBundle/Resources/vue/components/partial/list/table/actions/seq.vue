<template>
    <span>
        <button class="btn btn-success" @click="changeSeq('up')">
            <i class="fa fa-arrow-circle-up"></i>
        </button>

        <button class="btn btn-success" @click="changeSeq('down')">
            <i class="fa fa-arrow-circle-down"></i>
        </button>
    </span>
</template>

<script lang="babel!coffee" type="text/coffeescript">
module.exports =
    props: ['dataId']
    methods:
        # 更動排序事件
        changeSeq: (move) ->
            console.log @dataId, move
#            @$store.dispatch 'list.changeSeq',
#                id: @dataId
#                move: move
</script>