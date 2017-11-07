<template>
    <div>
      <label class="btn btn-default btn-primary btn-sm">
          Select Image <input type="file" name="avatar" accept="image/*" hidden @change="onChange">
      </label>
    </div>
</template>

<script>
    export default {
        methods: {
            onChange(e) {
                if ( ! e.target.files.length ) {
                    flash('No image was selected', 'info');
                    console.log('No Image');
                    return;
                }
                let file = e.target.files[0];
                let reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = e => {
                    let src = e.target.result;
                    this.$emit('loaded', { src, file });
                };
            },
        }
    };

</script>