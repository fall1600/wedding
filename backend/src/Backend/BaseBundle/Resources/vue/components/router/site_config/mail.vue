<template>
    <div class="page-content">
        <edit :editSetting="editSetting" :useCustomize="customize"></edit>
    </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
    module.exports =
        mixins: [require "components/backendbase/mixins/editConfig.coffee"]
        data: () ->
            customize: []
        events:
            "form.postSaveSuccess": (result) ->
                @$router.replace
                    name: "setup_mail-edit"
                return
        methods:
            getModel: () ->
                require('components/backendbase/actions/site_config/mail.coffee')
</script>
