<template>
  <div class="input-group" data-role="datetime-picker-wrap">
    <input type="text" class="form-control" data-role="datetime-picker"
           :readonly="isReadonly()" :disabled="isReadonly()">
    <span class="input-group-btn">
      <button class="btn btn-default" type="button" :disabled="isReadonly()">
        <i class="fa fa-calendar"></i>
      </button>
    </span>
  </div>
</template>

<script>
  var datetimepicker = require('eonasdan-bootstrap-datetimepicker');
  $.fn.datetimepicker = datetimepicker;
  module.exports = {
    props: ['value', 'type'],
    data: function data() {
      return {
        selector: null
      };
    },
    mounted: function mounted() {
      this.$nextTick(function () {
        this.init();
      });
    },
    methods: {
      init: function init() {
        var self = this;
        var defaultDatetime = this.value;
        if (this.value == undefined || this.value == null) {
          defaultDatetime = this.datetime(new Date());
          this.$emit('input', defaultDatetime);
        }
        this.selector = $(this.$el).find('input[data-role="datetime-picker"]');
        this.selector.datetimepicker({
          format: self.format,
          defaultDate: defaultDatetime
        });

        this.selector.on('dp.change', function (e) {
          self.$emit('input', self.selector.data().date);
        });
      },
      isReadonly: function isReadonly() {
        return false;
      },
      datetime: function datetime(time) {
        return this.$options.filters.moment(time, this.format);
      }
    },
    computed: {
      format: function format() {
        if (this.type == undefined) return 'YYYY-MM-DD HH:mm:ss';
        if (this.type == 'date') return 'YYYY-MM-DD';
        return 'YYYY-MM-DD HH:mm:ss';
      }
    },
    watch: {
      value: function value(_value) {
        $(this.$el).find('input[data-role="datetime-picker"]').val(this.value);
      }
    }
  };
</script>

<style src="eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css"></style>
<style lang="sass?indentedSyntax" type="text/sass">
div[data-role="datetime-picker"]
  display: inline-block
</style>