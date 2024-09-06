/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';

// start the Stimulus application
// import './bootstrap';
// Import JavaScript files
// import Modernizr from 'modernizr'

import $ from 'jquery';
import 'jquery-migrate';
// require('webpack-jquery-ui');
// require('webpack-jquery-ui/css');
// import 'jquery-ui/ui/widgets/sortable'; // Import the sortable widget
import 'jquery-ui-sortable';
import 'popper.js';
import {
    Alert,
    Button,
    Carousel,
    Collapse,
    Dropdown,
    Modal,
    Offcanvas,
    Popover,
    ScrollSpy,
    Tab,
    Toast,
    Tooltip
} from 'bootstrap';
import 'bootstrap-notify';
import 'chosen-js';
import './js/common';
import './js/tasks';
import './js/dashboard';

window.$ = $;
window.jQuery = $;

window.bootstrap = {
    Alert,
    Button,
    Carousel,
    Collapse,
    Dropdown,
    Modal,
    Offcanvas,
    Popover,
    ScrollSpy,
    Tab,
    Toast,
    Tooltip
};
