import './styles/app.scss';
import $ from 'jquery';
import 'jquery-migrate';
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

