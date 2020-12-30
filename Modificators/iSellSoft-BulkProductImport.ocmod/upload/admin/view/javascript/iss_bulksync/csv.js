/*global Setup*/
Setup.selectorForm = {
    init: function () {
        var fquery = "#iss_selector_form";
        $(fquery + " input," + fquery + " textarea," + fquery + " select").on('change', function () {
            Setup.selectorForm.save();
        });
        $('#form-parse select').change(function(){
            Setup.selectorForm.load();
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
                if( input_field.indexOf('attribute') > -1 && input_field.indexOf('attribute_group') < 0){
                    var label=$(`input[name=${input_field}-label]`).val();
                    var attribute_name=label.split('|')[0]||'';
                    var attribute_group_name=label.split('|')[1]||'';
                    config.sync_config.attributes.push({
                        field:input_field,
                        name:attribute_name,
                        group_description:attribute_group_name
                    });
                }
                if( input_field.indexOf('attribute_group') > -1 ){
                    var label=$(`input[name=${input_field}-label]`).val();
                    var attribute_array = [];
                    if(label.indexOf(',') == -1){
                        attribute_array.push(label);
                    }else{
                        attribute_array = label.split(',');
                    }
                    for(var i = 0; i < attribute_array.length; i++){
                        var current_attribute = attribute_array[i];
                        var attribute_name=current_attribute.split('|')[0]||'';
                        if(attribute_name == ''){
                            continue;
                        }
                        var attribute_group_name=current_attribute.split('|')[1]||'';
                        config.sync_config.attributes.push({
                            field:input_field,
                            name:attribute_name,
                            group_description:attribute_group_name,
                            index: i
                        });
                    }
                }
                if( input_field.indexOf('option') > -1 ){
                    var label=$(`input[name=${input_field}-label]`).val();
                    var option_name=label.split('|')[0]||'';
                    config.sync_config.options.push({
                        field:input_field,
                        name:option_name,
                        value_group_field:'option_group1',
                        price_group_field:'price_group1',
                        price_base_field:'price',
                        option_type:$(`select[name=${input_field}-selectortype]`).val()
                    });
                }
            } else
            if( input_type==='infilter'){
                var label=$(`input[name=${input_field}-label]`).val() || $input.parent().parent().find('label').html().replace(':','');
                var filter_name=label.split('|')[0]||'';
                config.sync_config.filters.push({
                    field:input_field,
                    name:filter_name,
                    group_description:filter_name,
                    delimeter:delimeter
                });
            } else
            if( input_type==='group_infilter' ){
                var label = $(`input[name=${input_field}-label]`).val();
                var attribute_array = [];
                if(label.indexOf(',') == -1){
                    attribute_array.push(label);
                }else{
                    attribute_array = label.split(',');
                }
                for(var i = 0; i < attribute_array.length; i++){
                    var current_attribute = attribute_array[i];
                    var filter_name=current_attribute.split('|')[0]||'';
                    if(filter_name == ''){
                        continue;
                    }
                    config.sync_config.filters.push({
                        field:input_field,
                        name:filter_name,
                        group_description:filter_name,
                        delimeter:delimeter,
                        index: i
                    });
                }
            } else
            if( input_type==='inattribute' ){
                var label=$(`input[name=${input_field}-label]`).val() || $input.parent().parent().find('label').html().replace(':','');
                var attribute_name=label.split('|')[0]||'';
                var attribute_group_name=label.split('|')[1]||'';
                config.sync_config.attributes.push({
                    field:input_field,
                    name:attribute_name,
                    group_description:attribute_group_name
                });
                
            } else
            if ( input_type==='source' ) {
                config.sync_config.sources[input_field] = input_value;
            }
            else {
                if(input_value.indexOf('\\') > -1){
                    input_value = input_value.replace(/\\/g, "\\\\");
                }
                config.sync_config[input_name] = input_value;
            }
        });
        if( !valid ){
            return false;
        }
        config.sync_name=config.sync_config.sync_name;
        Setup.selectorForm.config=config;
        
        var url = `./?route=extension/module/iss_bulksync_setup/syncConfigSave&`+ Setup.token_name +`=${Setup.user_token}`;
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
        var fvalue = sync_config;
        
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
            var label=attribute.group_description?`${attribute.name}|${attribute.group_description}`:attribute.name;
            
            if(attribute.field == 'attribute_group'){
                if(!fvalue[`${attribute.field}-label`]){
                    fvalue[`${attribute.field}-label`] = '';
                }
                fvalue[`${attribute.field}-label`] += label+',';
            } else {
                fvalue[`${attribute.field}-label`] = label;
            }
            fvalue[`${attribute.field}-inattribute`] = 1;
            fvalue[`${attribute.field}-infilter`] = is_filter(attribute.field);
            fvalue[`${attribute.field}-group_infilter`] = is_filter(attribute.field);
        }
        for (var option of sync_config.options) {
            var label=option.group_description?`${option.name}|${option.group_description}`:option.name;
            fvalue[`${option.field}-label`] = label;
            fvalue[`${option.field}-infilter`] = is_filter(option.field);
            fvalue[`${option.field}-selectortype`] = option.option_type;
        }
        $(fquery + " input," + fquery + " textarea," + fquery + " select").each(function (i, element) {
            var value = fvalue[element.name];
            $(element).val(value);
            $(element).css('background-color', '');
            if (value) {
                $(element).css('background-color', '#ffc');
            }
            if ($(element).attr('type') === 'checkbox') {
                if( fvalue[element.name] * 1) {
                    $(element).attr('checked', 'checked');
                } else {
                    $(element).removeAttr('checked');
                }
            }
        });
    },
    load: function () {
        if (!Setup.sync_id) {
            return;
        }
        var request = {
            route: 'extension/module/iss_bulksync_setup/syncConfigGet',
            sync_id: Setup.sync_id
        };
        request[Setup.token_name] = Setup.user_token;
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