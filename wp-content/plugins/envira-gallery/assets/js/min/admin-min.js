jQuery(document).ready(function($){if($("#screen-meta-links").prependTo("#envira-header-temp"),$("#screen-meta").prependTo("#envira-header-temp"),"undefined"!=typeof Clipboard){var e=new Clipboard(".envira-clipboard");$(document).on("click",".envira-clipboard",function(e){e.preventDefault()})}$("div.envira-notice").on("click",".notice-dismiss",function(e){e.preventDefault(),$(this).closest("div.envira-notice").fadeOut(),$(this).hasClass("is-dismissible")&&$.post(envira_gallery_admin.ajax,{action:"envira_gallery_ajax_dismiss_notice",nonce:envira_gallery_admin.dismiss_notice_nonce,notice:$(this).parent().data("notice")},function(e){},"json")})});