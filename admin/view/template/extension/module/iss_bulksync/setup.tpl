<style>
    #form-parse .form-control {
        background-color: white;
        width: 100%;
    }

    #form-parse .but.col{
        padding-left: 24%;
    }

    #form-parse .btn.btn-primary {
        margin-left: auto;
        margin-right: auto; 
        width: 70%;
    }

    .btn-upload-positive{
        width: 50%;
    } 

    .progress-container .progress{
        height: 15px;
    } 
    #content {
        min-height: 600px;
    }

</style>
<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">

            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb){ ?>
                    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?> 
            </ul>
        </div>
    </div>
    <div class="successdiv">
        <?php if(isset($success)){ ?>
            <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?></div>
        <?php } ?>
    </div>
    <div class="errordiv">
        <?php if(isset($error_warning)){ ?>
            <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?></div>
        <?php } ?>
    </div>

    <div class="container-fluid">
        <div id="content" class="panel panel-default"><?php echo $content_top; ?>
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $language['text_add_parser_title']; ?></h3>
            </div>
            <div class="panel-body">
                <form action="index.php?route=extension/module/iss_bulksync_setup/addparser&<?php echo $token_name; ?>=<?php echo $user_token; ?>" method="post">
                    <input type="hidden" name="sync_parser_name">
                    <input type="hidden" name="sync_name">
                    <?php foreach($parser_list as  $parser_id => $parser_name) { ?>
                        <button class="btn btn-primary" onclick="$('input[name=sync_parser_name]').val('<?php echo $parser_id; ?>');$('input[name=sync_name]').val('<?php echo $parser_name; ?>');">
                            <i class="fa fa-plus"></i>
                            <?php echo $language['button_add_parser_item']; ?> "<?php echo $parser_name; ?>"
                        </button>
                    <?php } ?>
                </form>
                <?php if(!empty($sync_list)){ ?>
                    <div class="panel-body">
                        <form id='form-parse'>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label><?php echo $language['text_select_parser']; ?></label>
                                        <select data-type="source" class="form-control"  >
                                            <?php foreach($sync_list as  $sync) { ?>
                                                <option value="<?php echo $sync['sync_id']; ?>" data-sync_parser_name="<?php echo $sync['sync_parser_name']; ?>" data-sync_name="<?php echo $sync['sync_name']; ?>">
                                                    <?php if(!empty($sync['sync_last_started'])){ ?>
                                                        <?php echo $sync['sync_name']; ?> (<?php echo $language['text_last_download']; ?>: <?php echo $sync['sync_last_started']; ?>)
                                                    <?php } else { ?>
                                                        <?php echo $sync['sync_name']; ?> (<?php echo $language['text_was_no_download']; ?>)
                                                    <?php } ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="but col" > 
                                    <button type="button" class="btn btn-primary btn-upload-positive" style="width:50%"><i class="fa fa-upload"></i> <?php echo $language['button_parse']; ?></button>
                                    <button type="submit" class="btn btn-primary btn-download-positive" style="width:50%"><i class="fa fa-download"></i> <?php echo $language['button_parse']; ?></button>
                                    <button type="button" class="btn btn-default btn-secondary"><i class="fa fa-eye"></i> <?php echo $language['button_open_parser']; ?></button>
                                    <button type="button" class="btn btn-danger"><i class="fa fa-trash-o"></i></button>
                                </div>
                            </div>    
                        </form>
                    </div>
                <?php } ?>
                <div class="panel-body"  id="iss_selector_panel" style="display:none">
                    <!-- SELECTOR FORM -->
                    <form id="iss_selector_form">
                        <div class="well">
                            <h2><?php echo $language['text_source_settings']; ?></h2>
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $language['text_source_name']; ?>: </label>
                                        <input class="form-control" name="sync_name" placeholder="<?php echo $language['text_source_name']; ?>" required="required">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $language['text_source_language']; ?>: </label>
                                        <select data-type="source" class="form-control" name="source_language" required="required">
                                            <?php foreach($language_list as  $lang) { ?>
                                            <option value="<?php echo $lang['language_id']; ?>"><?php echo $lang['name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $language['text_source_file']; ?>: </label>
                                        <input class="form-control" name="source_file" placeholder="<?php echo $language['text_source_file']; ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="well">
                            <h2><?php echo $language['text_grouping']; ?></h2>
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $language['text_source_group1']; ?>: </label>
                                        <select data-type="source" class="form-control" name="category_lvl1-source" >
                                            <?php echo $source_column_options; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $language['text_source_group2']; ?>: </label>
                                        <select data-type="source" class="form-control" name="category_lvl2-source" >
                                            <?php echo $source_column_options; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $language['text_source_group3']; ?>: </label>
                                        <select data-type="source" class="form-control" name="category_lvl3-source" >
                                            <?php echo $source_column_options; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="well">
                            <h2><?php echo $language['text_product_general_info']; ?></h2>
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $language['text_model']; ?>: </label>
                                        <select data-type="source" class="form-control" name="model-source"  required="required">
                                            <?php echo $source_column_options; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $language['text_name']; ?>: </label>
                                        <select data-type="source" class="form-control" name="product_name-source"  required="required">
                                            <?php echo $source_column_options; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $language['text_description']; ?>: </label>
                                        <select data-type="source" class="form-control" name="description-source" >
                                            <?php echo $source_column_options; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $language['text_manufacturer']; ?>: </label>
                                        <div style="float: right">
                                            <input type="checkbox" name="manufacturer-inattribute" class="form-control" style="display:inline-block;"> <?php echo $language['text_is_attribute']; ?>
                                            <input type="checkbox" name="manufacturer-infilter" class="form-control" style="display:inline-block;"> <?php echo $language['text_is_filter']; ?>
                                        </div>
                                        <select data-type="source" class="form-control" name="manufacturer-source" >
                                            <?php echo $source_column_options; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $language['text_tag']; ?>: </label>
                                        <select data-type="source" class="form-control" name="tag-source" >
                                            <?php echo $source_column_options; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $language['text_points']; ?>: </label>
                                        <div style="float: right">
                                            <input type="checkbox" name="points-inattribute" class="form-control" style="display:inline-block;"> <?php echo $language['text_is_attribute']; ?>
                                        </div>
                                        <select data-type="source" class="form-control" name="points-source" >
                                            <?php echo $source_column_options; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $language['text_ean']; ?>: </label>
                                        <div style="float: right">
                                            <input type="checkbox" name="ean-inattribute" class="form-control" style="display:inline-block;"> <?php echo $language['text_is_attribute']; ?>
                                        </div>
                                        <select data-type="source" class="form-control" name="ean-source" >
                                            <?php echo $source_column_options; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $language['text_mpn']; ?>: </label>
                                        <div style="float: right">
                                            <input type="checkbox" name="mpn-inattribute" class="form-control" style="display:inline-block;"> <?php echo $language['text_is_attribute']; ?>
                                        </div>
                                        <select data-type="source" class="form-control" name="mpn-source" >
                                            <?php echo $source_column_options; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $language['text_weight']; ?>: </label>
                                        <div style="float: right">
                                            <input type="checkbox" name="weight-inattribute" class="form-control" style="display:inline-block;"> <?php echo $language['text_is_attribute']; ?>
                                        </div>
                                        <select data-type="source" class="form-control" name="weight-source" >
                                            <?php echo $source_column_options; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $language['text_width']; ?>: </label>
                                        <div style="float: right">
                                            <input type="checkbox" name="width-inattribute" class="form-control" style="display:inline-block;"> <?php echo $language['text_is_attribute']; ?>
                                        </div>
                                        <select data-type="source" class="form-control" name="width-source" >
                                            <?php echo $source_column_options; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $language['text_length']; ?>: </label>
                                        <div style="float: right">
                                            <input type="checkbox" name="length-inattribute" class="form-control" style="display:inline-block;"> <?php echo $language['text_is_attribute']; ?>
                                        </div>
                                        <select data-type="source" class="form-control" name="length-source" >
                                            <?php echo $source_column_options; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $language['text_height']; ?>: </label>
                                        <div style="float: right">
                                            <input type="checkbox" name="height-inattribute" class="form-control" style="display:inline-block;"> <?php echo $language['text_is_attribute']; ?>
                                        </div>
                                        <select data-type="source" class="form-control" name="height-source" >
                                            <?php echo $source_column_options; ?>
                                        </select>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="well">
                            <h2><?php echo $language['text_product_data']; ?></h2>
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $language['text_price']; ?>: </label>
                                        <select data-type="source" class="form-control" name="price1-source" required="required">
                                            <?php echo $source_column_options; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $language['text_stock_count']; ?>: </label>
                                        <select data-type="source" class="form-control" name="stock_count-source" >
                                            <?php echo $source_column_options; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $language['entry_sort_order']; ?>: </label>
                                        <select data-type="source" class="form-control" name="sort_order-source" >
                                            <?php echo $source_column_options; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $language['text_stock_status']; ?>: </label>
                                        <div style="float: right">
                                            <input type="checkbox" name="stock_status-inattribute" class="form-control" style="display:inline-block;"> <?php echo $language['text_is_attribute']; ?>
                                            <input type="checkbox" name="stock_status-infilter" class="form-control" style="display:inline-block;"> <?php echo $language['text_is_filter']; ?>
                                        </div>
                                        <select data-type="source" class="form-control" name="stock_status-source" >
                                            <?php echo $source_column_options; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $language['text_min_order_size']; ?>: </label>
                                        <div style="float: right">
                                            <input type="checkbox" name="min_order_size-inattribute" class="form-control" style="display:inline-block;"> <?php echo $language['text_is_attribute']; ?>
                                            <input type="checkbox" name="min_order_size-infilter" class="form-control" style="display:inline-block;"> <?php echo $language['text_is_filter']; ?>
                                        </div>
                                        <select data-type="source" class="form-control" name="min_order_size-source" >
                                            <?php echo $source_column_options; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="well">
                            <h2><?php echo $language['text_images']; ?></h2>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label><?php echo $language['text_image_handling']; ?>: </label>
                                        <select data-type="source" class="form-control" name="image_handling" >
                                            <option value="use"><?php echo $language['text_image_url_and_source']; ?></option>
                                            <option value="load"><?php echo $language['text_image_url_only']; ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $language['text_main_image']; ?>: </label>
                                        <select data-type="source" class="form-control" name="image-source" >
                                            <?php echo $source_column_options; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $language['text_additional_image1']; ?>: </label>
                                        <select data-type="source" class="form-control" name="image1-source" >
                                            <?php echo $source_column_options; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $language['text_additional_image2']; ?>: </label>
                                        <select data-type="source" class="form-control" name="image2-source" >
                                            <?php echo $source_column_options; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $language['text_additional_image3']; ?>: </label>
                                        <select data-type="source" class="form-control" name="image3-source" >
                                            <?php echo $source_column_options; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $language['text_additional_image4']; ?>: </label>
                                        <select data-type="source" class="form-control" name="image4-source" >
                                            <?php echo $source_column_options; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $language['text_additional_image5']; ?>: </label>
                                        <select data-type="source" class="form-control" name="image5-source" >
                                            <?php echo $source_column_options; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="well">
                            <h2><?php echo $language['text_options']; ?></h2>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label><?php echo $language['text_option']; ?>:</label>
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <input class="form-control" name="option1-label" placeholder="<?php echo $language['text_option_name']; ?>">
                                            </div>
                                            <div class="col-sm-4">
                                                <select data-type="source" class="form-control" name="option1-source" >
                                                    <?php echo $source_column_options; ?>
                                                </select>
                                            </div>  
                                            <div class="col-sm-4">
                                                <select class="form-control" name="option1-selectortype" >
                                                    <option value="select"><?php echo $language['text_options_select']; ?></option>
                                                    <option value="radio"><?php echo $language['text_options_radio']; ?></option>
                                                    <option value="checkbox"><?php echo $language['text_options_checkbox']; ?></option>
                                                    <option value="text"><?php echo $language['text_options_text']; ?></option>
                                                    <option value="file"><?php echo $language['text_options_file']; ?></option>
                                                    <option value="date"><?php echo $language['text_options_date']; ?></option>
                                                    <option value="time"><?php echo $language['text_options_time']; ?></option>
                                                    <option value="datetime"><?php echo $language['text_options_datetime']; ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> 
                        </div>

                        <div class="well">
                            <h2><?php echo $language['text_attributes']; ?></h2>
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $language['text_attribute1']; ?>:</label>
                                        <div style="float: right">
                                            <input type="checkbox" name="attribute1-infilter" class="form-control" style="display:inline-block"> <?php echo $language['text_is_filter']; ?>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-8">
                                                <input class="form-control" name="attribute1-label" placeholder="<?php echo $language['text_attribute_name']; ?>">
                                            </div>
                                            <div class="col-sm-4">
                                                <select data-type="source" class="form-control" name="attribute1-source" >
                                                    <?php echo $source_column_options; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $language['text_attribute2']; ?>:</label>
                                        <div style="float: right">
                                            <input type="checkbox" name="attribute2-infilter" class="form-control" style="display:inline-block"> <?php echo $language['text_is_filter']; ?>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-8">
                                                <input class="form-control" name="attribute2-label" placeholder="<?php echo $language['text_attribute_name']; ?>">
                                            </div>
                                            <div class="col-sm-4">
                                                <select data-type="source" class="form-control" name="attribute2-source" >
                                                    <?php echo $source_column_options; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $language['text_attribute3']; ?>:</label>
                                        <div style="float: right">
                                            <input type="checkbox" name="attribute3-infilter" class="form-control" style="display:inline-block"> <?php echo $language['text_is_filter']; ?>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-8">
                                                <input class="form-control" name="attribute3-label" placeholder="<?php echo $language['text_attribute_name']; ?>">
                                            </div>
                                            <div class="col-sm-4">
                                                <select data-type="source" class="form-control" name="attribute3-source" >
                                                    <?php echo $source_column_options; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>                

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $language['text_attribute4']; ?>:</label>
                                        <div style="float: right">
                                            <input type="checkbox" name="attribute4-infilter" class="form-control" style="display:inline-block"> <?php echo $language['text_is_filter']; ?>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-8">
                                                <input class="form-control" name="attribute4-label" placeholder="<?php echo  $language['text_attribute_name']; ?>">
                                            </div>
                                            <div class="col-sm-4">
                                                <select data-type="source" class="form-control" name="attribute4-source" >
                                                    <?php echo $source_column_options; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $language['text_attribute5']; ?>:</label>
                                        <div style="float: right">
                                            <input type="checkbox" name="attribute5-infilter" class="form-control" style="display:inline-block"> <?php echo $language['text_is_filter']; ?>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-8">
                                                <input class="form-control" name="attribute5-label" placeholder="<?php echo  $language['text_attribute_name']; ?>">
                                            </div>
                                            <div class="col-sm-4">
                                                <select data-type="source" class="form-control" name="attribute5-source" >
                                                    <?php echo $source_column_options; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $language['text_attribute6']; ?>:</label>
                                        <div style="float: right">
                                            <input type="checkbox" name="attribute6-infilter" class="form-control" style="display:inline-block"> <?php echo $language['text_is_filter']; ?>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-8">
                                                <input class="form-control" name="attribute6-label" placeholder="<?php echo  $language['text_attribute_name']; ?>">
                                            </div>
                                            <div class="col-sm-4">
                                                <select data-type="source" class="form-control" name="attribute6-source" >
                                                    <?php echo $source_column_options; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>                    
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $language['text_attribute7']; ?>:</label>
                                        <div style="float: right">
                                            <input type="checkbox" name="attribute7-infilter" class="form-control" style="display:inline-block"> <?php echo $language['text_is_filter']; ?>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-8">
                                                <input class="form-control" name="attribute7-label" placeholder="<?php echo  $language['text_attribute_name']; ?>">
                                            </div>
                                            <div class="col-sm-4">
                                                <select data-type="source" class="form-control" name="attribute7-source" >
                                                    <?php echo $source_column_options; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $language['text_attribute8']; ?>:</label>
                                        <div style="float: right">
                                            <input type="checkbox" name="attribute8-infilter" class="form-control" style="display:inline-block"> <?php echo $language['text_is_filter']; ?>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-8">
                                                <input class="form-control" name="attribute8-label" placeholder="<?php echo  $language['text_attribute_name']; ?>">
                                            </div>
                                            <div class="col-sm-4">
                                                <select data-type="source" class="form-control" name="attribute8-source" >
                                                    <?php echo $source_column_options; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $language['text_attribute9']; ?>:</label>
                                        <div style="float: right">
                                            <input type="checkbox" name="attribute9-infilter" class="form-control" style="display:inline-block"> <?php echo $language['text_is_filter']; ?>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-8">
                                                <input class="form-control" name="attribute9-label" placeholder="<?php echo  $language['text_attribute_name']; ?>">
                                            </div>
                                            <div class="col-sm-4">
                                                <select data-type="source" class="form-control" name="attribute9-source" >
                                                    <?php echo $source_column_options; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>                    
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $language['text_attribute10']; ?>:</label>
                                        <div style="float: right">
                                            <input type="checkbox" name="attribute10-infilter" class="form-control" style="display:inline-block"> <?php echo $language['text_is_filter']; ?>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-8">
                                                <input class="form-control" name="attribute10-label" placeholder="<?php echo  $language['text_attribute_name']; ?>">
                                            </div>
                                            <div class="col-sm-4">
                                                <select data-type="source" class="form-control" name="attribute10-source" >
                                                    <?php echo $source_column_options; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $language['text_attribute11']; ?>:</label>
                                        <div style="float: right">
                                            <input type="checkbox" name="attribute11-infilter" class="form-control" style="display:inline-block"> <?php echo $language['text_is_filter']; ?>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-8">
                                                <input class="form-control" name="attribute11-label" placeholder="<?php echo  $language['text_attribute_name']; ?>">
                                            </div>
                                            <div class="col-sm-4">
                                                <select data-type="source" class="form-control" name="attribute11-source" >
                                                    <?php echo $source_column_options; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $language['text_attribute12']; ?>:</label>
                                        <div style="float: right">
                                            <input type="checkbox" name="attribute12-infilter" class="form-control" style="display:inline-block"> <?php echo $language['text_is_filter']; ?>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-8">
                                                <input class="form-control" name="attribute12-label" placeholder="<?php echo  $language['text_attribute_name']; ?>">
                                            </div>
                                            <div class="col-sm-4">
                                                <select data-type="source" class="form-control" name="attribute12-source" >
                                                    <?php echo $source_column_options; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>  
                            <h2><?php echo $language['text_attribute_group']; ?></h2>
                            <div class="row">
                                <div class="col-sm-8">
                                    <div class="form-group">
                                        <label><?php echo $language['text_attribute_group_template']; ?>:</label>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <input class="form-control" name="attribute_group-label" placeholder="<?php echo  $language['text_attribute_group_placeholder']; ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div style="float: right">
                                            <input type="checkbox" name="attribute_group-group_infilter" class="form-control" style="display:inline-block"> <?php echo $language['text_is_filter']; ?>
                                        </div>
                                        <select data-type="source" class="form-control" name="attribute_group-source" >
                                            <?php echo $source_column_options; ?>
                                        </select>
                                    </div> 
                                </div> 
                            </div>  
                        </div>
                                                
                                                
                         <a href="#iss_selector_form_extra" data-toggle="collapse"><?php echo $language['text_extra_settings']; ?></a>

                        <div id="iss_selector_form_extra" class="collapse">
                            <div class="form-group">
                                <label><?php echo $language['text_parsing_mode']; ?>:</label>
                                <select name="parsing_mode" class="form-control">
                                    <option value="detect_unchanged_entries"><?php echo $language['text_parse_changed_only']; ?></option>
                                    <option value="import_all"><?php echo $language['text_parse_all']; ?></option>
                                </select>
                                <label><?php echo $language['text_delete_absent']; ?>:</label>
                                <select name="delete_absent_products" class="form-control">
                                    <option value="yes"><?php echo $language['text_yes']; ?></option>
                                    <option value=""><?php echo $language['text_no']; ?> (<?php echo $language['text_default']; ?>)</option>
                                </select>
                                <label><?php echo $language['text_import_images']; ?>:</label>
                                <select name="image_mode" class="form-control">
                                    <option value=""><?php echo $language['text_images_new_only'].' ('.$language['text_default'].')'; ?></option>
                                    <option value="all_products"><?php echo $language['text_images_all']; ?></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><?php echo $language['text_meta_keyword_prefix']; ?>:</label>
                                <input class="form-control" name="meta_keyword_prefix" placeholder="<?php echo $language['text_meta_keyword_prefix']; ?>">
                                <label><?php echo $language['text_meta_tag_prefix']; ?>:</label>
                                <input class="form-control" name="meta_description_prefix" placeholder="<?php echo $language['text_meta_tag_prefix']; ?>">
                            </div>
                            <div class="form-group">
                                <label><?php echo $language['text_filter_delimiters']; ?>:</label>
                                <input class="form-control" name="filter_delimeter" placeholder="<?php echo $language['text_filter_delimiters']; ?>">
                            </div>
                            <div class="form-group">
                                <label><?php echo $language['text_round_to']; ?>:</label>
                                <input class="form-control" name="round_to" placeholder="<?php echo $language['text_round_to']; ?>">

                                <label><?php echo $language['text_tax_class']; ?>:</label>
                                <select data-type="source" class="form-control" name="source_tax_class_id">
                                    <option value="0">-</option>
                                    <?php  foreach($tax_class_list as $tax_class) { ?>
                                    <option value="<?php echo $tax_class['tax_class_id']; ?>"><?php echo $tax_class['title']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div> 
                    </form>
                    <!-- /SELECTOR FORM -->
                </div>
            </div>
            <?php echo $content_bottom; ?>
        </div>
        <?php echo $column_right; ?>
    </div>
</div>
<script type="text/javascript">
    Setup = {
        init: function () {
            Setup.adjustActionButtons();
            Setup.adjustActions();
            $("#form-parse .btn-secondary").click(function () {
                Setup.openImport();
            });
            $('#form-parse select').change(function(){
                var sync_id=$('#form-parse select').val();
                Setup.selectParser(sync_id);
            });
            
            $(".btn.btn-upload-positive").click(Setup.upload_file);
        },
        sync_id: 0,
        user_token: '<?php echo $user_token; ?>',
        token_name: '<?php echo $token_name; ?>',
        source_url: '',
        startParsing:function(request){
            $.post('index.php?route=extension/module/iss_bulksync_setup/startParsing&<?php echo $token_name; ?>=<?php echo $user_token; ?>', request, function (ok) {
                Setup.syncing_in_progress = false;
                if (ok * 1) {
                    alert('<?php echo $language["text_loading_completed"]; ?>');
                    $("#form-parse .btn-download-positive").html('<i id="spinner_icon" class="fa fa-circle-o-notch fa-spin"></i><?php echo $button_parse_completed; ?>');
                    location.href = 'index.php?route=extension/module/iss_bulksync_import&sync_id=' + Setup.sync_id + '&<?php echo $token_name; ?>=<?php echo $user_token; ?>';
                } else {
                    $("#form-parse .btn-download-positive").html('<?php echo $button_parse; ?>');
                    alert("<?php echo $language['text_error_while_parsing']; ?>: " + ok);
                }
            });
        },
        openImport:function(){
            Setup.sync_id = $('#form-parse select').val();
            location.href = 'index.php?route=extension/module/iss_bulksync_import&sync_id=' + Setup.sync_id + '&<?php echo $token_name; ?>=<?php echo $user_token; ?>'; 
        },
        selectParser:function(sync_id){
            Setup.sync_id = sync_id;
            Setup.adjustActionButtons();
        },
        adjustActionButtons: function () {
            var $option=$('#form-parse select').find('option[value=' + $('#form-parse select').val() + ']');
            Setup.sync_parser_name = $option.data('sync_parser_name');
            Setup.sync_name = $option.data('sync_name');
            if (Setup.sync_parser_name && Setup.sync_parser_name.indexOf('upload') > -1) {
                var upload_label = `<i class="fa fa-upload"></i> ${Setup.sync_name}`;
                $(".btn.btn-upload-positive").show().html(upload_label);
                $(".btn.btn-download-positive").hide();
                
                $("#iss_selector_panel").show();
            } else {
                var download_label = `<i class="fa fa-download"></i> ${Setup.sync_name}`;
                $(".btn.btn-download-positive").show().html(download_label);
                $(".btn.btn-upload-positive").hide();
                
                $("#iss_selector_panel").hide();
            }
        },

        adjustActions: function () {
            Setup.sync_id = $('#form-parse select').val();
            $('#form-parse select').on('change', Setup.adjustActionButtons);
            $("#form-parse .btn-danger").click(function () {
                if (confirm("<?php echo $language['text_delete_parser']; ?>")) {
                    Setup.sync_id = $('#form-parse select').val();
                    $.post('index.php?route=extension/module/iss_bulksync_setup/deleteparser&<?php echo $token_name; ?>=<?php echo $user_token; ?>', {sync_id: Setup.sync_id}, function (ok) {
                        location.reload();
                    });
                }
            });
            Setup.syncing_in_progress = false;
            $("#form-parse").on('submit', function (e) {
                Setup.sync_id = $(this).find('select').val();
                if (Setup.syncing_in_progress || !Setup.sync_id) {
                    return;
                }
                Setup.syncing_in_progress = true;
                $("#form-parse .btn-download-positive").html('<i id="spinner_icon" class="fa fa-circle-o-notch fa-spin"></i><?php echo $button_parsing; ?>');
                e.preventDefault();
                $.post('index.php?route=extension/module/iss_bulksync_setup/startParsing&<?php echo $token_name; ?>=<?php echo $user_token; ?>', {sync_id: Setup.sync_id}, function (ok) {
                    Setup.syncing_in_progress = false
                    if (ok * 1) {
                        //alert('<?php echo $language['text_loading_completed']; ?>');
                        $("#form-parse .btn-download-positive").html('<i id="spinner_icon" class="fa fa-circle-o-notch fa-spin"></i><?php echo $button_parse_completed; ?>');
                        location.href = 'index.php?route=extension/module/iss_bulksync_import&sync_id=' + Setup.sync_id + '&<?php echo $token_name; ?>=<?php echo $user_token; ?>';
                    } else {
                        $("#form-parse .btn-download-positive").html('<?php echo $button_parse; ?>');
                        alert("Error occured: " + ok);
                    }
                });
            });
        },
        timer:0,
        upload_file:function(){
            if( !Setup.selectorForm.save() ){
                return false;
            }
            Setup.source_url = $('input[name="source_file"]').val();
            if(Setup.source_url){
                Setup.sync_id = $("#form-parse").find('select').val();
                var request={
                    sync_id: Setup.sync_id
                };
                Setup.startParsing(request);
                return false;
            }
            var element = this;
            $('#form-upload').remove();
            $('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="file" /></form>');
            $('#form-upload input[name=\'file\']').trigger('click');
            clearInterval(Setup.timer);
            Setup.timer = setInterval(function () {
                if ($('#form-upload input[name=\'file\']').val() !== '') {
                    clearInterval(Setup.timer);
                    $.ajax({
                        url: 'index.php?route=tool/upload/upload&<?php echo $token_name; ?>=<?php echo $user_token; ?>',
                        type: 'post',
                        dataType: 'json',
                        data: new FormData($('#form-upload')[0]),
                        cache: false,
                        contentType: false,
                        processData: false,
                        beforeSend: function () {
                            $(element).button('loading');
                        },
                        complete: function () {
                            $(element).button('reset');
                        },
                        success: function (json) {
                            if( json['error'] ) {
                                alert(json['error']);
                            }
                            if (json['success']) {
                                Setup.sync_id = $("#form-parse").find('select').val();
                                $(element).parent().find('input').val(json['code']);
                                var request={
                                    sync_id: Setup.sync_id,
                                    code: json['code']
                                };
                                Setup.startParsing(request);
                            }
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                        }
                    });
                }
            }, 500);
        }
    };
    $(document).on('ready', Setup.init);
</script>
<script type="text/javascript" src="view/javascript/iss_bulksync/csv.js"></script>
<?php echo $footer; ?>
