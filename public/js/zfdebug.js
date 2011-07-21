var ZFDebugLoad = window.onload;
window.onload = function(){
    if (ZFDebugLoad) {
        ZFDebugLoad();
    }
    jQuery.noConflict();
};

function ZFDebugPanel(name) {
    jQuery(".ZFDebug_panel").each(function(i){
        if(jQuery(this).css("display") == "block") {
            jQuery(this).slideUp();
        } else {
            if (jQuery(this).attr("id") == name)
                jQuery(this).slideDown();
            else
                jQuery(this).slideUp();
        }
    });
}

function ZFDebugSlideBar() {
    if (jQuery("#ZFDebug_debug").position().left > 0) {
        document.cookie = "ZFDebugCollapsed=1;expires=;path=/";
        ZFDebugPanel();
        jQuery("#ZFDebug_toggler").html("&#187;");
        return jQuery("#ZFDebug_debug").animate({left:"-"+parseInt(jQuery("#ZFDebug_debug").outerWidth()-jQuery("#ZFDebug_toggler").outerWidth()+1)+"px"}, "normal", "swing");
    } else {
        document.cookie = "ZFDebugCollapsed=0;expires=;path=/";
        jQuery("#ZFDebug_toggler").html("&#171;");
        return jQuery("#ZFDebug_debug").animate({left:"1px"}, "normal", "swing");
    }
}

function ZFDebugToggleElement(name, whenHidden, whenVisible){
    if(jQuery(name).css("display")=="none"){
        jQuery(whenVisible).show();
        jQuery(whenHidden).hide();
    } else {
        jQuery(whenVisible).hide();
        jQuery(whenHidden).show();
    }
    jQuery(name).slideToggle();
}