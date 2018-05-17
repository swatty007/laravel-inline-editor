<template>

</template>

<script>
  var MediumEditor = require('medium-editor/dist/js/medium-editor');

  export default {

    data: function () {
      return {
        medium          : null,
        table           : null,
        options         : null,
        source_key      : null,
        source_value    : null,
        target_key      : null,
        target_value    : null,
      }
    },

    mounted: function() {
      this.table      = this.$el.attributes.table.nodeValue;

      var optionsString = this.$el.attributes.options.nodeValue;
      this.options = JSON.parse( optionsString.replace(/'/g, '"') );

      this.medium     = new MediumEditor(this.$el, this.options );
      this.medium.setContent( this.$el.attributes.content.nodeValue );

      this.source_key     = this.$el.attributes.source_key.nodeValue;
      this.source_value   = this.$el.attributes.source_value.nodeValue;

      this.target_key     = this.$el.attributes.target_key.nodeValue;
      this.target_value   = this.$el.attributes.content.nodeValue;
    },

    methods: {
      getContent: function () {
        return this.medium.getContent();
      },
      syncContent: function() {
        this.target_value = this.getContent();
      },
      hasChanged: function () {
        return this.getContent() != this.target_value;
      }
    },
  }
</script>
