//Work better
var photo_counter = 0;
Dropzone.autoDiscover = false;

$(document).ready(function () {
  let myDropzone = new Dropzone('.dropzone', { //Give your input (or form if you're not sending anything else) a class of dropzone
    uploadMultiple: false,
    parallelUploads: 100,
    maxFilesize: 1, //not working
    previewsContainer: '#dropzonePreview',
    previewTemplate: document.querySelector('#preview-template').innerHTML,
    addRemoveLinks: true, //not working
    dictRemoveFile: 'Remove',
    dictFileTooBig: 'Image is bigger than 8MB',
    dictRemoveFileConfirmation: "Are you sure you wish to delete this image?",
    maxFiles: 3, //not working
    autoProcessQueue: false,

    // The setting up of the dropzone
    init: function () {
      // Add server images
      myDropzone.on('maxfilesexceeded', function (file) {
          myDropzone.removeFile(file);
        }),

        $.get('/server-images', function (data) {
          $.each(data.images, function (key, value) {
            var file = {
              name: value.original,
              size: value.size
            };
            myDropzone.options.addedfile.call(myDropzone, file);
            myDropzone.createThumbnailFromUrl(file, 'images/icon_size/' + value.server);
            myDropzone.emit("complete", file);
            $('.serverfilename', file.previewElement).val(value.server);
            photo_counter++;
            $("#photoCounter").text("(" + photo_counter + ")");
          });
        }),
        // Update selector to match your button
        $("#button").click(function (e) {
          e.preventDefault();
          myDropzone.processQueue();
        }),
        //Tambahkan semua input formulir ke formData Dropzone yang akan diPOST
        this.on('sending', function (file, xhr, formData) {
          var data = $('#formKirim').serializeArray();
          $.each(data, function (key, el) {
            formData.append(el.name, el.value);
          });
        }),
        this.on("removedfile", function (file) {
          $.ajax({
            type: 'POST',
            url: 'upload/delete',
            data: {
              id: $('.serverfilename', file.previewElement).val(),
              _token: $('#csrf-token').val()
            },
            dataType: 'html',
            success: function (data) {
              var rep = JSON.parse(data);
              if (rep.code == 200) {
                photo_counter--;
                $("#photoCounter").text("(" + photo_counter + ")");
              }

            }
          });
        }),

        this.on("error", function (file, response) {
          if ($.type(response) === "string")
            var message = response; //dropzone sends it's own error messages in string
          else
            var message = response.message;
          file.previewElement.classList.add("dz-error");
          _ref = file.previewElement.querySelectorAll("[data-dz-errormessage]");
          _results = [];
          for (_i = 0, _len = _ref.length; _i < _len; _i++) {
            node = _ref[_i];
            _results.push(node.textContent = message);
          }
          return _results;
        }),

        this.on("success", function (file, response) {
          //Do something after the file has been successfully processed e.g. remove classes and make things go back to normal. 
          $('.serverfilename', file.previewElement).val(response.filename);
          photo_counter++;
          $("#photoCounter").text("(" + photo_counter + ")");
        })

    }, //init

  });
});
