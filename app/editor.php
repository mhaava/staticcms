<script
    src="https://code.jquery.com/jquery-3.4.1.min.js"
    integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
    crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.2.1/tinymce.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />

<script>
    var config = {
        selector: '.cms-editable-text',
        menubar: false,
        inline: true,
        plugins: [
            "lists",
            "link",
            "paste",
        ],
        toolbar: [
            'undo redo | fontsizeselect | bold italic underline',
            'forecolor backcolor | alignleft aligncenter alignright alignfull | numlist bullist outdent indent | link'
        ],
        valid_elements: 'strong,em,span[style],a[href],ul,ol,li,br',
        valid_styles: 'font-size,color,text-decoration,text-align',
        powerpaste_word_import: 'clean',
        powerpaste_html_import: 'clean',
        content_css: [
            '//fonts.googleapis.com/css?family=Montserrat&display=swap'
        ],
        paste_as_text: true,
        force_br_newlines : true,
        force_p_newlines : false,
        remove_trailing_brs: false,
        forced_root_block : '',
        init_instance_callback: function (editor) {
            editor.on('blur', function (e) {
                saveText(editor);
            });
        }
    };

    function saveText(editor)
    {
        var element = editor.targetElm;
        var index = $( ".cms-editable-text" ).index( element );
        var html = element.innerHTML;
        $.ajax({
            url: '/admin/ajax/saveText',
            type: 'POST',
            dataType: 'json',
            data: {
                'index': index,
                'content': html,
                'url': window.location.pathname,
            }
        });
    }

    function saveImage($img)
    {
        var index = $img.parent().attr('data-index');
        var src = $img.attr('src');
        $.ajax({
            url: '/admin/ajax/saveImage',
            type: 'POST',
            dataType: 'json',
            data: {
                'index': index,
                'src': src,
                'url': window.location.pathname,
            }
        }).done(function(data){
            location.reload();
        });
    }

    $(function()
    {
        tinymce.init(config);
        $('.cms-editable-image').parent().append('<i class="cms-editable-image-edit">Edit</i>');

        $('body').on('click', '.cms-editable-image-edit', function(e)
        {
            $('#ex1').modal();
            $('#image_list').attr('data-index', $('.cms-editable-image-edit').index($(this)));
        });

        $('body').on('click', '#image_list img', function(e)
        {
            saveImage($(this));
        });
        $imageEdit = $('.cms-editable-image-edit');
        $imageEdit.parent().css("position", "relative");
        $imageEdit.css({
            "font-size": "30px",
            "width": "80px",
            "height": "80px",
            "line-height": "80px",
            "text-align": "center",
            "opacity": "0.5",
            "position": "absolute",
            "background-color": "white",
            "border-radius": "50%",
            "top": "85%",
            "left": "calc(50% + 80px)",
            "transform": "translate(-85%, -50%)",
            "-ms-transform": "translate(-85%, -50%)",
            "margin": "-50px",
            "color": "black"
        });
        $imageEdit.hover(function(){
            $(this).css("opacity", "1");
        }, function(){
            $(this).css("opacity", "0.5");
        });
    });
</script>

<div id="ex1" class="modal">
    <div id="image_list">
        <?php
            $directory = BASEPATH. '/output/assets/images';
            $images = glob("$directory/*.{jpg,jpeg,png,bmp,gif}", GLOB_BRACE);
            foreach ($images as $image) {
                $image = explode('/', $image);
                $image = end($image);
        ?>
                <img src="/assets/images/<?php echo $image ?>" alt="" width="300" style="max-height: 300px; height: auto;"><br><br>
        <?php
            }
        ?>
    </div>
    <a href="#" rel="modal:close">Close</a>
</div>