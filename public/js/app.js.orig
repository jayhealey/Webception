/**
 * This file is part of the Webception package.
 *
 * (c) James Healey <jayhealey@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*
 * The following Application takes care of 6 parts:
 *
 *      Codeception : Check if Codeception is runnable.
 *      Toolbar     : Handle the toolbar state based on the Application state.
 *      State       : Hold the Application state.
 *      Tests       : Handlers for running tests & updating UI test elements.
 *      Console     : Handlers for the Console.
 *      Mode        : Allows test mode to be run (by setting a query string value).
 */

APP = {

    /**
     * Initialize the Application.
     */
    init: function() {

        // See if Codeception is runnable.
        APP.codeception.init();

        // Setup any other Test interactions.
        APP.test.init();

        // Setup the app & toolbar states.
        APP.state.init();

        // Setup the Console filters
        APP.console.init();

    },

    /**
     * Codeception Initialization
     */
    codeception: {

        vars: {

            ready: false,
            eCodeceptionButton: '#button_codeception',
            eLogButton: '#button_logs',
            eHideOnError: '.hide_on_error',
            eErrorContainer: '#error_container',
            eErrorMessage: '#error_container .message',
            eErrorResource: '#error_container strong.resource',
            eErrorConfig: '#error_container strong.config',
            sButton: 'button ',

            /**
             * List of checks to run.
             */
            checks: {
                'log': {
                    button: '#button_logs',
                    label: 'LOG',
                    url: 'logs',
                },
                'executable': {
                    button: '#button_codeception',
                    label: 'EXECUTABLE',
                    url: 'executable',
                }
            },

            /**
             * All the available states for the Codeception button
             */
            states: {
                'running': {
                    css: 'secondary disabled',
                    label: '%s <strong>EXECUTABLE...</strong>',
                    showError: false,
                },
                'passed': {
                    css: 'success ',
                    label: '%s <strong>PASSED</strong>',
                    showError: false,
                },
                'failed': {
                    css: 'alert',
                    label: '%s <strong>FAILED</strong>',
                    showError: true,
                },
            }
        },

        init: function()
        {
            // Run each check individually.
            Object.keys(APP.codeception.vars.checks).forEach(function (key)
            {
                APP.codeception.check(key);
            });
        },

        /**
         * Make an AJAX call to see if Codeception is executable.
         */
        check: function(type)
        {
            var chosen_type = APP.codeception.vars.checks[type];

            $.ajax({
                url         : chosen_type.url,
                method      : 'GET',
                cache       : false,
                dataType    : 'json',
                data        : APP.mode.data(),
                beforeSend  : function (xhr, settings)
                {
                    // Hide any error messages
                    //      and set the state of the codeception status to running.
                    $(APP.codeception.vars.eErrorContainer).hide();

                    APP.codeception.refresh('running', xhr, chosen_type);

                },
                success: function(data, status, xhr)
                {
                    // Update the Codeception button to indicate success!
                    APP.codeception.refresh('passed', data, chosen_type);
                },
                error: function(xhr, status, message)
                {
                    // Update the Codeception button to indicate it's not ready.
                    APP.codeception.refresh('failed', xhr, chosen_type);
                }
            });
        },

        /**
         * Refresh the state of the Codeception state button
         *
         * @param String    state
         * @param JSON      response
         */
        refresh: function(state, response, type)
        {
            // Load the details of the current state
            var chosenState = APP.codeception.vars.states[state];

            // Filter in the check-type into the button label
            var message = sprintf(chosenState.label, type.label);

            // Update the button state.
            $(type.button)
                .attr('class', APP.codeception.vars.sButton + chosenState.css)
                .html(message);

            // If Codeception is not setup properly,
            // show the error message and hide all the tests.
            if (chosenState.showError)
            {
                $(APP.codeception.vars.eHideOnError).hide();

                // If the resource details are not set, it means
                // the Codeception config is really broken.
                // An error will already show in that case.
                if (response.responseJSON.resource.length > 0)
                {
                    $(APP.codeception.vars.eErrorMessage).html(response.responseJSON.error);
                    $(APP.codeception.vars.eErrorResource).html(response.responseJSON.resource);
                    $(APP.codeception.vars.eErrorConfig).html(response.responseJSON.config);
                    $(APP.codeception.vars.eErrorContainer).show();
                }
            }
        }
    },

    /**
     * Store and Update the state of the Application
     */
    state: {
         vars: {

            // Default state. Used to reset the state if states change.
            currentState: 'ready',

            // Tallies for the test states
            countTests: 0,
            countRun: 0,
            countPass: 0,
            countFail: 0,

            // State booleans
            running: false,
            stopped: false,

        },

        init: function() {

            // Initial count of all the available tests
            APP.state.updateCount();

            // Update the UI to reflect the current state
            APP.state.refresh('ready');
        },

        /**
         * Set the Application state.
         *
         * @param String state
         */
        refresh: function(state) {

            // Get the current state
            APP.state.vars.currentState = state;

            // Load the details of the current state
            var chosenState = APP.toolbar.vars.states[state];

            // Set all the variables that can be used in the status message.
            var message_counts = {
                tests: APP.state.vars.countTests,
                run: APP.state.vars.countRun,
                pass: APP.state.vars.countPass,
                fail: APP.state.vars.countFail,
            };

            // Pass the message_counts into the chosen state message
            message = sprintf(chosenState.message, message_counts);

            // Grab the container
            $status = $(APP.toolbar.vars.eStatusContainer);

            // Get the click event for the action button
            var bind = chosenState.bind;

            // Update the message state and button states.
            APP.state.vars.running = chosenState.running;
            APP.state.vars.stopped = chosenState.stopped;

            // Update the message box (if required)
            $(APP.toolbar.vars.eMessageContainer, $status).html(message);

            // Update the class & content of the state button.
            $(APP.toolbar.vars.eButtonState, $status)
                .attr('class', APP.toolbar.vars.sButton + chosenState.buttonReadyCss)
                .html('<strong>'+ chosenState.buttonReadyLabel +'</strong>');

            // Update the bind, class & content on the action button.
            $(APP.toolbar.vars.eButtonAction, $status)
                .unbind('click')
                .bind('click', function(e) {
                    e.preventDefault();

                    // Bind the new click event to the action button.
                    // Also - I hate this line of code. Is there a better way?
                    eval("APP.test." + bind + "()");
                })
                .attr('class', APP.toolbar.vars.sButton + chosenState.buttonActionCss)
                .html('<strong>'+ chosenState.buttonActionLabel +'</strong>');
        },

        /**
         * Update the count of chosen tests.
         */
        updateCount: function()
        {
            APP.state.vars.countTests = APP.test.find().length;
        },

        /*
         * State Check: Return if the Tests are running.
         */
        isRunning: function() {
            return APP.state.vars.running;
        },

        /*
         * State Check: Return if the Tests have been stopped.
         */
        isStopped: function() {
            return APP.state.vars.stopped;
        },

        /*
         * State Check: Return if the chosen tests have been run.
         */
        isCompleted: function() {
            return APP.state.vars.countRun == APP.state.vars.countTests
        },

    },

    /**
     * Toolbar of Webception
     */
    toolbar: {

        vars: {

            // Elements
            eStatusContainer: '#status_container',
            eMessageContainer: 'h3.message',
            eProgressContainer: '#progress_container .meter',
            eButtonState: '#button_state',
            eButtonAction: '#button_action',

            // All possible States and Content of the buttons
            sButton: 'button small ',

            // All possible Toolbar states
            states : {
                'ready': {
                    message: '<strong>Ready to run </strong> %(tests)s tests.',
                    buttonReadyCss: 'secondary disabled',
                    buttonReadyLabel: 'READY',
                    buttonActionCss: 'success',
                    buttonActionLabel: 'START',
                    bind: 'start',
                    running: false,
                    stopped: false,
                },
                'running': {
                    message: '<strong>Running </strong>%(run)s out of %(tests)s.',
                    buttonReadyCss: '',
                    buttonReadyLabel: 'RUNNING',
                    buttonActionCss: 'alert',
                    buttonActionLabel: 'STOP',
                    bind: 'stop',
                    running: true,
                    stopped: false,
                },
                'stopped': {
                    message: '<strong>Tests stopped</strong> after %(run)s out of %(tests)s.',
                    buttonReadyCss: 'alert',
                    buttonReadyLabel: 'STOPPED',
                    buttonActionCss: 'secondary',
                    buttonActionLabel: 'RESET',
                    bind: 'reset',
                    running: false,
                    stopped: true,
                },
                'failed': {
                    message: '<strong>All tests run.</strong> %(pass)s passed, %(fail)s fails.',
                    buttonReadyCss: 'alert',
                    buttonReadyLabel: 'FAILED',
                    buttonActionCss: 'secondary',
                    buttonActionLabel: 'RESET',
                    bind: 'reset',
                    running: false,
                    stopped: false,
                },
                'passed': {
                    message: '<strong>All tests run.</strong> Everything passed!',
                    buttonReadyCss: 'success',
                    buttonReadyLabel: 'PASSED',
                    buttonActionCss: 'secondary',
                    buttonActionLabel: 'RESET',
                    bind: 'reset',
                    running: false,
                    stopped: false,
                },
            },
        },

        /**
         * Update the progress bar.
         *
         * @param Integer count
         * @param Integer total
         */
        progressBar: function(count, total)
        {
            $(APP.toolbar.vars.eProgressContainer).css('width', ((100/total) * count) +'%');
        },
    },

    /**
     * Test Runner & State Handlers
     */
    test: {

        init: function()
        {
            APP.test.binds();
        },

        vars: {

            // Elements
            eTestsGroup: '.tests_group',
            eTestState : 'span',
            eRunAll: '.all_toggle',
            eRunGroup: '.type_toggle',
            eTestCheckboxes: 'div.test input[type=checkbox]',

            // Basic styling of the state button
            sButton: 'tiny label radius right ',

            // All possible States and Content of the buttons
            states: {
                'ready': {
                    css: 'secondary disabled',
                    label: '<strong>READY</strong>',
                },
                'running': {
                    css: '',
                    label: '<strong>RUNNING</strong>',
                },
                'error': {
                    css: 'alert',
                    label: '<strong>ERROR</strong>',
                },
                'stopped': {
                    css: 'alert',
                    label: '<strong>STOPPED</strong>',
                },
                'failed': {
                    css: 'alert',
                    label: '<strong>FAILED</strong>',
                },
                'passed': {
                    css: 'success',
                    label: '<strong>PASSED</strong>',
                },
            }
        },

        /**
         * Test Runner.
         *
         * @param Array  List of test IDs.
         */
        run: function(tests)
        {
            // If the tests are being run and we still have tests left...
            if (APP.state.isRunning() && tests.length > 0)
            {
                // Remove the first test from the list,
                // and run that test.
                test = tests.shift();

                // Run the test
                $.ajax({
                    url         : test.attr('action'),
                    method      : 'GET',
                    cache       : false,
                    dataType    : "json",
                    beforeSend  : function (xhr, settings)
                    {
                        // Before running the tests,
                        // update the progress bar and set the test state to 'running'
                        APP.toolbar.progressBar(++APP.state.vars.countRun,
                                                  APP.state.vars.countTests);
                        APP.test.refresh(test, 'running');
                        APP.state.refresh('running');
                    },
                    success     : function(data, status, xhr)
                    {
                        // After a successful run, update the test status.
                        APP.test.complete(test, data, xhr);

                        // Carry on running the tests...
                        APP.test.run(tests);
                    }
                });

            } else if (APP.state.isStopped()) {

                APP.state.refresh('stopped');

            } else if (APP.state.isCompleted()) {

                // Re-enable the checkboxes
                APP.test.checkboxToggle();

                // All the tests have been run, so decide how the tests faired.
                (APP.state.vars.countFail > 0) ?
                    APP.state.refresh('failed') : APP.state.refresh('passed');

            }
        },

        /**
         * Set the state of the given test to Running.
         *
         * @param Form Object   test
         * @param String        state
         */
        refresh: function (test, state) {

            // Load the details of the current state
            var chosenState = APP.test.vars.states[state];

            // Update the test
            $(APP.test.vars.eTestState, $(test))
                .attr('class', APP.test.vars.sButton + chosenState.css)
                .html(chosenState.label);
        },

        /**
         * Set the test state after it's been run (whether it's pass or fail)
         */
        complete: function(test, data, xhr)
        {
            var state;

            // Check the state of the completed test and
            // set the UI state and updated counts.
            state = (data.passed == true) ? 'passed' : 'failed';

            // Cheeky one liner to increment the right counter.
            (data.passed == true) ? APP.state.vars.countPass++ : APP.state.vars.countFail++;

            // Append the test log to the console
            APP.console.add(data);

            // Update the single test state
            APP.test.refresh(test, state);

            // Force refresh on the console filter
            APP.console.filter(APP.console.vars.filter);
        },

        /**
         * Find all the Tests
         *
         * @return Array of form objects or FALSE
         */
        find: function()
        {
            var tests = [];

            // For every form on the page, only add it to the test list
            // if the checkbox is checked.
            $("form").each(function() {
                if ($('input[type=checkbox]', $(this)).prop('checked'))
                    tests.push($(this));
            });

            return tests;
        },

        /**
         * Start running the tests.
         */
        start: function() {

            tests = APP.test.find();

            // If there are tests available...
            if (tests.length > 0)
            {
                APP.console.clear();
                APP.test.checkboxToggle();
                APP.state.refresh('running');
                APP.test.run(tests);
            } else {
                APP.state.showError('Please select some tests to run.');
            }
        },

        /**
         * Stop the tests from running.
         */
        stop: function() {

            // Re-enable the checkboxes
            APP.test.checkboxToggle();

            // And set the application state to Stopped.
            APP.state.refresh('stopped');
        },

        /**
         * Reset the Application state to ready.
         */
        reset: function() {

            // Reset all the counters
            APP.state.vars.countRun  = 0;
            APP.state.vars.countPass = 0;
            APP.state.vars.countFail = 0;

            // Clear the progress bar
            APP.toolbar.progressBar(0, APP.state.vars.countTests);

            // Reset all the tests
            $("form").each(function() {
                APP.test.refresh($(this), 'ready');
            });

            // And finally refresh the state
            APP.state.refresh('ready');
        },

        /**
         * Binds for the Test
         */
        binds: function()
        {
            // INCLUDE ALL - toggle the group on/off when clicked.
            $(APP.test.vars.eRunAll).on('click', function(e)
            {
                $ischecked = $(this).is(":checked");

                $(APP.test.vars.eRunAll).prop('checked', $ischecked);
                $(APP.test.vars.eRunGroup).prop('checked', $ischecked);
                $(APP.test.vars.eTestCheckboxes).prop('checked', $ischecked);

                APP.state.updateCount();
                APP.state.refresh(APP.state.vars.currentState);
            });

            // Group Checkboxes - Unchecking a checkbox removes it from the list
            // and also unchecks the 'Run all' checkbox.
            $(APP.test.vars.eRunGroup).on('click', function(e)
            {
                $ischecked = $(this).is(":checked");

                $(APP.test.vars.eTestCheckboxes, $(this).closest(APP.test.vars.eTestsGroup))
                    .prop('checked', $ischecked);

                APP.test.checkboxToggleAll();
                APP.state.updateCount();
                APP.state.refresh(APP.state.vars.currentState);
            });

            // Test Checkboxes - Unchecking a checkbox removes it from the list
            // and also unchecks the 'Include All' checkbox.
            $(APP.test.vars.eTestCheckboxes).on('click', function(e)
            {
                APP.test.checkboxToggleGroup(this);
                APP.test.checkboxToggleAll();
                APP.state.updateCount();
                APP.state.refresh(APP.state.vars.currentState);
            });
        },

        /**
         * Based on the count of how many tests are checked,
         * the UI toggles the 'Include All' checkboxes.
         */
        checkboxToggleAll: function()
        {
            // Count all of the available tests
            var all_checked   = $('form input[type=checkbox]:checked').length;
            var all_count     = $('form input[type=checkbox]').length;

            // If all the tests are checked, check the 'Include All' checkbox
            $(APP.test.vars.eRunAll).prop('checked', (all_checked == all_count));
        },

        /**
         * Based on the count of how many tests are checked in a group,
         * the UI toggles the 'Include (type) Tests' and the 'Include All' checkboxes.
         *
         * @param  element group Checkbox that handled the click-event.
         */
        checkboxToggleGroup: function(group)
        {
            // In the current test group, find how many are available & how many are checked
            var $group        = $(group).closest(APP.test.vars.eTestsGroup);
            var group_count   = $(APP.test.vars.eTestCheckboxes, $group).length;
            var group_checked = $(APP.test.vars.eTestCheckboxes +':checked', $group).length;

            // If all the tests in the group are checked, check the 'Include (type) Tests' checkbox
            $(APP.test.vars.eRunGroup, $group).prop('checked', (group_count == group_checked));
        },

        /**
         * Checkboxes are enabled by default.
         *
         * On start, they're disabled.
         * On reset, they're enabled.
         */
        checkboxToggle: function()
        {
            $('input:checkbox').toggleDisabled();
        },
    },

    /**
     * Console Functions & Binds
     */
    console: {

        init: function() {

            // Ready the Console filter binds
            APP.console.binds();

        },

        vars: {
            eFilter: '.console_filter',
            eConsoleContainer: '#console_container',
            sButtonDefault: 'console_filter radius',
            filter: 'all',
        },

        /**
         * Binds for the Console
         */
        binds: function() {

            // Console Filters
            $(APP.console.vars.eFilter).on('click', function(e)
            {
                e.preventDefault();

                var filter = $(this);

                // Clear out the other filter styles
                $.each( $(APP.console.vars.eFilter), function( index, filt ){
                    $(filt).addClass('secondary').removeClass($(filt).attr('data-css'));
                });

                // Add the class to the button to imply it's active
                APP.console.vars.filter = filter.attr('id');
                APP.console.filter(filter.attr('id'));
                filter.removeClass('secondary').addClass(filter.attr('data-css'));

            });

            // Add high-light colour to console filter on hover.
            $(APP.console.vars.eFilter).on({
                mouseenter: function() {
                    $(this).removeClass('secondary').addClass($(this).attr('data-css'));
                },
                mouseleave: function() {
                    // Only remove the hover state if the current state is not the same.
                    if ($(this).attr('id') != APP.console.vars.filter)
                        $(this).addClass('secondary').removeClass($(this).attr('data-css'));
                }
            });
        },

        /**
         * Filter the tests in the console type the filter
         *
         * @param  string filter 'All|Passed|Failed'
         */
        filter: function(filter) {
            $(APP.console.vars.eConsoleContainer +' > div').hide();
            $(APP.console.vars.eConsoleContainer +' .'+ filter).show();
        },

        /**
         * Given the result of a running a test, append the console output.
         *
         * @param object Test
         */
        add: function(test)
        {
            var consoleContainer = $(APP.console.vars.eConsoleContainer);
            consoleContainer.show();

            // Load the details of the current state
            var chosenState = APP.test.vars.states[test.state];

            var testResult = $('<div/>', {'class': test.state +' all'});
            var state = $('<span/>', {'class': APP.test.vars.sButton + chosenState.css,
                            }).html(chosenState.label).appendTo(testResult);

            $('<h5/>', {'text': test.title}).appendTo(testResult);
            $('<pre/>', {'text': test.log}).appendTo(testResult);
            $('<hr>').appendTo(testResult);

            testResult.appendTo(consoleContainer);

            // Move down to the bottom of the container
            consoleContainer[0].scrollTop = consoleContainer[0].scrollHeight;
        },

        /**
         * Empty the content of the console
         */
        clear: function() {
            $(APP.console.vars.eConsoleContainer).html('');
        },
    },

    /**
     * If Test mode is set (via query string), it's pass forward
     * into any AJAX calls.
     */
    mode: {

        data: function()
        {
            if (location.search.indexOf('test=') >= 0)
                return {'test' : APP.mode.getParameterByName('test') };

            return {};
        },

        getParameterByName: function(name)
        {
            name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
            var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(location.search);
            return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
        }
    }
};

(function($) {
    $.fn.toggleDisabled = function(){ return this.each(function(){ this.disabled = !this.disabled; });}
})(jQuery);

; (function ( context, $ ) {
    "use strict";
    APP.init();
})(this, jQuery, window);