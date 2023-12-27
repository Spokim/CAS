const ElementLoad = (id) => {
    var element = $(id);
    element.prop("disabled", true);
    element.html(
        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
    );
};

const ElementLoadReset = (id, text) => {
    var element = $(id);
    element.prop("disabled", false);
    element.html(text);
};
