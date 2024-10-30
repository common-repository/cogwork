/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports) {

/* The code for the Gutenberg block is created in this file block.js
and then you use Webpack to compile the data to the file block.build.js that is run by Wordpress.
How to install Wepback https://modularwp.com/how-to-build-gutenberg-blocks-jsx/
*/

var __ = wp.i18n.__;
var registerBlockType = wp.blocks.registerBlockType;

var patt = /cwContentTypeOptions\[(\w+)\]/i;

registerBlockType('cogwork/shortcodes', {

    // Register the block
    title: __('Cogwork'),
    icon: "admin-generic",
    category: 'common',
    // Using attributes so values will be stored between session 
    attributes: {
        content: { type: 'string' },
        contenttext: { type: 'string' },
        typesHTML: { type: 'string' }

    },

    edit: function edit(props) {

        props.setAttributes({ typesHTML: getTypeOptions() });

        /* Get HTML data from server and remove the containing selectbox because the onclick attribute for the select tag will
           not be triggered in React. */

        function getTypeOptions() {
            /* cw_script_vars.htmlcontent is a variabel with the HTML data created on servers side in the
               gutenbergblock.php script */
            var selectboxtoremove = cw_script_vars.htmlcontent;
            //Remove the select start tag.         
            selectboxtoremove = selectboxtoremove.substring(selectboxtoremove.indexOf(">") + 1);
            // Also remove the end select tag and return the value
            return selectboxtoremove.replace('</select>', '');
        }

        // Selecting view depending on if there is a created shortcode then starting editor

        var shortCodeViewStyle = {
            display: props.attributes.content ? "block" : "none"
        };

        var selectViewStyle = {
            display: props.attributes.content ? "none" : "block"

            // Triggered by the cwTogglebutton
        };function toggleView(e) {

            var cwClickedButton = jQuery(e.target);
            /* using closest and class name instead of parent because it works
            /  even if you add exta div wrappers. */
            var cwClickedButtonParent = cwClickedButton.closest(".cogworkGutenbergBlock");

            var cwSelectView = cwClickedButtonParent.find(".selectView");
            var cwShortCodeView = cwClickedButtonParent.find(".shortcodeView").first();

            //if select shortcode is visible
            if (cwSelectView.is(":visible")) {

                var cwTypeSelector = cwClickedButtonParent.find(".cwContentTypeSelector").first();
                var cwTypeSelectorValue = cwTypeSelector.val();

                // Create shortcode and switch view if shortcode is selected
                if (cwTypeSelectorValue) {
                    //Set shortcode value
                    var cwShortcode = '[cw ' + cwTypeSelectorValue;
                    //Set shortcode text displayed in editor
                    var cwShortcodeText = "<strong>Typ av kortkod: " + cwTypeSelector.find('option:selected').text() + "</strong>";
                    //Get selected options
                    cwClickedButtonParent.find('.cwContentTypeOptions').each(function () {
                        var currentElement = jQuery(this);
                        var value = currentElement.val();

                        if (value > '') {
                            var name = currentElement.attr('name');
                            var optionName = patt.exec(name)[1];
                            //Set options value for shortcocdes
                            cwShortcode += ' ' + optionName + '=' + value;
                            //Set shortcode text displayed in editor
                            cwShortcodeText += "</br>" + currentElement.parent().prev().text() + ": ";
                            if (currentElement.is("input")) {
                                cwShortcodeText += value;
                            } else {
                                cwShortcodeText += currentElement.find('option:selected').text();
                            }
                        }
                    });

                    cwShortcode += ']';

                    // Set attributes so the values will be both be stored and display on webpage
                    props.setAttributes({ content: cwShortcode });
                    props.setAttributes({ contenttext: cwShortcodeText });

                    // Swicth view
                    cwSelectView.hide();
                    cwShortCodeView.show();
                    cwClickedButton.text("Ändra kortkod");
                }
            }
            // Switch to select shortcode view and change text toggle butoom
            else {

                    cwSelectView.show();
                    cwShortCodeView.hide();
                    cwClickedButton.text("Skapa kortkod");
                }
        }

        // Triggered by the "Ångra" button
        function regretChoice(e) {
            var cwClickedButton = jQuery(e.target);
            // using closest and class name instead of parent so it you add exta div wrappers.
            var cwClickedButtonParent = cwClickedButton.closest(".cogworkGutenbergBlock");
            cwClickedButtonParent.find(".selectView").hide();
            cwClickedButtonParent.find(".shortcodeView").show();
            cwClickedButtonParent.find(".cwTogglebutton").text("Ändra shortcode");
        }

        //Remove existing suboptions and add new options when contentype value is changed.
        function cwGetOptions(e) {
            var cwSelectbox = jQuery(e.target);
            var cwSelectboxParent = cwSelectbox.closest(".selectView");
            var cwSelectedContentTypeValue = cwSelectbox.val();
            var cwOptiondivs = cwSelectboxParent.find(".optiondivs");
            //Remove existing options in cwOptionsdivs
            cwOptiondivs.html("");

            //Calling Ajax call created in script gutenbergblock.php to get options depending on wich contentype selected
            if (cwSelectedContentTypeValue) {

                jQuery.ajax({
                    url: cwgutenbergoption_ajax.ajax_url,
                    type: 'post',
                    data: {
                        action: 'cw_gutenberg_options',
                        inputData: cwSelectedContentTypeValue
                    },
                    success: function success(response) {

                        cwOptiondivs.html(response);
                        cwOptiondivs.show();
                    }
                });
            }
        }

        // Output to editor
        return wp.element.createElement(
            'div',
            { 'class': 'cogworkGutenbergBlock' },
            wp.element.createElement(
                'h2',
                null,
                'CogWork'
            ),
            wp.element.createElement(
                'div',
                { 'class': 'selectView', style: selectViewStyle },
                wp.element.createElement(
                    'h3',
                    null,
                    'V\xE4lj kortkod'
                ),
                wp.element.createElement('select', { name: 'cwContentTypeSelector', 'class': 'cwContentTypeSelector', onChange: cwGetOptions, dangerouslySetInnerHTML: { __html: props.attributes.typesHTML } }),
                wp.element.createElement('p', null),
                wp.element.createElement('div', { 'class': 'optiondivs' }),
                wp.element.createElement('p', null)
            ),
            wp.element.createElement(
                'div',
                { style: shortCodeViewStyle, 'class': 'shortcodeView' },
                wp.element.createElement(
                    'h3',
                    null,
                    'Vald kortkod'
                ),
                wp.element.createElement('p', { dangerouslySetInnerHTML: { __html: props.attributes.contenttext } }),
                wp.element.createElement(
                    'p',
                    null,
                    'Kortkod: ',
                    props.attributes.content
                )
            ),
            wp.element.createElement(
                'button',
                { 'class': 'cwTogglebutton', onClick: toggleView },
                props.attributes.content ? "Ändra kortkod" : "Skapa kortkod"
            ),
            wp.element.createElement(
                'button',
                { hidden: true, 'class': 'selectView', onClick: regretChoice },
                '\xC5ngra'
            )
        );
    },


    // Output to webpage
    save: function save(props) {
        return wp.element.createElement(
            'div',
            null,
            props.attributes.content
        );
    }
});

/***/ })
/******/ ]);