<template>
  <div data-role="tinymce-wrap">
    <div class="progress" v-if="progressbarRender">
      <div class="progress-bar progress-bar-striped active" :class="progressStatus" role="progressbar"
       aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" :style="progressStyle" v-show="progress != 0"></div>
    </div>
    <textarea data-role="tinymce" :id="uid" :tinymce-uid="uid">{{value}}</textarea>
    <input data-role="tinymce-file-browser" :tinymce-file-browser-uid="uid" type="file" accept="image/*">
  </div>
</template>

<script>
  var uuidV4 = require('uuid/v4');
  require('tinymce/tinymce');
  require('tinymce/themes/modern/theme');
  require('tinymce/plugins/paste/plugin');
  require('tinymce/plugins/link/plugin');
  require('tinymce/plugins/autoresize/plugin');
  require('tinymce/plugins/image/plugin');
  require('tinymce/plugins/media/plugin');
  require('tinymce/plugins/table/plugin');
  require('tinymce/plugins/code/plugin');
  require('tinymce/plugins/advlist/plugin');
  require('tinymce/plugins/autolink/plugin');
  require('tinymce/plugins/lists/plugin');
  require('tinymce/plugins/charmap/plugin');
  require('tinymce/plugins/print/plugin');
  require('tinymce/plugins/preview/plugin');
  require('tinymce/plugins/anchor/plugin');
  require('tinymce/plugins/searchreplace/plugin');
  require('tinymce/plugins/visualblocks/plugin');
  require('tinymce/plugins/fullscreen/plugin');
  require('tinymce/plugins/insertdatetime/plugin');
  require('tinymce/plugins/contextmenu/plugin');
  module.exports = {
    props: ['value', 'upload'],
    mounted: function mounted() {
      this.init();
    },
    data: function data() {
      return {
        progressbarRender: false,
        progress: 0,
        progressStatus: 'progress-bar-success'
      };
    },
    methods: {
      init: function init() {
        var self = this;
        self.progressbarRender = false;
        $(function () {
          self.progressbarRender = true;
        });

        tinymce.remove({
          selector: 'textarea[tinymce-uid=' + self.uid + ']'
        });

        tinymce.init({
          resize: 'both',
          selector: 'textarea[tinymce-uid=' + self.uid + ']',
          skin: false,
          file_picker_types: 'image',
          min_height: 450,
          plugins: ['advlist autolink lists link image charmap print preview anchor', 'searchreplace visualblocks code fullscreen', 'insertdatetime media table contextmenu paste code table'],
          image_title: true,
          toolbar: 'code | undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table | link uploadphoto | fullscreen',
          menubar: false,
          setup: function setup(editor) {
            editor.on('blur', function (e) {
              self.$emit('input', editor.getContent());
            });

            if (self.upload != undefined && typeof self.upload === 'function') {
              editor.addButton('uploadphoto', {
                icon: 'image',
                onclick: function onclick() {
                  self.fileBrowser[0].click();
                }
              });
            }
          }
        });

        this.fileBrowser.change(function (event) {
          var formData = new window.FormData();
          formData.append('file', event.target.files[0]);

          self.progress = 0;
          self.progressStatus = 'progress-bar-success';

          self.upload('/photo/default', formData, function (progress) {
            var percentage = Math.floor(100 * (progress.loaded / progress.total));
            self.progress = percentage;
          }).then(function (result) {
            var uid = result._uid;
            var url = result.large;
            var info = result.large.split(uid)[1].split('.');
            var suffix = null;
            var ext = null;
            console.log(uid, url);
            for (var index in info) {
              if (index == 1) {
                suffix = info[index];
              }
              if (index == 2) {
                ext = info[index].split('?')[0];
              }
            }
            var imageTag = '<img src=' + url + ' class=\'img-responsive\' data-type=\'img\' data-uid=' + uid + ' data-suffix=' + suffix + ' data-ext=' + ext + ' />';
            tinymce.execCommand('mceInsertContent', false, imageTag);
            self.resetProgress();
          }).catch(function (reason) {
            self.progressStatus = 'progress-bar-danger';
            self.resetProgress();
          });
        });
      },
      resetProgress: function resetProgress() {
        var self = this;
        this.fileBrowser.val('');
        setTimeout(function () {
          self.progress = 0;
        }, 1000);
      }
    },
    watch: {
      value: function value() {
        if (this.value == null || this.value == undefined) return;
        if (tinymce.get(this.uid) == null || tinymce.get(this.uid) == undefined) return;
        tinymce.get(this.uid).setContent(this.value);
      }
    },
    computed: {
      uid: function uid() {
        return uuidV4();
      },
      fileBrowser: function fileBrowser() {
        return $('[tinymce-file-browser-uid=' + this.uid + ']');
      },
      progressStyle: function progressStyle() {
        return 'width: ' + this.progress + '%';
      }
    }
  };
</script>

<style src="tinymce/skins/lightgray/skin.min.css"></style>
<style lang="sass?indentedSyntax" type="text/sass" scoped>
div[data-role="tinymce-wrap"]
  .progress
    height: 5px
    margin-bottom: 0
  input[tinymce-file-browser-uid]
    display: none
</style>
