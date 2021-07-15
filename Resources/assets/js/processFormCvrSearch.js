$(function () {
    const $cvrInputElement = $('#process_company_cvr');
    const $searchCvrButton = $('#process_company_search');

    $cvrInputElement.change(function () {
        if ($('#cvr-search-results').length) {
            $('#cvr-search-results').hide();
            $searchCvrButton.text($searchCvrButton.data('search-text'));
            $searchCvrButton.show();
        }
    });

    $searchCvrButton.click(function () {
        $searchCvrButton.text($searchCvrButton.data('loading-text'));

        $.ajax({
            url: $searchCvrButton.data('search-action'),
            data: { 'cvr': $cvrInputElement.val() },
            success: function (html) {
                if ($('#cvr-search-results').length) {
                    $searchCvrButton.hide();
                    $('#cvr-search-results').replaceWith(html);
                    $('#cvr-search-results').show();
                } else {
                    $searchCvrButton.hide();
                    $searchCvrButton.after(html);
                }
            }
        });
    });
});
