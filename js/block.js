/* The code for the Gutenberg block is created in this file block.js
and then you use Webpack to compile the data to the file block.build.js that is run by Wordpress.
How to install Wepback https://modularwp.com/how-to-build-gutenberg-blocks-jsx/
*/

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks
const patt = /cwContentTypeOptions\[(\w+)\]/i;


registerBlockType('cogwork/shortcodes', {


    // Register the block
    title: __('Cogwork'),
    icon: "admin-generic",
    category: 'common',    
    // Using attributes so values will be stored between session 
    attributes: {
        content: { type: 'string' },
        contenttext: { type: 'string' },
        typesHTML: { type: 'string' },

    },

    edit(props) {

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
            return selectboxtoremove.replace('</select>', '')

        }

        // Selecting view depending on if there is a created shortcode then starting editor

        var shortCodeViewStyle = {
            display: props.attributes.content ? "block" : "none"
        }

        var selectViewStyle = {
            display: props.attributes.content ? "none" : "block"
        }

        
        // Triggered by the cwTogglebutton
        function toggleView(e) {

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
                            }
                            else {
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
            var cwSelectbox = jQuery(e.target)
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
                        inputData: cwSelectedContentTypeValue,
                    },
                    success: function (response) {

                        cwOptiondivs.html(response);
                        cwOptiondivs.show();
                    } 
                }); 


            }
        }


        // Output to editor
        return (
            <div class="cogworkGutenbergBlock">

                <h2>CogWork</h2>


                <div class="selectView" style={selectViewStyle}>
                    <h3>Välj kortkod</h3>
                    <select name="cwContentTypeSelector" class="cwContentTypeSelector" onChange={cwGetOptions} dangerouslySetInnerHTML={{ __html: props.attributes.typesHTML }}></select>
                    <p></p>
                    <div class="optiondivs"></div>
                    <p></p>
                </div>
                <div style={shortCodeViewStyle} class="shortcodeView">
                     <h3>Vald kortkod</h3>
                    <p dangerouslySetInnerHTML={{ __html: props.attributes.contenttext }}></p>
                    <p>Kortkod: {props.attributes.content}</p>
                </div>
                <button class="cwTogglebutton" onClick={toggleView}>{props.attributes.content ? "Ändra kortkod" : "Skapa kortkod"}</button>
                <button hidden class="selectView" onClick={regretChoice}>Ångra</button>
            </div>

        );
    },

    // Output to webpage
    save: function (props) {
        return (
            <div>{props.attributes.content}</div>
        );
    }
});

