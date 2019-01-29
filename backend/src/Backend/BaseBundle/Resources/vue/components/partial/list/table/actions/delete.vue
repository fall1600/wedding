<template>
    <span class="btn btn-success" v-on:click="deleteListItem">
        {{'action.delete'|trans}}
    </span>
</template>

<script lang="babel!coffee" type="text/coffeescript">
module.exports =
    props: ['dataId']
    methods:
        deleteListItem: (rowIndex) ->
            if confirm @$root.$options.filters.trans 'list.alert.delete'
                @$store.dispatch 'list.deleteData', @dataId
</script>