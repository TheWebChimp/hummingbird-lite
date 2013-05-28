tinymce.init({
	selector: "textarea.wysiwyg",
	theme : "advanced",
    plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",
    // Theme options
    theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,fontselect,fontsizeselect",
    theme_advanced_buttons2 : "cut,copy,paste,pastetext,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,link,unlink,anchor,image,code,|,forecolor,backcolor",
    theme_advanced_statusbar_location : "bottom",
    theme_advanced_resizing : true,
    theme_advanced_resize_horizontal : false,
    force_br_newlines : false,
    convert_newlines_to_brs: false
});

jQuery(document).ready(function($) {

    $('#per_page').on('change', function() {
        var list = $('#list');
        var el = $(this);
        var type = list.data('type');
        var page = list.data('page');
        var limit = el.val() || 10;
        var order = list.data('order');
        window.location.href = '?type='+type+'&page='+page+'&per_page='+limit+'&order='+order;
    });

    $('#order').on('change', function() {
        var list = $('#list');
        var el = $(this);
        var type = list.data('type');
        var page = list.data('page');
        var limit = list.data('limit');
        var order = el.val();
        window.location.href = '?type='+type+'&page='+page+'&per_page='+limit+'&order='+order;
    });

});