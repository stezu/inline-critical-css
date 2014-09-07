var criticalCSS = (function () {
    var exports = {};

    // lets go ahead and grab our stylesheets
    var stylesheets = document.styleSheets,
        height = window.innerHeight;

    // a public function that returns a string of our critical styles
    exports.getCriticalStyles = function () {
        var critical = '',
            rules, rule;

        // loop through our page's stylesheets
        for (var i = 0; i < stylesheets.length; i++) {
            // grab the rules from that particular stylesheet
            rules = stylesheets[i].rules;

            if (!rules) {
                continue;
            }

            // loop through the rules for this stylesheet
            for (var j = 0; j < rules.length; j++) {
                rule = rules[j];

                // check that this rule's selector is a valid selector and if so,
                // set the selection as the el
                try {
                    var el = document.querySelectorAll(rule.selectorText);
                } catch (e) {
                    continue;
                }

                // if the selection of elements exists, loop through them and check if
                // they are 'above the fold'.  if so, add them to our critical string
                if (!el) {
                    continue;
                }

                for (var k = 0; k < el.length; k++) {
                    if (el[k].getBoundingClientRect().top < height) {
                        critical += rule.cssText;
                        break;
                    }
                }
            }
        }
        return critical;
    };
    return exports;
})();