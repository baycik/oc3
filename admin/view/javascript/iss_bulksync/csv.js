/*global Setup*/
Setup.selectorForm = {
    init: function () {
        var fquery = "#iss_selector_form";
        $(fquery + " input," + fquery + " textarea," + fquery + " select").on('change', function () {
            Setup.selectorForm.save();
        });
        Setup.selectorForm.load();
    },
    get_empty_config:function(){
        var $option=$('#form-parse select').find('option[value=' + $('#form-parse select').val() + ']');
        var sync_name = $option.data('sync_name');
        return {
            sync_name: sync_name,
            sync_config: {
                image_handling:'use',
                parsing_mode:'detect_unchanged_entries',
                attributes: [],
                filters: [],
                options: [],
                sources: {}
            }
        };
    },
    save: function () {
        var config = Setup.selectorForm.get_empty_config();
        var fquery = "#iss_selector_form";
        var delimeter=$(`${fquery} input[name=filter_delimeter]`).val().split('');
        var valid=true;
        $(fquery + " input," + fquery + " textarea," + fquery + " select").each(function( i, node){
            var $input=$(node);
            var input_name = $input.attr('name');
            var input_type = input_name.split('-')[1] || null;
            var input_field = input_name.split('-')[0];
            var input_value = $input.val();
            if ($input.attr('type') === 'checkbox') {
                input_value = $input.is(':checked') ? 1 : 0;
            }
            if (input_value) {
                $input.css('background-color', '#ffc');
                $input.css('border-color', '');
            } else {
                $input.css('background-color', '');
                if( $input.attr('required') ){
                    $input.css('background-color', '#fcc');
                    $input.css('border-color', '#f00');
                    valid=false;
                    $input.get(0).focus();
                }
                return true;
            }
            
            if ( input_type==='label' ) {
                if( input_field.indexOf('attribute') > -1 ){
                    config.sync_config.attributes.push({
                        field:input_field,
                        name:$(`input[name=${input_field}-label]`).val(),
                        group_description:''
                    });
                }
                if( input_field.indexOf('option') > -1 ){
                    config.sync_config.options.push({
                        field:input_field,
                        name:$(`input[name=${input_field}-label]`).val(),
                        group_description:''
                    });
                }
            } else
            if( input_type==='infilter' ){
                var label=$(`input[name=${input_field}-label]`).val() || $input.parent().parent().find('label').html().replace(':','');
                config.sync_config.filters.push({
                    field:input_field,
                    name:label,
                    delimeter:delimeter
                });
            } else
            if( input_type==='inattribute' ){
                var label=$(`input[name=${input_field}-label]`).val() || $input.parent().parent().find('label').html().replace(':','');
                config.sync_config.attributes.push({
                    field:input_field,
                    name:label,
                    group_description:''
                });
            } else
            if ( input_type==='source' ) {
                config.sync_config.sources[input_field] = input_value;
            }
            else {
                config.sync_config[input_name] = input_value;
            }
            
            
            
        });
        if( !valid ){
            return false;
        }
        config.sync_name=config.sync_config.sync_name;
        Setup.selectorForm.config=config;
        
        console.log(config.sync_config);
        var url = `./?route=extension/module/iss_bulksync_setup/syncConfigSave&user_token=${Setup.user_token}`;
        var request = {
            sync_id: Setup.sync_id,
            config: JSON.stringify(Setup.selectorForm.config)
        };
        $.post(url, request, function (ok) {
            if (!ok * 1) {
                Setup.selectorForm.load();
            }
        });
        return true;
    },
    set: function (config) {
        Setup.selectorForm.config = config;
        if ( !Setup.selectorForm.config || !Setup.selectorForm.config.sync_config ) {
            Setup.selectorForm.config = Setup.selectorForm.get_empty_config();
        }
        var sync_config = Setup.selectorForm.config.sync_config;
        var fquery = "#iss_selector_form";
        var fvalue = {};
        
        function is_filter(field) {
            for ( var filter of sync_config.filters ) {
                if ( filter.field === field ){
                    return true;
                }
            }
            return false;
        }
        for (var target in sync_config.sources) {
            var source = sync_config.sources[target];
            fvalue[`${target}-source`] = source;
        }
        for (var attribute of sync_config.attributes) {
            fvalue[`${attribute.field}-label`] = attribute.name || '';
            fvalue[`${attribute.field}-infilter`] = is_filter(attribute.field);
        }
        for (var option of sync_config.options) {
            fvalue[`${option.field}-label`] = option.name || '';
            fvalue[`${option.field}-infilter`] = is_filter(option.field);
        }
        fvalue['image_handling']=sync_config.image_handling;
        fvalue['parsing_mode']=sync_config.parsing_mode;
        fvalue['sync_name']=Setup.selectorForm.config.sync_name;
        fvalue['source_language']=sync_config.source_language;
        $(fquery + " input," + fquery + " textarea," + fquery + " select").each(function (i, element) {
            var value = fvalue[element.name];
            $(element).val(value);
            if (value) {
                $(element).css('background-color', '#ffc');
            }
            if ($(element).attr('type') === 'checkbox' && fvalue[element.name] * 1) {
                $(element).attr('checked', 'checked');
            }
        });
    },
    load: function () {
        if (!Setup.sync_id) {
            return;
        }
        var request = {
            route: 'extension/module/iss_bulksync_setup/syncConfigGet',
            sync_id: Setup.sync_id,
            user_token: Setup.user_token
        };
        $.get("./", request, function (resp) {
            try {
                var config = JSON.parse(resp);
            } catch (e) {
                console.log(e);
            }
            Setup.selectorForm.set(config);
        });
    }
};
$(document).on('ready', Setup.selectorForm.init);