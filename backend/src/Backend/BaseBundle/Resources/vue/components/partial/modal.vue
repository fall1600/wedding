<template>
    <div class="modal fade baseModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>

                    <!--Modal Title-->
                    <h4 class="modal-title" id="myModalLabel">
                        {{ modalConfig.title }}
                    </h4>
                </div>

                <!--Modal Body-->
                <div class="modal-body" v-html="modalConfig.content"></div>
            </div>
        </div>
    </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
module.exports =
    computed:
        modalConfig: () ->
            return @$store.getters.modalConfig
    watch:
        modalConfig: () ->
            $('.baseModal').modal('show')
</script>
