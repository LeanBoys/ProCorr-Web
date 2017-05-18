// Linkify
function replaceURLWithHTMLLinks(text) {
    var re = /(\(.*?)?\b((?:https?|ftp|file):\/\/[-a-z0-9+&@#\/%?=~_()|!:,.;]*[-a-z0-9+&@#\/%=~_()|])/ig;
    return text.replace(re, function(match, lParens, url) {
        var rParens = '';
        lParens = lParens || '';

        var lParenCounter = /\(/g;
        while (lParenCounter.exec(lParens)) {
            var m;

            if (m = /(.*)(\.\).*)/.exec(url) ||
                    /(.*)(\).*)/.exec(url)) {
                url = m[1];
                rParens = m[2] + rParens;
            }
        }
        return lParens + "<a href='" + url + "' target='_blank' title='" + url + "'>" + url + "</a>" + rParens;
    });
}
var elm = document.getElementById('linkify');
elm.innerHTML = replaceURLWithHTMLLinks(elm.innerHTML);
 