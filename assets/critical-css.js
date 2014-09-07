var criticalCSS = (function () {
    var exports = {};

    // lets go ahead and grab our stylesheets
    var stylesheets = document.styleSheets,
        height = window.innerHeight;

    // loop through the rules for this stylesheet
    function getCriticalSelectors(rule) {
        var styles = '';

        // check that this rule's selector is a valid selector and if so,
        // set the selection as the el
        try {
            var el = document.querySelectorAll(rule.selectorText);
        } catch (e) {
            return styles;
        }

        // if the selection of elements exists, loop through them and check if
        // they are 'above the fold'.  if so, add them to our critical string
        if (el) {
            for (var l = 0; l < el.length; l++) {
                if (el[l].getBoundingClientRect().top < height) {
                    styles += rule.cssText;
                    break;
                }
            }
        }

        return styles;
    }

    // a public function that returns a string of our critical styles
    exports.getCriticalStyles = function () {
        var critical = '',
            rules, rule, subRules, subRule;

        // loop through our page's stylesheets
        for (var i = 0; i < stylesheets.length; i++) {
            // grab the rules from that particular stylesheet
            rules = stylesheets[i].rules;

            if (rules && window.matchMedia(stylesheets[i].media.mediaText).matches) {
                for (var j = 0; j < rules.length; j++) {
                    rule = rules[j];

                    // if this is a media query
                    if (rule.cssRules) {
                        subRules = rule.cssRules;

                        for (var k = 0; k < subRules.length; k++) {
                            var mediaQuery = rule.media.mediaText;

                            subRule = subRules[k];

                            if (window.matchMedia(mediaQuery).matches) {
                                critical += '@media ' + mediaQuery + ' {' + getCriticalSelectors(subRule) + '}';
                            }
                        }
                    }

                    // this is a standard rule
                    else {
                        critical += getCriticalSelectors(rule);
                    }
                }
            }
        }

        return critical;
    };

    // determine the filesize in bytes of an arbitrary string
    exports.getFileSize = function (string) {

        // if string is not set, return the filesize of the critical css
        if (!string) {
            string = this.getCriticalStyles();
        }

        return encodeURI(string).split(/%..|./).length - 1;
    };

    return exports;
})();

window.addEventListener('load', function () {
    var criticalStyles = criticalCSS.getCriticalStyles(),
        http = new XMLHttpRequest();

    // open the post connection to the server
    http.open("POST", iccss.ajaxurl, true);

    // let them know we're sending data
    http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    // something that's in every demo
    http.onreadystatechange = function () {
        if (http.readyState != 4 || http.status != 200) return;
    };

    // send the data with the appropriate action that wordpress is expecting
    http.send("action=iccss_cache_critical_css&critical_css=" + criticalStyles);
});
