<template>
    <div class="page-title hidden-print">
        <div class="title_left">
            <h3>
                {{ pageTitle|trans({}, 'forms') }}
            </h3>
        </div>
        <div class="title_right">
            <div class="col-md-5 col-sm-5 col-xs-12 pull-right top_search">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Search">
                <span class="input-group-btn">
                    <button class="btn btn-default">
                        <i class="fa fa-search"></i>
                    </button>
                </span>
            </div>
        </div>
    </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
    module.exports =
        computed:
            pageTitle: () ->
                @$store.getters.pageTitle
</script>

<style lang="sass?indentedSyntax" type="text/sass" scoped>
    @import "../assets/sass/base.sass"
    .page-title
        @extend %clearfix
    .input-group
        opacity: 0
</style>