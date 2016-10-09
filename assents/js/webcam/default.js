
(function(window) {

    var document = window.document;

    function encode(str) {

        var ret;

        if (encodeURIComponent instanceof Function)
            ret = encodeURIComponent(str);
        else
            ret = escape(str);

        return ret;
    }

    function request(x, y, site) {

        var xhr;

        if (window.XMLHttpRequest) {
            xhr = new XMLHttpRequest();
        } else {
            xhr = new ActiveXObject("Microsoft.XMLHTTP");
        }

        if (xhr) {

            xhr.open("GET", "/pixel.php?r=" + site + "&x=" + x + "&y=" + y, false);

            try {
                xhr.send(null);
            } catch(e) { }
        }
        xhr = null;
    }

    function handleClick(e) {

        var posx = 0;
        var posy = 0;

        var targ;

        if (!e) e = window["event"];

        if (e.target)
            targ = e.target;
        else if (e.srcElement)
            targ = e.srcElement;
        else
            return;

        do {

            if ('a' === targ.nodeName.toLowerCase()) {

                if (e.pageX || e.pageY) {
                    posx = e.pageX;
                    posy = e.pageY;
                } else if (e.clientX || e.clientY) {
                    posx = e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
                    posy = e.clientY + document.body.scrollTop + document.documentElement.scrollTop;
                }

                posx-= (document.body.scrollWidth - 1024) >> 1;

                request(posx, posy, encode(window.location.href));

                break;
            }

        } while ((targ = targ.parentNode));
    }

    if (!window["_track"]) {

        window["_track"] = this;

        if (document.addEventListener) {
            document.addEventListener("click", handleClick, true);
        } else if (document["attachEvent"]) {
            document["attachEvent"]("onclick", handleClick);
        }
    }

})(this);
