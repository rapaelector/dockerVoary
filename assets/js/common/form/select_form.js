/**
 * Make input type with data-type="select" to a select get its data-value to make options value
 * If the other option is choosen return the select to a simple input
 */
function selectBuilder() {
    var $field = $(this);
    $field.hide();
    var data = $field.data('choices');
    var selectId = $field.attr('id') + '_select';
    var $select = $('<select class="form-control" id="' + selectId + '"></select>');
    var currentVal = $field.val();
    var found = false;
    for (var i = 0; i < data.length; i++) {
        var value = data[i];
        $select.append('<option value="' + getOptionValue(value) + '">' + getOptionLabel(value) + '</option>');
        if (currentVal == getOptionValue(value) && !found) {
            found = true;
        }
    }

    $field.parent().append($select);

    $select.on('change', function () {
        $field.val($(this).val());
    });

    if (!found && currentVal != '') {
        var $option = $('<option value="' + currentVal + '">' + getOptionLabel(currentVal) + '</option>');
        // $select.find('option:last-child').before($option);
    }
    $select.val(currentVal);

    var selectOptionValueEditor = new SelectOptionValueEditor();
    selectOptionValueEditor.init({selector: '#' + selectId});

    $select.on('select_editor.change', function (e, value) {
        var val = value.replace('%', '');
        $field.val(val);
    });
}

function getOptionValue(value) {
    var values = value.toString().split('.');
    return values[1] == undefined ? values[0] + '.0' : value;
}

function getOptionLabel(value) {
    if (value != -1) return value + ' %';
    return 'Autre';
}

function SelectOptionValueEditor () {
    var Editor = this;
    var _this;

    Editor.defaults = {
        editables: ['-1', '-1.0'],
        selector: 'select',
        input_attr: {
            'class': 'form-control',
            type: 'number',
            placeholder: 'value',
            required: 'required,'
        },
        formater: function (value) {
            return value + ' %';
        },
    };

    Editor.initElements = function () {
        _this.$elems = {};
        _this.$elems.fields = $(_this.param.selector);
    }

    Editor.bindElements = function () {
        _this.$elems.fields.on('change', fieldChangeHandler);
    }

    function buildAttr (attrs) {
        var attributes = [];
        if (attrs) {
            for (var attr in attrs) {
                if (attr) {
                    attributes.push(attr + '="' + attrs[attr] + '"');
                }
            }
        }

        return attributes.join(' ');
    }

    function fieldChangeHandler (e) {
        var $field = $(this);
        var val = $field.val();
        var $input = $('<input ' + buildAttr(_this.param.input_attr) + ' />');
        var $parent = $field.parent();
        var $option = $field.find(':selected');
        var otherText = $option.text();
        if (_this.param.editables.indexOf(val) != -1) {
            $parent.append($input);
            $input.focus();
            $field.hide();
            $input.keyup(function () {
                var value = $(this).val();
                if (_this.param.formater != false)
                    value = _this.param.formater(value);
                $option.attr('value', value).text(value);
                $field.trigger('change');
                $field.trigger('select_editor.change', [value]);
            });
            $input.blur(function () {
                if (_this.param.editables.indexOf($option.attr('value')) == -1) {
                    $(this).remove();
                    $field.show();
                    $field.append('<option value="-1">' + otherText + '</option>');
                    $field.trigger('select_editor.change', [$option.attr('value')]);
                }
                var values = [];
                $field.find('option').each(function () {
                    var oval = $(this).val();
                    if (values.indexOf(oval) == -1) {
                        values.push(oval);
                    } else {
                        $(this).remove();
                        $field.val(oval);
                    }
                });
            });
        }
    }

    function checkOption($select, value) {
        var exist = false;
        $select.find('option').each (function () {
            if ($(this).attr('value') == value) {
                return true;
            }
        });
        return exist;
    }

    Editor.init = function (options) {
        _this = this;
        _this.param = $.extend(true, _this.defaults, options);
        _this.initElements();
        _this.bindElements();
    }
};

export {
    selectBuilder,
    SelectOptionValueEditor,
};