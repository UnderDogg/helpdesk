$(function ()
{
    // Markdown
    $("textarea[data-provide='markdown']").markdown();

    // Select2
    $(".select-labels").select2({
        formatResult: formatLabel,
        formatSelection: formatLabel
    });

    /**
     * Formats a select2 label.
     *
     * @param label
     *
     * @returns {*|jQuery}
     */
    function formatLabel(label)
    {
        return $(label.element).data('display');
    }

    // Form Confirmation window
    $('.form-confirm').on('submit', function(e)
    {
        e.preventDefault();

        var self = this;

        var title = $(self).data('title');
        var text = $(self).data('message');

        swal({
            title: (title ? title : "Are you sure?"),
            text: text,
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            cancelButtonText: "Cancel",
            closeOnConfirm: false,
            closeOnCancel: true
        }, function(isConfirm) {
            if (isConfirm) {
                return self.submit();
            }
        });
    });

    // Delete link confirmation window
    $('[data-post]').on('click', function(e)
    {
        e.preventDefault();

        var self = this;

        var title = $(self).data('title');
        var text = $(self).data('message');
        var url = $(self).attr('href');
        var method = $(self).data('post');
        var token = $("meta[name='csrf-token']").attr('content');

        var form = $("<form></form>");

        form.attr('method', 'POST');
        form.attr('action', url);

        form.append('<input name="_method" type="hidden" value="'+method+'" />');
        form.append('<input name="_token" type="hidden" value="'+token+'" />');

        swal({
            title: (title ? title : "Are you sure?"),
            text: text,
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            cancelButtonText: "Cancel",
            closeOnConfirm: false,
            closeOnCancel: true
        }, function(isConfirm) {
            if (isConfirm) {
                $('body').append(form);

                return form.submit();
            }
        });
    });

});
