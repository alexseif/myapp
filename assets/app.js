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

import $ from 'jquery';
import 'jquery-migrate';
window.$ = $;
window.jQuery = $;
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
import moment from 'moment';
// import 'modernizr';
// import 'jquery-ui';
import 'bootstrap-notify';
import 'chosen-js';
import './js/common';
import './js/tasks';