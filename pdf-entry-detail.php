<?php
class GFEntryDetail{

	/* NEED THIS FUNCTION - BLD */
    public static function notes_grid($notes, $is_editable, $emails = null, $autoresponder_subject=""){
        if(sizeof($notes) > 0 && $is_editable && GFCommon::current_user_can_any("gravityforms_edit_entry_notes")){
            ?>
            <div class="alignleft actions" style="padding:3px 0;">
                <label class="hidden" for="bulk_action"><?php _e(" Bulk action", "gravityforms") ?></label>
                <select name="bulk_action" id="bulk_action">
                    <option value=''><?php _e(" Bulk action ", "gravityforms") ?></option>
                    <option value='delete'><?php _e("Delete", "gravityforms") ?></option>
                </select>
                <?php
                $apply_button = '<input type="submit" class="button" value="' . __("Apply", "gravityforms") . '" onclick="jQuery(\'#action\').val(\'bulk\');" style="width: 50px;" />';
                echo apply_filters("gform_notes_apply_button", $apply_button);
                ?>
            </div>
            <?php
        }
        ?>
        <table class="widefat fixed entry-detail-notes" cellspacing="0">
            <?php
            if(!$is_editable){
            ?>
            <thead>
                <tr>
                    <th id="notes">Notes</th>
                </tr>
            </thead>
            <?php
            }
            ?>
            <tbody id="the-comment-list" class="list:comment">
            <?php
            $count = 0;
            $notes_count = sizeof($notes);
            foreach($notes as $note){
                $count++;
                $is_last = $count >= $notes_count ? true : false;
                ?>
                <tr valign="top">
                    <?php
                    if($is_editable && GFCommon::current_user_can_any("gravityforms_edit_entry_notes")){
                    ?>
                        <th class="check-column" scope="row" style="padding:9px 3px 0 0">
                            <input type="checkbox" value="<?php echo $note->id ?>" name="note[]"/>
                        </th>
                        <td colspan="2">
                    <?php
                    }
                    else{
                    ?>
                        <td class="entry-detail-note<?php echo $is_last ? " lastrow" : "" ?>">
                    <?php
                    }
                    ?>
                            <div style="margin-top:4px;">
                                <div class="note-avatar"><?php echo get_avatar($note->user_id, 48);?></div>
                                <h6 class="note-author"> <?php echo esc_html($note->user_name)?></h6>
                                <p style="line-height:130%; text-align:left; margin-top:3px;"><a href="mailto:<?php echo esc_attr($note->user_email)?>"><?php echo esc_html($note->user_email) ?></a><br />
                                <?php _e("added on", "gravityforms"); ?> <?php echo esc_html(GFCommon::format_date($note->date_created, false)) ?></p>
                            </div>
                            <div class="detail-note-content"><?php echo esc_html($note->value) ?></div>
                        </td>

                </tr>
                <?php
            }
            if($is_editable && GFCommon::current_user_can_any("gravityforms_edit_entry_notes")){
                ?>
                <tr>
                    <td colspan="3" style="padding:10px;" class="lastrow">
                        <textarea name="new_note" style="width:100%; height:50px; margin-bottom:4px;"></textarea>
                        <?php
                        $note_button = '<input type="submit" name="add_note" value="' . __("Add Note", "gravityforms") . '" class="button" style="width:60px;" onclick="jQuery(\'#action\').val(\'add_note\');"/>';
                        echo apply_filters("gform_addnote_button", $note_button);

                        if(!empty($emails)){ ?>
                            &nbsp;&nbsp;
                            <span>
                                <select name="gentry_email_notes_to" onchange="if(jQuery(this).val() != '') {jQuery('#gentry_email_subject_container').css('display', 'inline');} else{jQuery('#gentry_email_subject_container').css('display', 'none');}">
                                    <option value=""><?php _e("Also email this note to", "gravityforms") ?></option>
                                    <?php foreach($emails as $email){ ?>
                                        <option value="<?php echo $email ?>"><?php echo $email ?></option>
                                    <?php } ?>
                                </select>
                                &nbsp;&nbsp;

                                <span id='gentry_email_subject_container' style="display:none;">
                                    <label for="gentry_email_subject"><?php _e("Subject:", "gravityforms") ?></label>
                                    <input type="text" name="gentry_email_subject" id="gentry_email_subject" value="<?php echo $autoresponder_subject ?>" style="width:35%"/>
                                </span>
                            </span>
                        <?php } ?>
                    </td>
                </tr>
            <?php
            }
            ?>
            </tbody>
        </table>
        <?php
    }
	/* NEED THIS FUNCTION - BLD */
    public static function lead_detail_grid($form, $lead, $allow_display_empty_fields=false){
        $form_id = $form["id"];
        $display_empty_fields = false;
        if($allow_display_empty_fields){
            $display_empty_fields = rgget("gf_display_empty_fields", $_COOKIE);
        }

        ?>
        <table cellspacing="0" class="widefat fixed entry-detail-view">
            <thead>
                <tr>
                    <th id="details">
                    <?php echo $form["title"]?> <!--: <?php _e("Entry # ", "gravityforms") ?> <?php echo $lead["id"] ?> -->
                    </th>
                    <th style="width:140px; font-size:10px; text-align: right;">
                    <?php
                        if($allow_display_empty_fields){
                            ?>
                            <input type="checkbox" id="gentry_display_empty_fields" <?php echo $display_empty_fields ? "checked='checked'" : "" ?> onclick="ToggleShowEmptyFields();"/>&nbsp;&nbsp;<label for="gentry_display_empty_fields"><?php _e("show empty fields", "gravityforms") ?></label>
                            <?php
                        }
                        ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php
                $count = 0;
                $field_count = sizeof($form["fields"]);
                $has_product_fields = false;
	
                foreach($form["fields"] as $field){
                    switch(RGFormsModel::get_input_type($field)){
                        case "section" :
                            if(!GFCommon::is_section_empty($field, $form, $lead) || $display_empty_fields){
                                $count++;
                                $is_last = $count >= $field_count ? true : false;
                                ?>
                                <tr>
                                    <td colspan="2" class="entry-view-section-break<?php echo $is_last ? " lastrow" : ""?>"><?php echo esc_html(GFCommon::get_label($field))?></td>
                                </tr>
                                <?php
                            }
                        break;

                        case "captcha":
                        case "html":
                        case "password":
                        case "page":
                            //ignore captcha, html, password, page field
                        break;
						case "signature":
                            $value = RGFormsModel::get_lead_field_value($lead, $field);
                            $folder = site_url() .  "/wp-content/uploads/gravity_forms/signatures/";
							$display_value = '<img src="'. $folder.$value .'" alt="Signature" width="100" height="60" />';
							
							/*$count++;*/
							$is_last = $count >= $field_count && !$has_product_fields ? true : false;
							$last_row = $is_last ? " lastrow" : "";
							/*$even = ($count%2) ? ' odd' : ' even';*/
							
							$content = '
							<tr>
								<td colspan="2" class="entry-view-field-name"> '. esc_html(GFCommon::get_label($field)) .'</td>
							</tr>
							<tr>
								<td colspan="2" class="entry-view-field-value' . $last_row .'">' . $display_value . '</td>
							</tr>';		
							
							echo $content;
						break;

                        default:
						
                            //ignore product fields as they will be grouped together at the end of the grid
                            if(GFCommon::is_product_field($field["type"])){
                                $has_product_fields = true;
                                continue;
                            }

                            $value = RGFormsModel::get_lead_field_value($lead, $field);
                            $display_value = GFCommon::get_lead_field_display($field, $value, $lead["currency"]);

                            $display_value = apply_filters("gform_entry_field_value", $display_value, $field, $lead, $form);

                            if($display_empty_fields || !empty($display_value) || $display_value === "0"){
                                $count++;
                                $is_last = $count >= $field_count && !$has_product_fields ? true : false;
                                $last_row = $is_last ? " lastrow" : "";
								$even = ($count%2) ? ' odd' : ' even';

                                $display_value =  empty($display_value) && $display_value !== "0" ? "&nbsp;" : $display_value;

                                $content = '
                                <tr>
                                    <td colspan="2" class="entry-view-field-value' . $last_row . $even . '"><strong>' .  esc_html(GFCommon::get_label($field)) . '</strong> ' . $display_value . '</td>
                                </tr>';

                                $content = apply_filters("gform_field_content", $content, $field, $value, $lead["id"], $form["id"]);

                                echo $content;

                            }
                        break;
                    }
					
                }
                $products = array();
                if($has_product_fields){
                    $products = GFCommon::get_product_fields($form, $lead);
                    if(!empty($products["products"])){
                        ?>
                        <tr>
                            <td colspan="2" class="entry-view-field-name"><?php echo apply_filters("gform_order_label_{$form["id"]}", apply_filters("gform_order_label", __("Order", "gravityforms"), $form["id"]), $form["id"]) ?></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="entry-view-field-value lastrow">
                                <table class="entry-products" cellspacing="0" width="97%">
                                  <colgroup>
                                          <col class="entry-products-col1" />
                                          <col class="entry-products-col2" />
                                          <col class="entry-products-col3" />
                                          <col class="entry-products-col4" />
                                    </colgroup>
                                    <thead>
                                      <tr>
                                        <th scope="col"><?php echo apply_filters("gform_product_{$form_id}", apply_filters("gform_product", __("Product", "gravityforms"), $form_id), $form_id) ?></th>
                                        <th scope="col" class="textcenter"><?php echo apply_filters("gform_product_qty_{$form_id}", apply_filters("gform_product_qty", __("Qty", "gravityforms"), $form_id), $form_id) ?></th>
                                        <th scope="col"><?php echo apply_filters("gform_product_unitprice_{$form_id}", apply_filters("gform_product_unitprice", __("Unit Price", "gravityforms"), $form_id), $form_id) ?></th>
                                        <th scope="col"><?php echo apply_filters("gform_product_price_{$form_id}", apply_filters("gform_product_price", __("Price", "gravityforms"), $form_id), $form_id) ?></th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                    <?php

                                        $total = 0;
                                        foreach($products["products"] as $product){
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="product_name"><?php echo esc_html($product["name"])?></div>
                                                    
                                                        <?php
                                                        $price = GFCommon::to_number($product["price"]);
                                                        if(is_array(rgar($product,"options"))){
															echo '<ul class="product_options">';
                                                            $count = sizeof($product["options"]);
                                                            $index = 1;
                                                            foreach($product["options"] as $option){
                                                                $price += GFCommon::to_number($option["price"]);
                                                                $class = $index == $count ? " class='lastitem'" : "";
                                                                $index++;
                                                                ?>
                                                                <li<?php echo $class?>><?php echo $option["option_label"]?></li>
                                                                <?php
                                                            }
															echo '</ul>';
                                                        }
                                                        $subtotal = floatval($product["quantity"]) * $price;
                                                        $total += $subtotal;
                                                        ?>
                                                    
                                                </td>
                                                <td class="textcenter"><?php echo $product["quantity"] ?></td>
                                                <td><?php echo GFCommon::to_money($price, $lead["currency"]) ?></td>
                                                <td><?php echo GFCommon::to_money($subtotal, $lead["currency"]) ?></td>
                                            </tr>
                                            <?php
                                        }
                                        $total += floatval($products["shipping"]["price"]);
                                    ?>
                                    </tbody>
                                    <tfoot>
                                        <?php
                                        if(!empty($products["shipping"]["name"])){
                                        ?>
                                            <tr>
                                                <td colspan="2" rowspan="2" class="emptycell">&nbsp;</td>
                                                <td class="textright shipping"><?php echo $products["shipping"]["name"] ?></td>
                                                <td class="shipping_amount"><?php echo GFCommon::to_money($products["shipping"]["price"], $lead["currency"])?>&nbsp;</td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                        <tr>
                                            <?php
                                            if(empty($products["shipping"]["name"])){
                                            ?>
                                                <td colspan="2" class="emptycell">&nbsp;</td>
                                            <?php
                                            }
                                            ?>
                                            <td class="textright grandtotal"><?php _e("Total", "gravityforms") ?></td>
                                            <td class="grandtotal_amount"><?php echo GFCommon::to_money($total, $lead["currency"])?></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </td>
                        </tr>

                        <?php
                    }
                }
                ?>
            </tbody>
        </table>
        <?php
    }
	
	/* returns the form values as an array instead of pre-formated html */
    public static function lead_detail_grid_array($form, $lead, $allow_display_empty_fields=false){
        $form_id = $form["id"];
        $display_empty_fields = false;
        if($allow_display_empty_fields){
            $display_empty_fields = rgget("gf_display_empty_fields", $_COOKIE);
        }

		$form_array['form_title'] = $form['title'];

		$count = 0;
		$field_count = sizeof($form["fields"]);
		$has_product_fields = false;
	
                foreach($form["fields"] as $field){
                    switch(RGFormsModel::get_input_type($field)){
                        case "section" :
                        break;

                        case "captcha":
                        case "html":
							$form_array['html'][] = RGFormsModel::get_lead_field_value($lead, $field); 
                        case "password":
                        case "page":
                            //ignore captcha, html, password, page field
                        break;
						case "signature":
                            $value = RGFormsModel::get_lead_field_value($lead, $field);
                            $folder = site_url() .  "/wp-content/uploads/gravity_forms/signatures/";
							$form_array['signature'][] = '<img src="'. $folder.$value .'" alt="Signature" width="100" height="60" />';
							
							/*$count++;*/
							$is_last = $count >= $field_count && !$has_product_fields ? true : false;
							$last_row = $is_last ? " lastrow" : "";
						break;

                        default:
                            //ignore product fields as they will be grouped together at the end of the grid
                            if(GFCommon::is_product_field($field["type"])){
                                $has_product_fields = true;
                                continue;
                            }

                            $value = RGFormsModel::get_lead_field_value($lead, $field); 
                            $form_array['field'][$field['label']] = GFEntryDetail::get_lead_field_display($field, $value, $lead["currency"]);

                        break;
                    }
					
                }
				
                $products = array();
                if($has_product_fields){
                    $products = GFCommon::get_product_fields($form, $lead);
                    if(!empty($products["products"])){
						foreach($products["products"] as $product){
							$form_array['products'][] = array(
									'name' => esc_html($product['name']), 
									'price' => esc_html($product['price']), 
									'options' => $product['options'], 
									'quantity' => $product["quantity"], 
									'shipping' => $products['shipping']);
						}
					}
				}
		return $form_array;
    }
	
	
	
    public static function get_lead_field_display($field, $value, $currency="", $use_text=false, $format="html", $media="screen"){

        if($field['type'] == 'post_category')
            $value = self::prepare_post_category_value($value, $field);

        switch(RGFormsModel::get_input_type($field)){
            case "name" :
                if(is_array($value)){
                    $prefix = trim(rgget($field["id"] . ".2", $value));
                    $first = trim(rgget($field["id"] . ".3", $value));
                    $last = trim(rgget($field["id"] . ".6", $value));
                    $suffix = trim(rgget($field["id"] . ".8", $value));

                    return array('prefix' => $prefix, 'first' => $first, 'last' => $last, 'suffix' => $suffix);
                }
                else{
                    return $value;
                }

            break;
            case "creditcard" :
                if(is_array($value)){
                    $card_number = trim(rgget($field["id"] . ".1", $value));
                    $card_type = trim(rgget($field["id"] . ".4", $value));
                    $separator = $format == "html" ? "<br/>" : "\n";
                    return empty($card_number) ? "" : $card_type . $separator . $card_number;
                }
                else{
                    return "";
                }
            break;

            case "address" :
                if(is_array($value)){
                    $street_value = trim(rgget($field["id"] . ".1", $value));
                    $street2_value = trim(rgget($field["id"] . ".2", $value));
                    $city_value = trim(rgget($field["id"] . ".3", $value));
                    $state_value = trim(rgget($field["id"] . ".4", $value));
                    $zip_value = trim(rgget($field["id"] . ".5", $value));
                    $country_value = trim(rgget($field["id"] . ".6", $value));

                    $line_break = $format == "html" ? "<br />" : "\n";

                    $address_display_format = apply_filters("gform_address_display_format", "default");

					$address['street'] = $street_value;
					$address['street2'] = $street2_value;
					$address['city'] =  $city_value;
					$address['state'] =  $state_value;
					$address['zip'] = $zip_value;
					$address['country'] = $country_value;

                    return $address;
                }
                else{
                    return "";
                }
            break;

            case "email" :
                return GFCommon::is_valid_email($value) && $format == "html" ? $value : $value;
            break;

            case "website" :
                return GFCommon::is_valid_url($value) && $format == "html" ? $value : $value;
            break;

            case "checkbox" :
                if(is_array($value)){

                    $items = '';

                    foreach($value as $key => $item){
                        if(!empty($item)){
                            switch($format){
                                case "text" :
                                    $items .= GFCommon::selection_display($item, $field, $currency, $use_text) . ", ";
                                break;

                                default:
                                    $items .= "<li>" . GFCommon::selection_display($item, $field, $currency, $use_text) . "</li>";
                                break;
                            }
                        }
                    }
                    if(empty($items)){
                        return "";
                    }
                    else if($format == "text"){
                        return substr($items, 0, strlen($items)-2); //removing last comma
                    }
                    else{
                        return "<ul class='bulleted'>$items</ul>";
                    }
                }
                else{
                    return $value;
                }
            break;

            case "post_image" :
                $ary = explode("|:|", $value);
                $url = count($ary) > 0 ? $ary[0] : "";
                $title = count($ary) > 1 ? $ary[1] : "";
                $caption = count($ary) > 2 ? $ary[2] : "";
                $description = count($ary) > 3 ? $ary[3] : "";

                if(!empty($url)){
                    $url = str_replace(" ", "%20", $url);

                    switch($format){
                        case "text" :
                            $value = $url;
                            $value .= !empty($title) ? "\n\n" . $field["label"] . " (" . __("Title", "gravityforms") . "): " . $title : "";
                            $value .= !empty($caption) ? "\n\n" . $field["label"] . " (" . __("Caption", "gravityforms") . "): " . $caption : "";
                            $value .= !empty($description) ? "\n\n" . $field["label"] . " (" . __("Description", "gravityforms") . "): " . $description : "";
                        break;

                        default :
                            $value = "<a href='$url' target='_blank' title='" . __("Click to view", "gravityforms") . "'><img src='$url' width='100' /></a>";
                            $value .= !empty($title) ? "<div>Title: $title</div>" : "";
                            $value .= !empty($caption) ? "<div>Caption: $caption</div>" : "";
                            $value .= !empty($description) ? "<div>Description: $description</div>": "";

                        break;
                    }
                }
                return $value;

            case "fileupload" :
                $file_path = $value;
                if(!empty($file_path)){
                    $info = pathinfo($file_path);
                    $file_path = esc_attr(str_replace(" ", "%20", $file_path));
                    $value = $format == "text" ? $file_path : "<a href='$file_path' target='_blank' title='" . __("Click to view", "gravityforms") . "'>" . $info["basename"] . "</a>";
                }
                return $value;
            break;

            case "date" :
                return GFCommon::date_display($value, rgar($field, "dateFormat"));
            break;

            case "radio" :
            case "select" :
                return GFCommon::selection_display($value, $field, $currency, $use_text);
            break;

            case "multiselect" :
                if(empty($value) || $format == "text")
                    return $value;

                $value = explode(",", $value);

                $items = '';
                foreach($value as $item){
                    $items .= "<li>" . GFCommon::selection_display($item, $field, $currency, $use_text) . "</li>";
                }

                return "<ul class='bulleted'>{$items}</ul>";

            break;

            case "calculation" :
            case "singleproduct" :
                if(is_array($value)){
                    $product_name = trim($value[$field["id"] . ".1"]);
                    $price = trim($value[$field["id"] . ".2"]);
                    $quantity = trim($value[$field["id"] . ".3"]);

                    $product = $product_name . ", " . __("Qty: ", "gravityforms") . $quantity . ", " . __("Price: ", "gravityforms") . $price;
                    return $product;
                }
                else{
                    return "";
                }
            break;

            case "number" :
                return GFCommon::format_number($value, rgar($field, "numberFormat"));
            break;

            case "singleshipping" :
            case "donation" :
            case "total" :
            case "price" :
                return GFCommon::to_money($value, $currency);

            case "list" :
                if(empty($value))
                    return "";
                $value = unserialize($value);

                $has_columns = is_array($value[0]);

                if(!$has_columns){
                    $items = '';
                    foreach($value as $key => $item){
                        if(!empty($item)){
                            switch($format){
                                case "text" :
                                    $items .= $item . ", ";
                                break;
                                case "url" :
                                    $items .= $item . ",";
                                break;
                                default :
                                    if($media == "email"){
                                        $items .= "<li>{$item}</li>";
                                    }
                                    else{
                                        $items .= "<li>{$item}</li>";
                                    }
                                break;
                            }
                        }
                    }

                    if(empty($items)){
                        return "";
                    }
                    else if($format == "text"){
                        return substr($items, 0, strlen($items)-2); //removing last comma
                    }
                    else if($format == "url"){
                        return substr($items, 0, strlen($items)-1); //removing last comma
                    }
                    else if($media == "email"){
                        return "<ul class='bulleted'>{$items}</ul>";
                    }
                    else{
                        return "<ul class='bulleted'>{$items}</ul>";
                    }
                }
                else if(is_array($value)){
                    $columns = array_keys($value[0]);

                    $list = "";

                    switch($format){
                        case "text" :
                            $is_first_row = true;
                            foreach($value as $item){
                                if(!$is_first_row)
                                    $list .= "\n\n" . $field["label"] . ": ";
                                $list .= implode(",", array_values($item));

                                $is_first_row = false;
                            }
                        break;

                        case "url" :
                            foreach($value as $item){
                                $list .= implode("|", array_values($item)) . ",";
                            }
                            if(!empty($list))
                                $list = substr($list, 0, strlen($list)-1);
                        break;

                        default :
                            if($media == "email"){
                                $list = "<table class='gfield_list' style='border-top: 1px solid #DFDFDF; border-left: 1px solid #DFDFDF; border-spacing: 0; padding: 0; margin: 2px 0 6px; width: 100%'><thead><tr>";

                                //reading columns from entry data
                                foreach($columns as $column){
                                    $list .= "<th style='background-image: none; border-right: 1px solid #DFDFDF; border-bottom: 1px solid #DFDFDF; padding: 6px 10px; font-family: sans-serif; font-size: 12px; font-weight: bold; background-color: #F1F1F1; color:#333; text-align:left'>" . esc_html($column) . "</th>";
                                }
                                $list .= "</tr></thead>";

                                $list .= "<tbody style='background-color: #F9F9F9'>";
                                foreach($value as $item){
                                    $list .= "<tr>";
                                    foreach($columns as $column){
                                        $val = rgar($item, $column);
                                        $list .= "<td style='padding: 6px 10px; border-right: 1px solid #DFDFDF; border-bottom: 1px solid #DFDFDF; border-top: 1px solid #FFF; font-family: sans-serif; font-size:12px;'>{$val}</td>";
                                    }

                                    $list .="</tr>";
                                }

                                $list .="<tbody></table>";
                            }
                            else{
                                $list = "<table class='gfield_list'><thead><tr>";

                                //reading columns from entry data
                                foreach($columns as $column){
                                    $list .= "<th>" . esc_html($column) . "</th>";
                                }
                                $list .= "</tr></thead>";

                                $list .= "<tbody>";
                                foreach($value as $item){
                                    $list .= "<tr>";
                                    foreach($columns as $column){
                                        $val = rgar($item, $column);
                                        $list .= "<td>{$val}</td>";
                                    }

                                    $list .="</tr>";
                                }

                                $list .="<tbody></table>";
                            }
                        break;
                    }

                    return $list;
                }
                return "";
            break;

            default :
            	if (!is_array($value))
            	{
                	return nl2br($value);
				}
            break;
        }
    }	
	
}
?>