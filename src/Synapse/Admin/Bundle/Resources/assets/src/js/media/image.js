// cropping module for synapse images
$(() => {
  const _templates = {
    formatted_image: image_web_path => `<img src="${ image_web_path }" />`,
    success_message: () => `
      <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <strong>Success !</strong> The new format has been saved.
      </div>
    `
  };

  $('.synapse-image-format').each((index, elt) => {
    var $this = $(elt);
    var croppers = {};

    $this.find('.panel-signets a[data-toggle="tab"]').on('shown.bs.tab', (e) => {
      var $img = $this.find('.cropping-area img:visible');
      var id = $img.prop('id');

      if (id && !croppers[id]) {
        var $container = $img.parents('.format-container');
        var $data = $img.parents('.cropping-area');
        var $displayArea = $container.find('.display-area');

        croppers[id] = { ratio: $data.data('ratio') };

        $img.cropper({
          minContainerWidth: "100%",
          aspectRatio: croppers[id].ratio,
          zoomOnWheel: false,
          crop: (e) => {
            croppers[id].data = {
              x: e.x, y: e.y, width: e.width, height: e.height,
              rotate: e.rotate, scaleX: e.scaleX, scaleY: e.scaleY
            }
          }
        });
        $container.find('.validate-crop').click((e) => {
          e.preventDefault();

          // toggle buttons
          var $crop = $(e.currentTarget).addClass('hidden');
          var $spinner = $crop.siblings('.spinner-crop').removeClass('hidden');

          // server call
          $.ajax({
            url: $data.data('format-endpoint'),
            method: 'POST',
            data: {
              options: croppers[id].data
            },
            success: (data) => {
              $crop.removeClass('hidden');
              $spinner.addClass('hidden');

              $displayArea.find('.alert-dismissible').remove();
              $displayArea
                .prepend(_templates.success_message())
                .find('.formatted-image')
                  .empty()
                  .html(_templates.formatted_image(
                    $displayArea.data('assets-prefix')
                    + data.webPath
                    + '?' + new Date().getTime()
                  ))
              ;
              $container.find('.cancel-crop').click();
            }
          });
        });
      }
    });
  });
});
