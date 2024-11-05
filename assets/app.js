import './styles/app.scss'; 
import $ from 'jquery';
import 'jquery-migrate/dist/jquery-migrate.js';
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

// Import DataTables styles
import 'datatables.net-bs5';

// Set up jQuery globally
window.$ = $;
window.jQuery = $;

// Set up bootstrap
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

// Initialize Chosen plugin after jQuery is set up
$(function () {
    $('.chosen').chosen();
});