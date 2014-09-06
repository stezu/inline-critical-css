# Inline Critical CSS

This plugin should open the requested web page in a browser with an average window size and determine the styles necessary for the above-the-fold content. It should then inline the required styles in the head of the browser and move the `<link rel="stylesheet">` tags to the bottom of the page. Theoretically this should decrease the rendering time of the page and increase the total load time (an adverse effect that will hopefully not be noticed by users).

## Steps

1. Pull up web page in headless browser with a resolution of 1920x1080 (or whatever is most common)
2. Use functions to determine critical css (hopefully they exist already)
3. Determine if inlining the above css is beneficial (maybe it's too big, or whatever adverse possibilities)
4. Load the stylesheets with javascript in a non-blocking manner
5. Move all `<link rel="stylesheet">` tags to the bottom of the page in a `<noscript>` tag in case of no scripts
6. ???
7. Profit.

## Resources

- [Google PageSpeed Critical CSS](https://developers.google.com/speed/pagespeed/service/PrioritizeCriticalCss)
- [Mink Headless Browser](https://github.com/Behat/Mink)
- [Selenium2 Driver for Mink](https://github.com/Behat/MinkSelenium2Driver)
- [Penthouse Critical CSS Generator](https://github.com/pocketjoso/penthouse)
