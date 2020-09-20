<template>

</template>

<script>
  import Vue from 'vue';
  import Resource from 'vue-resource';
  Vue.use(Resource);
  import VueSweetalert2 from 'vue-sweetalert2';
  Vue.use(VueSweetalert2);

  export default {
    methods: {
      applyChanges: function () {
        var changed     = [],
          changedBlocks = [],
          blocks        = this.getBlockComponents(this.$root);

        blocks.forEach(function (block) {
          if (block.hasChanged() == false) {
            return true;
          }

          block.syncContent();

          changed.push({
            rawText         : block.rawText,
            validationRules : block.validationRules,
            lang            : block.lang,
            table           : block.table,
            source_key      : block.source_key,
            source_value    : block.source_value,
            target_key      : block.target_key,
            target_value    : block.target_value,
          });
          changedBlocks.push(block);
        });

        if (changed.length !== 0) {
          this.$swal({
              title: 'Apply changes?',
              type: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Yes, save it!'
            })
            .then((result) => {
              if (result.value) {

                this.$http.post('/laravel-inline-editor', {blocks: changed})
                  .then(response => {
                    if (response.status === 200) {
                      this.$swal({
                        position: 'top-end',
                        type: 'success',
                        title: 'Your work has been saved',
                        showConfirmButton: false,
                        timer: 1500
                      })
                    }
                  }, response => {
                    // error callback

                    if( response.body.message === 'The given data was invalid.') {
                      this.$swal({
                        type: 'error',
                        title: 'The given data was invalid',
                        text: response.body.errors.target_value
                      });
                    }
                    else {
                      this.$swal({
                        type: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong!'
                      });
                    }
                  });
              }
            })
        }
      },

      getBlockComponents: function(parent) {
        var blocks = [];

        parent.$children.forEach(function (component) {
          if (component.$options._componentTag == 'inline-content-block') {
            blocks.push(component);
            return;
          }

          if (component.$children.length > 0) {
            var childrenBlocks = this.getBlockComponents(component);

            childrenBlocks.forEach(function (block) {
              blocks.push(block);
            });
          }
        }.bind(this));

        return blocks;
      }
    },

  };
</script>
