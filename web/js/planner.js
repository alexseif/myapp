/**
 * The following content was designed & implemented under AlexSeif.com
 **/


let planner = {
    init: function () {
        this.bindEvents();
        this.bindDraggable();
    },
    bindEvents: function () {
        $('.add-item').click(this.addItem);
        $('.add-connection').click(this.addConnection);
    },
    bindDraggable: function () {
        _self = this;
// target elements with the "draggable" class
        interact('.draggable')
            .draggable({
                // enable inertial throwing
                inertia: true,
                // keep the element within the area of it's parent
                modifiers: [
                    interact.modifiers.restrictRect({
                        restriction: 'parent',
                        endOnly: true
                    })
                ],
                // enable autoScroll
                autoScroll: true,

                listeners: {
                    // call this function on every dragmove event
                    move: _self.dragMoveListener,

                    // call this function on every dragend event
                    end(event) {
                    }
                }
            });
    },
    addItem: function () {
        $($($(this).data('template')).html())
            .clone()
            .appendTo('#canvas');
        $('.edit-me').click(planner.editItem);
        $('.save-me').click(planner.saveItem);
        $('.item').connections();
    },
    editItem: function () {
        let editingItem = $(this).parents('.item');
        editingItem.children(':not(.tools)').each(function () {
            $(this).data('html', $(this).html());
            $(this).html($('<input name="' + $(this).attr('class') + '" value="' + $(this).text() + '"/>'));
        });
    },
    saveItem: function () {
        let savedItem = $(this).parents('.item');
        savedItem.find('input').each(function () {
            $(this).parent().text($(this).val());
        });
    },
    addConnection: function () {
    // add connection code
    },
    dragMoveListener: function (event) {
        var target = event.target;
        // keep the dragged position in the data-x/data-y attributes
        var x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx;
        var y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;

        // translate the element
        target.style.transform = 'translate(' + x + 'px, ' + y + 'px)';

        // update the posiion attributes
        target.setAttribute('data-x', x);
        target.setAttribute('data-y', y);
        $('.item').connections('update');

    },
};

planner.init();


// this function is used later in the resizing and gesture demos
window.dragMoveListener = planner.dragMoveListener;

