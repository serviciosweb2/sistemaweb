$(function () {
    $("#tree").dynatree({
        children: [],
        onSelect: function(select, node) {

        },
        onDblClick: function(node, event) {
            node.toggleSelect();
        },
        onKeydown: function(node, event) {
            if( event.which == 32 ) {
                node.toggleSelect();
                return false;
            }
        },
        cookieId: "dynatree-Cb3",
        idPrefix: "dynatree-Cb3-"
    });
})