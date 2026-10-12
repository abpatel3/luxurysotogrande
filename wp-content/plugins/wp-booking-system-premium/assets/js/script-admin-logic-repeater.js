jQuery(function ($) {
    /**
     * Handles the displaying of the correct comparison dropdown
     * 
     */
    $(".wpbs-logic-rules").on('change', '.logic-rule-condition', function () {
        $field = $(this);
        $(this).parents('.wpbs-logic-rule').removeClass().addClass('wpbs-logic-rule wpbs-logic-rule-' + $field.val());
        $comparison_rules_select = $(this).parents('.wpbs-logic-rule').find('.logic-rule-comparison');
        var comparison_rules = $field.find('option:selected').data('comparison-rules');

        $comparison_rules_select.empty();
        $.each(comparison_rules, function (key, value) {
            $comparison_rules_select.append('<option value="' + key + '">' + value + '</option>');
        });

    })

    /**
     * If the page is empty, add the first group
     * 
     */
    if (!$(".wpbs-logic-rules .wpbs-logic-rule").length) {
        wpbs_add_logic_group();

        wpbs_logic_rule_datepickers();
        wpbs_unsaved_changes = false;
    }

    /**
     * Add logic rule click event
     * 
     */
    $(".wpbs-logic-rules").on('click', '.logic-rule-add-and', function (e) {
        e.preventDefault();
        var group_index = $(this).parents('.wpbs-logic-rule-group').index();
        var rule_index = $(this).parents('.wpbs-logic-rule').index();

        wpbs_add_logic_rule(group_index, rule_index);

        wpbs_logic_rule_datepickers();
    });

    /**
     * Add logic group click event
     */
    $(".wpbs-logic-rules").on('click', '.logic-rule-add-or', function (e) {
        e.preventDefault();
        wpbs_add_logic_group();

        wpbs_logic_rule_datepickers();
    });

    /**
     * Add logic group click event
     */
    $(".wpbs-logic-rules").on('click', '.wpbs-logic-rule-remove', function (e) {
        e.preventDefault();
        $rule = $(this).parents('.wpbs-logic-rule');


        // Remove group if we are deleting the last rule
        if ($rule.parents('.wpbs-logic-rule-group').find('.wpbs-logic-rule').length == 1) {
            $rule.parents('.wpbs-logic-rule-group').remove();
            wpbs_logic_regenerate_group_index();
        }

        // Remove rule
        $rule.remove();

    });

    /**
     * Add logic rule
     * 
     */
    function wpbs_add_logic_rule(group_index, rule_index) {
        var logic_rule_html = $('<div />');
        logic_rule_html.html($("#wpbs-logic-rule-template").html());

        logic_rule_html.find('[data-name]').each(function () {
            $(this).attr('name', $(this).data('name').replace('index', group_index));
        });

        if ($(".wpbs-logic-rule-group").eq(group_index).find('.wpbs-logic-rule-group-inner .wpbs-logic-rule').length) {
            $(logic_rule_html.html()).insertAfter($(".wpbs-logic-rule-group").eq(group_index).find('.wpbs-logic-rule-group-inner .wpbs-logic-rule').eq(rule_index))
            $(".wpbs-logic-rule-group").eq(group_index).find('.wpbs-logic-rule').eq(rule_index + 1).find('.logic-rule-condition').trigger('change');
        } else {
            $(".wpbs-logic-rule-group").eq(group_index).find('.wpbs-logic-rule-group-inner').append(logic_rule_html.html());
            $(".wpbs-logic-rule-group").eq(group_index).find('.logic-rule-condition').trigger('change');
        }

    }

    /**
     * Add logic group
     * 
     */
    function wpbs_add_logic_group() {
        var group_index = $(".wpbs-logic-rule-group").length
        $('<div class="wpbs-logic-rule-group" data-index="' + group_index + '"><div class="wpbs-logic-rule-group-inner"></div><div class="logic-rule-group-separator"><p>or</p></div></div>').insertBefore($(".logic-rule-group-add-group"));
        wpbs_add_logic_rule(group_index, 0);
    }

    /**
     * Regenerate Group Indexes
     * 
     */
    function wpbs_logic_regenerate_group_index() {
        $(".wpbs-logic-rule-group").each(function () {
            var $group = $(this);
            var index = $group.index();
            $group.data('index', index);

            $group.find('[data-name]').each(function () {
                $(this).attr('name', $(this).data('name').replace('index', index));
            });
        })
    }

    /**
     * logic rule datepickers
     * 
     */
    function wpbs_logic_rule_datepickers() {

        $('.wpbs-settings-field-logic-conditions .logic-rule-datepicker').datepicker('destroy');
        $('.wpbs-settings-field-logic-conditions .logic-rule-datepicker').datepicker({
            maxDate: "+10Y",
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            constrainInput: false,
            showOtherMonths: true,
            selectOtherMonths: true,
            beforeShow: function () {
                $('#ui-datepicker-div').addClass('wpbs-datepicker');
            },
            onClose: function () {
                $('#ui-datepicker-div').hide().removeClass('wpbs-datepicker');
            },
        }).keyup(function (e) {
            if (e.keyCode == 8 || e.keyCode == 46) {
                $.datepicker._clearDate(this);
            }
        });
    }

    wpbs_logic_rule_datepickers();
});