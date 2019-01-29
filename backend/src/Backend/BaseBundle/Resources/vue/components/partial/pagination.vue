<template>
    <div v-show="pager.pages > 1" class="pagination-container">
        <span class="pagination-total">
            <span class="info">
                {{'pagination.data_length'|trans}}: <strong>{{ pager.rows }}</strong>
            </span>
            <span class="info">
                {{'pagination.current_page_data_length'|trans}}: <strong>{{ data.length }}</strong>
            </span>
        </span>
        <nav aria-label="Page navigation">
            <div class="col-xs-1 pagination-input">
                <input type="number" class="form-control input-sm" v-model="inputValue" @change="changePageByInput()" @keyup.enter="changePageByInput()">
            </div>
            <ul class="pagination">
                <!--first page-->
                <li>
                    <a @click="changePage(1)"><span aria-hidden="true">&laquo;</span></a>
                </li>

                <li :class="{disabled: !hasPrevPage}">
                    <a :disabled="prevPage <= 1" @click="changePage(prevPage)"><span aria-hidden="true">&lsaquo;</span></a>
                </li>

                <!--page header-->
                <li :class="{active: pager.page==page}" v-if="showPageHeader()">
                    <a @click="changePage(1)"><span aria-hidden="true">1</span></a>
                </li>
                <li :class="{active: pager.page==page}" v-if="showPageHeader() && pager.page-show-1 != 1"><a>...</a></li>

                <!--page body-->
                <li v-for="page in pager.pages" :class="{active: pager.page==page}" v-if="renderPage(page)">

                    <a @click="changePage(page)"><span aria-hidden="true">{{page}}</span></a>
                </li>

                <!--page footer-->
                <li :class="{active: pager.page==page}" v-if="showPageFooter() && pager.page+show+1 != pager.pages"><a>...</a></li>
                <li :class="{active: pager.page==page}" v-if="showPageFooter()">
                    <a @click="changePage(pager.pages)"><span aria-hidden="true">{{pager.pages}}</span></a>
                </li>

                <!--last page-->
                <li :class="{disabled: !hasNextPage}">
                    <a @click="changePage(nextPage)"><span aria-hidden="true">&rsaquo;</span></a>
                </li>

                <li>
                    <a :disabled="nextPage >= pager.pages" @click="changePage(pager.pages)"><span aria-hidden="true">&raquo;</span></a>
                </li>
            </ul>
        </nav>
    </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
# 分頁元件
# @param string routeName, - 父元件注入的routeName, 決定分頁列導向的連結
# @param number page - 父元件注入的page, 決定分頁的數量
# 範例:
# <pagination :routeName="page-list" :pager="5"></pagination>

module.exports =
  mounted: () ->
    @initInput()
  props: ['pager']
  data: () ->
    show: 2
    inputValue: 0
  computed:
    data: () ->
        return @$store.getters.listData
    hasPrevPage: () ->
      return true if @pager.page > 1
      false
    hasNextPage: () ->
      return true if @pager.page < @pager.pages
      false
    prevPage: () ->
      return 1 if !@hasPrevPage
      @pager.page - 1
    nextPage: () ->
      return @pager.pages if !@hasNextPage
      @pager.page + 1
    renderRange: () ->
      result =
        start: 1
        end: @pager.pages
      return result if !@useShortPagination
      if @pager.page - @show >= 1
        result.start = @pager.page - @show
      if @pager.page + @show <= @pager.pages
        result.end = @pager.page + @show
      return result
    useShortPagination: () ->
      @pager.pages > 5
  methods:
    initInput: () ->
      page = @pager.page
      page = 1 if page == undefined || page == null
      @inputValue = page
    renderPage: (page) ->
      start = @renderRange.start
      end = @renderRange.end
      return false if start == undefined || end == undefined
      if start <= page && page <= end
        return true
      else
        return false
    showPageHeader: () ->
      return false if !@useShortPagination
      if @pager.page - @show > 1
        return true
      else
        return false
    showPageFooter: () ->
      return false if !@useShortPagination
      if @pager.page + @show < @pager.pages
        return true
      else
        return false
    pageChange: (page) ->
      @$root.$emit 'page-change', @, page
    changePage: (page) ->
        query = @$route.query
        query = {} if query == undefined
        query.page = page
        @$router.push
            query: {}
        @$router.push
            query: query
    changePageByInput: () ->
        @inputValue = 1 if typeof @inputValue != 'number'
        if @inputValue != '' && @inputValue != 0
            query = @$route.query
            query = {} if query == undefined
            query.page = @inputValue
            @$router.push
                query: {}
            @$router.push
                query: query
  watch:
    pager:
        deep: true
        handler: () ->
            @initInput()
</script>

<style lang="sass?indentedSyntax" type="text/sass" scoped>
    @import "../assets/sass/base.sass"
    .pagination-input
        float: right
        display: inline-block
    .pagination
        float: right
        > li
            > a
                border: 0
                color: lighten(black, 65%)
                cursor: pointer
            &.active
                > a
                    background: #eee
                    color: color_main('theme')
                    font-weight: bold
                    text-shadow: 1px 2px 1px white
        &-container
            @extend %clearfix
            clear: both
            background: white
            border: 3px solid #eee
            > nav[aria-label="Page navigation"]
                > .pagination
                    margin: 0
        &-total
            float: left
            line-height: 30px
            padding: 0 1em
            color: lighten(black, 65%)

            .info
                margin-right: 30px
</style>