/**
 * Keep location-dependent filters in sync.
 *
 * Mark the location <select> with data-location-filter="1".
 * Mark dependents with data-depends-on-location="customers|suppliers|contacts|users|cashiers".
 */
(function ($) {
    function fillSelect($sel, items) {
        if (!$sel.length) {
            return;
        }

        var current = $sel.val();
        $sel.find('option').remove();
        $.each(items, function (id, name) {
            $sel.append($('<option></option>').attr('value', id).text(name));
        });

        if (current && $sel.find('option[value="' + current + '"]').length) {
            $sel.val(current);
        } else {
            $sel.val('');
        }

        $sel.trigger('change.select2');
    }

    function scopeFor($el) {
        var $section = $el.closest('.search-section, .filters, form, .box, .content');
        return $section.length ? $section : $(document);
    }

    function syncDependents($location) {
        var locationId = $location.val();
        var $scope = scopeFor($location);
        var $dependents = $scope.find('[data-depends-on-location]');

        if (!$dependents.length) {
            return;
        }

        $dependents.each(function () {
            $(this).val('').trigger('change.select2');
        });

        var url = (typeof base_path !== 'undefined' ? base_path : '') + '/filters/location-options';
        $.getJSON(url, { location_id: locationId })
            .done(function (data) {
                $dependents.each(function () {
                    var key = $(this).attr('data-depends-on-location');
                    if (data[key]) {
                        fillSelect($(this), data[key]);
                    }
                });
            });
    }

    $(document).on('change', 'select[data-location-filter]', function () {
        syncDependents($(this));
    });
})(jQuery);
