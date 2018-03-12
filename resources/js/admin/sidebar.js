
const jquery = require('jquery');

const getStorage = function (key) {
    const st = window.localStorage.getItem(key);

    console.log('storage: ', key, st);
    if (_.isNil(st)) {
        return {
            "menu": [],
        };
    }

    return JSON.parse(st);
};

const saveStorage = function (key, obj) {
    console.log('save: ', key, obj);
    window.localStorage.setItem(key, JSON.stringify(obj));
};

const addItem = function (obj, item) {

    if (_.isNil(obj)) {
        obj = [];
    }

    const index = _.indexOf(obj, item);
    if (index === -1) {
        obj.push(item);
    }

    return obj;
};

const removeItem = function (obj, item) {

    if (_.isNil(obj)) {
        obj = [];
    }

    const index = _.indexOf(obj, item);
    if (index !== -1) {
        console.log("removing", item);
        obj.splice(index, 1);
    }

    return obj;
};

jquery(document).ready(function () {

    $(".select2").select2();

    let storage = getStorage("phpvms.admin");

    // see what menu items should be open
    for (let idx = 0; idx < storage.menu.length; idx++) {
        const id = storage.menu[idx];
        const elem = jquery(".collapse#" + id);
        elem.addClass("in").trigger("show.bs.collapse");

        const caret = jquery("a." + id + " b");
        caret.addClass("pe-7s-angle-down");
        caret.removeClass("pe-7s-angle-right");
    }

    jquery(".collapse").on("hide.bs.collapse", function () {
        console.log('hiding');
        const id = jquery(this).attr('id');
        const elem = jquery("a." + id + " b");
        elem.removeClass("pe-7s-angle-down");
        elem.addClass("pe-7s-angle-right");

        removeItem(storage.menu, id);
        saveStorage("phpvms.admin", storage);
    });

    jquery(".collapse").on("show.bs.collapse", function () {
        console.log('showing');
        const id = jquery(this).attr('id');
        const caret = jquery("a." + id + " b");
        caret.addClass("pe-7s-angle-down");
        caret.removeClass("pe-7s-angle-right");

        addItem(storage.menu, id);
        saveStorage("phpvms.admin", storage);
    });

});
