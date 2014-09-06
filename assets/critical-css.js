var criticalCSS = (function () {
    var exports = {};

    // lets go ahead and grab our stylesheets
    var stylesheets = document.styleSheets,
        height = window.innerHeight;

    // a public function that returns a string of our critical styles
    exports.getCriticalStyles = function () {
        var critical, rules, rule;

        // loop through our page's stylesheets
        for (var i = 0; i < stylesheets.length; i++) {
            // grab the rules from that particular stylesheet
            rules = stylesheets[i].rules;

            for (var j = 0; j < stylesheets[i].rules.length; j++) {
                rule = stylesheets[i].rules[j];

                try {
                    var el = document.querySelectorAll(rule.selectorText);
                } catch (e) {
                    continue;
                }

                if (el) {
                    for (var k = 0; k < el.length; k++) {
                        if (el[k].top < height) {
                            critical += el[k].cssText;
                        }
                    }
                }
            }
        }
        return critical;
    };
})();