<template>
    <div class="loader__wrapper">
        <div class="loader"></div>
    </div>
</template>
<style lang="sass?indentedSyntax" type="text/sass">
    @-webkit-keyframes load8
      0%
        -webkit-transform: rotate(0deg)
        transform: rotate(0deg)

      100%
        -webkit-transform: rotate(360deg)
        transform: rotate(360deg)
    @keyframes load8
      0%
        -webkit-transform: rotate(0deg)
        transform: rotate(0deg)

      100%
        -webkit-transform: rotate(360deg)
        transform: rotate(360deg)
    .loader
        $loader-size: 5em
        $loader-width: .5em
        $loader-speed: .7s
        border-radius: 50%
        width: $loader-size
        height: $loader-size
        position: relative
        text-indent: -9999em
        border-top: $loader-width solid rgba(255, 255, 255, 0.2)
        border-right: $loader-width solid rgba(255, 255, 255, 0.2)
        border-bottom: $loader-width solid rgba(255, 255, 255, 0.2)
        border-left: $loader-width solid #ffffff
        -webkit-transform: translateZ(0)
        -ms-transform: translateZ(0)
        transform: translateZ(0)
        -webkit-animation: load8 $loader-speed infinite linear
        animation: load8 $loader-speed infinite linear
        &:after
            border-radius: 50%
            width: $loader-size
            height: $loader-size
        &__wrapper
            position: fixed
            left: 0
            top: 0
            height: 100%
            width: 100%
            background: rgba(#333, .9)
            z-index: 10
            text-align: center
            &:before, > *
                display: inline-block
                vertical-align: middle
            &:before
                content: ''
                height: 100%

</style>