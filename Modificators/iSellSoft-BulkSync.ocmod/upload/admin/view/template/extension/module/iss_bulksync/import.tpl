<style>
    .input-group.mb-10 form{
        display: flex;
        margin-bottom: 10px; 
        margin-top: 10px;
        border: 2px solid lightgray;
    }

    input{
        background-color:white;
    }
    tbody form {
        background-color: white;
        border: 2px solid lightgray;
    }
    tbody form select{
        background-color: white;
        border: 2px solid lightgray; 
        width: 100%;
    }
    .btn.btn-outline-secondary{
        border-radius:0px;
    }
    .table-bordered input, .table-bordered select{
        border: 1px solid #d0d0d0;
        width: 100%;
    }
    .category_lvl{
        border-top: none !important;
        border-left: none !important;
        border-right: none !important;
        border-bottom: 1px solid lightgray  !important;
    }


</style>

<?php echo $header; ?><?php echo $column_left; ?>

<div id="content" >
    <div class="page-header">
        <div class="container-fluid">
          <h1><?php echo $heading_title; ?></h1>
          <ul class="breadcrumb">
            <?php foreach($breadcrumbs as  $breadcrumb){ ?>
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
    
    <div class="row"
        <?php if(isset($column_left) && isset($column_right)){ ?>
            <?php echo 'class="col-sm-6"'; ?>
        <?php } else if(isset($column_left) || isset($column_right)){ ?>    
            <?php echo 'class="col-sm-9"'; ?>
        <?php } else { ?>   
            <?php echo 'class="col-sm-12"'; ?>
        <?php } ?>
        >
        <div class="container-fluid">
            <div id="content" class="panel panel-default"><?php echo $content_top; ?>
                <div class="panel-heading">
                    <button style="margin: 10px 0" class="btn btn-default"><a href="<?php echo $back_link; ?>"><i class="fa fa-backward"></i> <?php echo $language['text_select_parser']; ?></a></button>
                    <button style="width:100%" type="button" id="button-import" class="btn btn-primary"><i style="display: none"  id="spinner_icon" class="fa fa-circle-o-notch fa-spin"></i> <?php echo $language['button_import']; ?></button>  
                    <div class="progress-header" style="margin-top: 15px; margin-bottom:15px"></div>
                    <div class="progress" style="height: 10px">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <hr>
                <div class="panel-body">
                    <form method="post" action="<?php echo $url; ?>"> 
                        <table style="width:100%;">
                            <tr>
                                <td style="width:100%; ">
                                    <input type="text" class="form-control" name="filter_name" value="<?php echo $filter_name; ?>" placeholder="<?php echo $language['text_filter_group']; ?>" style="display:inline-block;width: 96%;"/>
                                    <button class="btn btn-primary" style="width:3%;padding: 11px;display:inline-block;"><i class="fa fa-search"></i></button>
                                </td>
                            </tr>
                        </table>
                    </form>
                    <div class="form-group"> 
                        <form action="" method="post" enctype="multipart/form-data" id="form-import">
                            <div class="table-responsive">
                                <table class="table table-bordered" style="font-size: 13px;">
                                    <thead>
                                        <tr>
                                            <td style="" class="text-center" colspan="3"><?php echo $language['text_product_group']; ?></td>
                                            <td style="" class="text-left"><?php echo $language['text_total_products']; ?></td>
                                            <td class="text-left"><?php echo $language['text_comission']; ?></td>
                                            <td class="text-left"><?php echo $language['text_dest_cat']; ?></td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(!empty($categories)){ ?>
                                            <?php foreach($categories as $category){ ?>
                                                <tr class="x-form-row  <?php if($category['destination_categories']){ echo 'selected-row'; } ?>" data-group_id="<?php echo $category['group_id']; ?>">
                                                    <td class="text-left category_lvl" ><?php if(!empty($category['category_lvl1'])){ echo $category['category_lvl1']; } ?></td>
                                                    <td class="text-left category_lvl"><?php if(!empty($category['category_lvl2'])){ echo $category['category_lvl2']; } ?></td>
                                                    <td class="text-left category_lvl"><?php if(!empty($category['category_lvl3'])){ echo $category['category_lvl3']; } ?></td>
                                                    <td class="text-left"><?php echo $category['total_products']; ?></td>
                                                    <td class="text-left">
                                                        <input style=""  type="text" name="category_comission" value="<?php echo $category['comission']; ?>" placeholder="<?php echo $language['text_comission']; ?> %" data-group_id="<?php echo $category['group_id']; ?>" class="form-control" />
                                                    </td>
                                                    <td class="text-left" data-group_id="<?php echo $category['group_id']; ?>">
                                                        <input type="text" name="category" value="" placeholder="<?php echo $language['text_entry_category']; ?>"class="form-control"/>
                                                        <div class="destination-category-panel" class="well well-sm" style="overflow: auto;"> 
                                                            <?php $destination_category_list = explode('||',$category['destination_categories']); ?>
                                                            <?php  foreach($destination_category_list as $destination_category){ ?>
                                                                <?php  foreach($all_categories as $allowed_category){ ?>
                                                                    <?php if($destination_category == $allowed_category['category_id']){ ?>
                                                                    <div id="category_<?php echo $allowed_category['category_id']; ?>">
                                                                        <i class="fa fa-minus-circle" id="<?php echo $allowed_category['category_id']; ?>_<?php echo $category['group_id']; ?>"></i> 
                                                                        <?php echo $allowed_category['name']; ?>
                                                                    </div>
                                                                    <?php } ?>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </div>
                                                        <input autocomplete="off" type="hidden" id="destination_categories_<?php echo $category['group_id']; ?>" name="destination_category" value="|<?php echo $category['destination_categories']; ?>|"/>
                                                        <input autocomplete="off" type="hidden" name="group_id" value="<?php echo $category['group_id']; ?>"/>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <tr>
                                                <td class="text-center" colspan="10"><?php echo $language['text_no_results']; ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                                    <br><br><br><br><br><br><br><br>
                            </div>
                        </form>
                        <div class="row">
                            <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
                            <div class="col-sm-6 text-right"><?php echo $results; ?></div>

                        </div><?php echo $content_bottom; ?>
                    </div>
                    </div>
            </div>
            <?php echo $column_right; ?></div>
    </div>
    <script type="text/javascript">
	var sync_id = "<?php echo $sync_id; ?>";
	$("#form-import input").on('change', function (e) {
	    e.preventDefault();
	    savePrefs(e.target);
	});
        
        function savePrefs( node ){
            var group_id=$(node).data('group_id');
            var $row_node=$(`tr[data-group_id=${group_id}]`);
	    var item = {
		group_id: group_id,
		category_comission: $row_node.find('input[name=category_comission]').val().replace(/[^\d\.-]/g, ''),
		destination_categories: $row_node.find('input[name=destination_category]').val().substring(1).slice(0,-1) 
	    };
	    if (item.destination_categories !== "") {
		$row_node.addClass('selected-row');
	    } else {
		$row_node.removeClass('selected-row');
	    }
	    $.post('index.php?route=extension/module/iss_bulksync_import/saveImportPrefs&token=<?php echo $token; ?>', {data: JSON.stringify(item)}, function (ok) {
		if (!ok * 1) {
		    alert("<?php echo $language['text_save_setting_failed']; ?>");
		};
	    });
        }
        
	var step_size = 0;
	var current_progress = 0;

	$("#button-import").on('click', function () {
	    if (confirm('<?php echo $language["text_confirm"]; ?>')) {
		$('.progress-header').html('<?php echo $language["text_import_starting"]; ?>');
		$('#spinner_icon').show();
		$(".btn-secondary").attr('disabled', '');
		$.post('index.php?route=extension/module/iss_bulksync_import/getTotalImportCategories&token=<?php echo $token; ?>', {sync_id: sync_id}, function (total) {
		    var totalCategories = JSON.parse(total);
		    step_size = 100 / totalCategories.total_rows;
		    $('.progress-header').val('<?php echo $language["text_import_progress"]; ?> ' + Math.round(current_progress) + '%');
		    continueImport(totalCategories);
		});
	    }
	});

	function continueImport(totalCategories) {
	    if (totalCategories.total_rows == 0) {
                current_progress=0;
		$('.progress-bar').width('100%');
		$('.progress-header').html('<?php echo $language["text_delete_absent"]; ?>');
		$.post('index.php?route=extension/module/iss_bulksync_import/deleteAbsentProducts&token=<?php echo $token; ?>',{sync_id: sync_id}).done(function(ok){
		    $('.progress-header').html('<?php echo $language["text_import_finished"]; ?>');
		    $('#spinner_icon').hide();
		    $(".btn-secondary").attr('disabled', null);
                    if( !ok*1 ){
                        alert('<?php echo $language["text_import_failed"]; ?>');
                    }
		});
		return;
	    }
	    $.post('index.php?route=extension/module/iss_bulksync_import/importUserProducts&token=<?php echo $token; ?>', {sync_id: sync_id, group_id: totalCategories.groups[0]}).done(function (ok) {
		if (totalCategories.total_rows > 0) {
		    current_progress += step_size;
		    totalCategories.total_rows -= 1;
		    totalCategories.groups.shift()
		    $('.progress-header').html('<?php echo $language["text_import_progress"]; ?> ' + Math.round(current_progress) + '%');
		    $('.progress-bar').width(current_progress + '%');
		    continueImport(totalCategories);
		} else {
                    current_progress=0;
		    $('#spinner_icon').hide();
		    $(".btn-secondary").attr('disabled', null);
                    if( !ok*1 ){
                        alert('<?php echo $language["text_import_failed"]; ?>');
                    }
		}
	    }).fail(function(){
                continueImport(totalCategories);
            });
	}
        // Category
          $('input[name=\'category\']').autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: 'index.php?route=extension/module/iss_bulksync_import/autocompleteCategories&token=<?php echo $token; ?>&filter_name=' + encodeURIComponent(request),
                        dataType: 'json',
                        success: function(json) {
                                response($.map(json, function(item) {
                                        return {
                                                label: item['name'],
                                                value: item['category_id']
                                        }
                                }));
                        }
                    });
                },
                select: function(item) {
                    var group_id = $(this).parent().data('group_id');
                    var input_value =  $('#destination_categories_'+group_id).val();
                    var in_the_middle = ((input_value.indexOf('|'+item['value']+'|') > -1));
                    if(in_the_middle){
                        return;
                    }
                    $('#destination_categories_'+group_id).val(input_value+'|'+item['value']+'|');
                    $(this).next().next().append('<div id="category' + item['value'] + '"><i class="fa fa-minus-circle" id="'+item['value']+'_'+group_id+'"></i> ' + item['label'] + '</div>');
                    savePrefs(this.parentElement);
                }
          });

          $('.destination-category-panel').delegate('.fa-minus-circle', 'click', function() {
                var category_id = $(this).attr('id').split('_')[0];
                var group_id = $(this).attr('id').split('_')[1];
                var input_value = $('#destination_categories_'+group_id).val();
                $('#destination_categories_'+group_id).val(input_value.replace('|'+category_id+'|',''));
                savePrefs(this.parentElement.parentElement.parentElement);
                $(this).parent().remove();
          });
    </script>
    <style>
        .selected-row{
            background-color:#ffc
        }
    </style>
</div>
<?php echo $footer; ?>