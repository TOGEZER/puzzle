<?php

function likebtn_admin_votes() {

    global $likebtn_page_sizes;
    global $likebtn_countries;
    global $wpdb;
    global $blog_id;

    wp_enqueue_script('jquery-ui-dialog');
    wp_enqueue_style("wp-jquery-ui-dialog");

    $likebtn_entities = _likebtn_get_entities(true);
    // Custom item
    $likebtn_entities[LIKEBTN_ENTITY_CUSTOM_ITEM] = __('Custom item');

    $page_size = LIKEBTN_STATISTIC_PAGE_SIZE;
    if (isset($_GET['likebtn_page_size'])) {
        $page_size = (int)$_GET['likebtn_page_size'];
    }

    // pagination
    require_once(dirname(__FILE__) . '/likebtn_like_button_pagination.class.php');

    $pagination_target = "admin.php?page=likebtn_votes";
    foreach ($_GET as $get_parameter => $get_value) {
        $pagination_target .= '&' . $get_parameter . '=' . stripcslashes($get_value);
    }

    $p = new LikeBtnLikeButtonPagination();
    $p->limit($page_size); // Limit entries per page
    $p->target($pagination_target);
    //$p->currentPage(); // Gets and validates the current page
    $p->prevLabel(__('Previous', LIKEBTN_I18N_DOMAIN));
    $p->nextLabel(__('Next', LIKEBTN_I18N_DOMAIN));

    if (!isset($_GET['paging'])) {
        $p->page = 1;
    } else {
        $p->page = $_GET['paging'];
    }

    // query for limit paging
    $query_limit = "LIMIT " . ($p->page - 1) * $p->limit . ", " . $p->limit;

    list($query_prepared, $blogs, $votes_blog_id, $entity_name, $post_id, $user_id, $ip, $vote_type, $country) = likebtn_votes_query($query_limit);

    // echo "<pre>";
    // echo $query;
    // echo $query_prepared;
    // echo $wpdb->prepare($query, $query_parameters);
    // $wpdb->show_errors();
    // exit();
    $votes = $wpdb->get_results($query_prepared);

    $total_found = 0;
    if (isset($votes[0])) {
        $query_found_rows = "SELECT FOUND_ROWS() as found_rows";
        $found_rows = $wpdb->get_results($query_found_rows);

        $total_found = (int) $found_rows[0]->found_rows;

        $p->items($total_found);
        $p->calculate(); // Calculates what to show
        $p->parameterName('paging');
        $p->adjacents(1); // No. of page away from the current page
    } else {
        $votes = array();
    }

    // Countries
    $countries = $likebtn_countries;

    $loader = _likebtn_get_public_url() . 'img/ajax_loader_hor.gif';

    likebtn_admin_header();
    ?>

    <script type="text/javascript">
        var likebtn_msg_ip_info = '<?php _e("IP Info", LIKEBTN_I18N_DOMAIN); ?>';
    </script>

    <div>
        <form action="" method="get" id="votes_form" autocomplete="off">
            <input type="hidden" name="page" value="likebtn_votes" />
            <div class="postbox statistics_filter_container">
                <div class="inside">
                    <div class="likebtn-form-group">
                        <label><?php _e('Item Type', LIKEBTN_I18N_DOMAIN); ?>:</label>
                        <select name="likebtn_entity_name" >
                            <option value="">-- <?php _e('Any', LIKEBTN_I18N_DOMAIN); ?> --</option>
                            <?php foreach ($likebtn_entities as $entity_name_value => $entity_title): ?>
                                <option value="<?php echo $entity_name_value; ?>" <?php selected($entity_name, $entity_name_value); ?> ><?php _e($entity_title, LIKEBTN_I18N_DOMAIN); ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <div class="likebtn-form-group">
                        <label><?php _e('Item ID', LIKEBTN_I18N_DOMAIN); ?>:</label>
                        <input type="text" name="likebtn_post_id" value="<?php echo htmlspecialchars($post_id) ?>" size="10" />
                    </div>
                    <br/>
                    <div class="likebtn-form-group">
                        <label><?php _e('User ID', LIKEBTN_I18N_DOMAIN); ?>:</label>
                        <input type="text" name="likebtn_user_id" value="<?php echo htmlspecialchars($user_id) ?>" size="10" />
                    </div>
                    
                    <div class="likebtn-form-group">
                        <label><?php _e('IP'); ?>:</label>
                        <input type="text" name="likebtn_ip" value="<?php echo htmlspecialchars($ip) ?>" size="20"/>
                    </div>

                    <?php if (!empty($countries)): ?>
                        <div class="likebtn-form-group">
                            <label><?php _e('Country', LIKEBTN_I18N_DOMAIN); ?>:</label>
                            <select name="likebtn_country" style="width:160px">
                                <option value=""></option>
                                <?php foreach ($countries as $country_code => $country_name): ?>
                                    <option value="<?php echo $country_code; ?>" <?php selected($country, $country_code); ?> ><?php echo $country_name ?> - <?php echo $country_code; ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    <?php endif ?>
                    
                    <div class="likebtn-form-group">
                        <label><?php _e('Vote Type', LIKEBTN_I18N_DOMAIN); ?>:</label>
                        <select name="likebtn_vote_type" >
                            <option value="">-- <?php _e('Likes & Dislikes', LIKEBTN_I18N_DOMAIN); ?> --</option>
                            <option value="1" <?php selected((int)$vote_type, 1); ?> ><?php _e('Likes', LIKEBTN_I18N_DOMAIN); ?></option>
                            <option value="-1" <?php selected((int)$vote_type, -1); ?> ><?php _e('Dislikes', LIKEBTN_I18N_DOMAIN); ?></option>
                        </select>
                    </div>

                    <div class="likebtn-form-group">
                        <input class="button-secondary" type="button" name="reset" value="<?php _e('Reset filter', LIKEBTN_I18N_DOMAIN); ?>" onClick="jQuery('.statistics_filter_container :input[type!=button]').val('');
                jQuery('#votes_form').submit();"/>
                    </div>
                </div>
            </div>

            <?php if ($blogs): ?>
                <label><?php _e('Site', LIKEBTN_I18N_DOMAIN); ?>:</label>
                <select name="likebtn_blog_id" >
                    <?php foreach ($blogs as $blog_id_value => $blog_title): ?>
                        <option value="<?php echo $blog_id_value; ?>" <?php selected($votes_blog_id, $blog_id_value); ?> ><?php echo $blog_title; ?></option>
                    <?php endforeach ?>
                </select>&nbsp;&nbsp;
            <?php endif ?>
            
            <label><?php _e('Page Size', LIKEBTN_I18N_DOMAIN); ?>:</label>
            <select name="likebtn_page_size" >
                <?php foreach ($likebtn_page_sizes as $page_size_value): ?>
                    <option value="<?php echo $page_size_value; ?>" <?php selected($page_size, $page_size_value); ?> ><?php echo $page_size_value ?></option>
                <?php endforeach ?>

            </select><br/><br/>
            <div class="tablenav">
                <nobr>
                    <input class="button-primary" type="submit" name="show" value="<?php _e('View', LIKEBTN_I18N_DOMAIN); ?>" /> 
                    &nbsp;
                    <?php _e('Votes Found', LIKEBTN_I18N_DOMAIN); ?>: <strong><?php echo $total_found ?></strong>
                </nobr>
                <?php if (count($votes) && $p->lastpage > 1): ?>
                    <div class="tablenav-pages">
                        <?php echo $p->show(); ?>
                    </div>
                <?php endif ?>
            </div>
        </form>
        <br/>

        <div class="tablenav">
            <button type="button" class="button-secondary" onclick="likebtnVg('<?php _e('Votes Graph', LIKEBTN_I18N_DOMAIN); ?>', '<?php echo get_option('likebtn_plan') ?>')" ><img src="<?php echo _likebtn_get_public_url(); ?>img/graph.png" class="likebtn-btn-img"/> <?php _e('Votes Graph', LIKEBTN_I18N_DOMAIN); ?></button>
        </div>

        <form method="post" action="" id="votes_actions_form">
        <input type="hidden" name="bulk_action" value="" id="stats_bulk_action" />
        <table class="widefat" id="votes_container">
            <thead>
                <tr>
                    <?php /*<th><input type="checkbox" onclick="statisticsItemsCheckbox(this)" value="all" style="margin:0"></th>*/ ?>
                    <?php if ($blogs && $votes_blog_id == 'all'): ?>
                        <th><?php _e('Site') ?></th>
                    <?php endif ?>
                    <th colspan="2"><?php _e('User', LIKEBTN_I18N_DOMAIN) ?></th>
                    <th>IP</th>
                    <th><?php _e('Date', LIKEBTN_I18N_DOMAIN) ?></th>
                    <th><?php _e('Type', LIKEBTN_I18N_DOMAIN) ?></th>
                    <th><?php _e('Item', LIKEBTN_I18N_DOMAIN) ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($votes as $votes_item): ?>
                    <?php
                        // Switch to blog if needed
                        if ($blogs && isset($votes_item->blog_id) && $votes_item->blog_id != $blog_id) {
                            switch_to_blog($votes_item->blog_id);
                        }

                        $entity_info = _likebtn_parse_identifier($votes_item->identifier);

                        $user_name = '';
                        if ($votes_item->user_id) {
                            $user_name = _likebtn_get_entity_title(LIKEBTN_ENTITY_USER, $votes_item->user_id);
                        }
                        $avatar_url = '';
                        if ($user_name) {
                            $avatar_url = _likebtn_get_avatar_url($votes_item->user_id);
                        }

                        $user_url = '';
                        if ($user_name) {
                            $user_url = _likebtn_get_entity_url(LIKEBTN_ENTITY_USER, $votes_item->user_id);
                        }

                        $item_title = '';
                        $item_url = '';
                        if ($votes_item->item_id) {
                            $item_title = $votes_item->identifier;
                            $item_url = $votes_item->url;
                            $entity_type_name = __('Custom Item', LIKEBTN_I18N_DOMAIN);
                        } else {
                            if ($entity_info['entity_name'] && $entity_info['entity_id']) {
                                $item_title = _likebtn_get_entity_title($entity_info['entity_name'], $entity_info['entity_id']);
                                $item_title = _likebtn_prepare_title($entity_info['entity_name'], $item_title);
                                $item_url = _likebtn_get_entity_url($entity_info['entity_name'], $entity_info['entity_id'], '', $votes_blog_id);
                            }
                            $entity_type_name = _likebtn_get_entity_name_title($entity_info['entity_name']);
                        }

                        if ((int)$votes_item->type == 1) {
                            $entity_vote_type = 'like';
                        } else {
                            $entity_vote_type = 'dislike';
                        }
                    ?>

                    <tr id="vote_<?php echo $votes_item->id; ?>">
                        <?php /*<td><input type="checkbox" class="item_checkbox" value="<?php echo $votes_item->post_id; ?>" name="item[]" <?php if ($blogs && $votes_item->blog_id != $blog_id): ?>disabled="disabled"<?php endif ?>></td>*/ ?>
                        <?php if ($blogs && $votes_blog_id == 'all'): ?>
                            <td><?php echo get_blog_option($votes_item->blog_id, 'blogname') ?></td>
                        <?php endif ?>
                        <?php if ($avatar_url): ?>
                            <td width="32">
                                <a href="<?php echo $user_url ?>" target="_blank"><img src="<?php echo $avatar_url; ?>" width="32" height="32" /></a>
                            </td>
                        <?php endif ?>
                        <td <?php if (!$avatar_url): ?>colspan="2"<?php endif ?>>
                            <?php if ($user_name): ?>
                                <a href="<?php echo $user_url ?>" target="_blank"><?php echo $user_name; ?></a>
                            <?php else: ?>
                                <?php echo __('Anonymous', LIKEBTN_I18N_DOMAIN); ?>
                            <?php endif ?>
                        </td>
                        <td><a href="javascript:likebtnIpInfo('<?php echo $votes_item->ip; ?>');" class="likebtn_ttip" title="<?php _e('View IP info', LIKEBTN_I18N_DOMAIN) ?>"><?php echo $votes_item->ip; ?></a></td>
                        <td><?php echo date("Y.m.d H:i:s", strtotime($votes_item->created_at)); ?></td>
                        <td>
                            <img src="<?php echo _likebtn_get_public_url()?>img/thumb/<?php echo $entity_vote_type; ?>.png" alt="<?php _e(ucfirst($entity_vote_type), LIKEBTN_I18N_DOMAIN) ?>" title="<?php _e(ucfirst($entity_vote_type), LIKEBTN_I18N_DOMAIN) ?>" class="likebtn_ttip" />
                        </td>
                        <td><a href="<?php echo $item_url ?>" target="_blank"><?php echo $item_title; ?></a> 
                            <?php if ($entity_type_name): ?>
                                — <?php echo $entity_type_name ?><?php if (isset($entity_info['entity_id'])): ?> (<?php echo $entity_info['entity_id']; ?>)<?php endif ?>
                            <?php endif ?>
                        </td>
                    </tr>
                <?php endforeach ?>

                <?php
                    if ($blogs && $blog_id) {
                        switch_to_blog($blog_id);
                    }
                ?>
            </tbody>
        </table>
        </form>
        <?php if (count($votes) && $p->lastpage > 1): ?>
            <div class="tablenav">
                <div class="tablenav-pages">
                    <?php echo $p->show(); ?>
                </div>
            </div>
        <?php endif ?>
        <br/><br/>
        <a href="javascript:jQuery('#likebtn_no_vts').toggle();void(0);"><?php _e('Do not see votes?', LIKEBTN_I18N_DOMAIN); ?></a>
        <div id="likebtn_no_vts">
            <p class="description">
                ● <?php _e('If Like button is added using HTML-code votes will not be populated into your database. The recommended way of enabling the Like buttons is via <strong>Buttons</strong> tab or <a href="https://likebtn.com/en/wordpress-like-button-plugin#shortcode" target="_blank">[likebtn] shortcode</a>.', LIKEBTN_I18N_DOMAIN); ?><br/>
                ● <?php echo strtr(
           __('Make sure not to disable anonymous access to %admin_ajax%, otherwise votes from anonymous visitors will not be accepted.', LIKEBTN_I18N_DOMAIN), 
            array('%admin_ajax%'=>'<a href="'.admin_url('admin-ajax.php').'" target="_blank">/wp-admin/admin-ajax.php</a>')) ?>
            </p>
        </div>
    </div>

    <div id="likebtn_ip_info" class="likebtn_ip_info hidden">
        <div class="likebtn_ip_info_map"></div>
        <table class="widefat">
            <tr>
                <th><strong>IP</strong></th>
                <td class="likebtn-ii-ip" width="50%"><img src="<?php echo $loader ?>" /></td>
            </tr>
            <tr>
                <th><strong><?php _e('Country', LIKEBTN_I18N_DOMAIN); ?></strong></th>
                <td class="likebtn-ii-country"><img src="<?php echo $loader ?>" /></td>
            </tr>
            <tr>
                <th><strong><?php _e('City', LIKEBTN_I18N_DOMAIN); ?></strong></th>
                <td class="likebtn-ii-city"><img src="<?php echo $loader ?>" /></td>
            </tr>
            <tr>
                <th><strong><?php _e('Lat/Long', LIKEBTN_I18N_DOMAIN); ?></strong></th>
                <td class="likebtn-ii-latlon"><img src="<?php echo $loader ?>" /></td>
            </tr>
            <tr>
                <th><strong><?php _e('Postal Code', LIKEBTN_I18N_DOMAIN); ?></strong></th>
                <td class="likebtn-ii-postal"><img src="<?php echo $loader ?>" /></td>
            </tr>
            <tr>
                <th><strong><?php _e('Network', LIKEBTN_I18N_DOMAIN); ?></strong></th>
                <td class="likebtn-ii-network"><img src="<?php echo $loader ?>" /></td>
            </tr>
            <tr>
                <th><strong><?php _e('Hostname', LIKEBTN_I18N_DOMAIN); ?></strong></th>
                <td class="likebtn-ii-hostname"><img src="<?php echo $loader ?>" /></td>
            </tr>
        </table>
        <div class="ui-dialog-buttonpane ui-widget-content ui-helper-clearfix">
            <div class="ui-dialog-buttonset">
                <button type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only button-secondary likebtn-button-close" role="button"><span class="ui-button-text"><?php _e('Close', LIKEBTN_I18N_DOMAIN); ?></span></button>
            </div>
        </div>
    </div>
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?v=3.exp&callback=showMap">
    </script>
    <div id="likebtn_vg" class="hidden">
        <div class="likebtn-vgraph"></div>
        <div class="likebtn-vgraph-error likebtn_error">
            <?php echo _e('Error occured, please try again later.', LIKEBTN_I18N_DOMAIN) ?>
        </div>
        <div class="ui-dialog-buttonpane ui-widget-content ui-helper-clearfix">
            <div class="ui-dialog-buttonset">
                <button type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only button-secondary likebtn-button-close" role="button"><span class="ui-button-text"><?php _e('Close', LIKEBTN_I18N_DOMAIN); ?></span></button>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="<?php echo _likebtn_get_public_url() ?>/js/highstock/highstock.js"></script>
    <script type="text/javascript" src="<?php echo _likebtn_get_public_url() ?>/js/highstock/exporting.js"></script>
    <script type="text/javascript" src="<?php echo _likebtn_get_public_url() ?>/js/highstock/no-data-to-display.js"></script>
    <script type="text/javascript">
        var global_highcharts_lang = {
            rangeSelectorZoom: '',
            rangeSelectorFrom: '',
            rangeSelectorTo: '/',
            loading: "<?php _e('Loading...', LIKEBTN_I18N_DOMAIN) ?>",
            downloadJPEG: "<?php _e('Download JPEG image', LIKEBTN_I18N_DOMAIN) ?>",
            downloadPDF: "<?php _e('Download PDF document', LIKEBTN_I18N_DOMAIN) ?>",
            downloadPNG: "<?php _e('Download PNG image', LIKEBTN_I18N_DOMAIN) ?>",
            downloadSVG: "<?php _e('Download SVG vector image', LIKEBTN_I18N_DOMAIN) ?>",
            printChart: "<?php _e('Print chart', LIKEBTN_I18N_DOMAIN) ?>",
            months: ["<?php _e('January', LIKEBTN_I18N_DOMAIN) ?>", "<?php _e('February', LIKEBTN_I18N_DOMAIN) ?>", "<?php _e('March', LIKEBTN_I18N_DOMAIN) ?>", "<?php _e('April', LIKEBTN_I18N_DOMAIN) ?>", "<?php _e('May', LIKEBTN_I18N_DOMAIN) ?>", "<?php _e('June', LIKEBTN_I18N_DOMAIN) ?>", "<?php _e('July', LIKEBTN_I18N_DOMAIN) ?>", "<?php _e('August', LIKEBTN_I18N_DOMAIN) ?>", "<?php _e('September', LIKEBTN_I18N_DOMAIN) ?>", "<?php _e('October', LIKEBTN_I18N_DOMAIN) ?>", "<?php _e('November', LIKEBTN_I18N_DOMAIN) ?>", "<?php _e('December', LIKEBTN_I18N_DOMAIN) ?>"],
            numericSymbols: null,
            shortMonths: ["<?php _e('Jan', LIKEBTN_I18N_DOMAIN) ?>", "<?php _e('Feb', LIKEBTN_I18N_DOMAIN) ?>", "<?php _e('Mar', LIKEBTN_I18N_DOMAIN) ?>", "<?php _e('Apr', LIKEBTN_I18N_DOMAIN) ?>", "<?php _e('May', LIKEBTN_I18N_DOMAIN) ?>", "<?php _e('Jun', LIKEBTN_I18N_DOMAIN) ?>", "<?php _e('Jul', LIKEBTN_I18N_DOMAIN) ?>", "<?php _e('Aug', LIKEBTN_I18N_DOMAIN) ?>", "<?php _e('Sep', LIKEBTN_I18N_DOMAIN) ?>", "<?php _e('Oct', LIKEBTN_I18N_DOMAIN) ?>", "<?php _e('Nov', LIKEBTN_I18N_DOMAIN) ?>", "<?php _e('Dec', LIKEBTN_I18N_DOMAIN) ?>"],
            weekdays: ["<?php _e('Sunday', LIKEBTN_I18N_DOMAIN) ?>", "<?php _e('Monday', LIKEBTN_I18N_DOMAIN) ?>", "<?php _e('Tuesday', LIKEBTN_I18N_DOMAIN) ?>", "<?php _e('Wednesday', LIKEBTN_I18N_DOMAIN) ?>", "<?php _e('Thursday', LIKEBTN_I18N_DOMAIN) ?>", "<?php _e('Friday', LIKEBTN_I18N_DOMAIN) ?>", "<?php _e('Saturday', LIKEBTN_I18N_DOMAIN) ?>"],
            noData: "<?php _e('No votes found', LIKEBTN_I18N_DOMAIN) ?>"
        }

        var vg_chart;
        var drilldown_level = 0;
        var max_drilldown_level = 2;
        var data_by_level = [];
        var likebtn_vg;

        // Votes graph
        function likebtnVg(msg_title, plan)
        {
            likebtn_vg = jQuery("#likebtn_vg").clone();
            likebtn_vg.removeClass('hidden');
            likebtn_vg.removeAttr('id');

            likebtn_vg.dialog({
                resizable: false,
                autoOpen: false,
                modal: true,
                width: '90%',
                title: msg_title,
                draggable: false,
                show: 'fade',
                dialogClass: 'likebtn_dlg',
                open: function() {
                    jQuery('.ui-widget-overlay, .likebtn_dlg .likebtn-button-close').bind('click', function() {
                        likebtn_vg.dialog('close');
                    });
                },
                position: { 
                    my: "center", 
                    at: "center" 
                }
            });

            likebtn_vg.dialog('open');

            if (typeof(plan) != "undefined" && parseInt(plan) < plans.ultra) {
                jQuery(".likebtn-vgraph:visible:first").hide();
                likebtn_vg.find('.likebtn-vgraph-error:first').text("<?php echo strtr(__('Please upgrade at least to %plan% in order to user this feature.', LIKEBTN_I18N_DOMAIN), array('%plan%' => 'ULTRA')); ?>").show();
                return false;
            }

            jQuery.getJSON('<?php echo admin_url('admin-ajax.php') ?>?action=likebtn_vgaph&<?php echo $_SERVER['QUERY_STRING'] ?>', function(response) {

                if (!response.data) {
                    jQuery(".likebtn-vgraph:visible:first").hide();
                    likebtn_vg.find('.likebtn-vgraph-error:first').text("<?php echo _e('Error occured, please try again later.', LIKEBTN_I18N_DOMAIN) ?>").show();
                    return false;
                }

                if (response.error_message) {
                    jQuery(".likebtn-vgraph:visible:first").hide();
                    likebtn_vg.find('.likebtn-vgraph-error:first').text(response.error_message).show();
                    return false;
                }

                data_by_level[drilldown_level] = {
                    data: response.data,
                    extremes: null
                };

                Highcharts.setOptions({
                    lang: global_highcharts_lang
                });

                // Create the chart
                chart_options = {
                    chart: {
                        renderTo: jQuery(".likebtn-vgraph:visible:first")[0],
                        //type: 'StockChart',
                        events: {
                            load: function(event) {
                                hideChartElements();
                            }
                        }
                    },
                    rangeSelector : {
                        inputEnabled: false
                    },
                    rangeSelector: {
                        buttons: [],
                        inputDateFormat: '%d.%m.%Y %H:%M',
                        inputEditDateFormat: '%d.%m.%Y %H:%M',
                        inputBoxBorderColor: 'white'
                    },
                    /*title : {
                        text : '<?php _e('Votes graph', LIKEBTN_I18N_DOMAIN) ?>'
                    },*/
                    series : [
                    {
                        name: "<?php _e('Total Votes', LIKEBTN_I18N_DOMAIN) ?>", 
                        data: response.data.t, 
                        color: "#337ab7",
                        marker: {
                            enabled: true,
                            radius: 1,
                            symbol: "circle"
                        }
                    },
                    {
                        name: "<?php _e('Likes', LIKEBTN_I18N_DOMAIN) ?>",
                        data: response.data.l,
                        color: "#5cb85c",
                        marker: {
                            enabled: true,
                            radius: 1,
                            symbol: "circle"
                        }
                    },
                    {
                        name: "<?php _e('Dislikes', LIKEBTN_I18N_DOMAIN) ?>",
                        data: response.data.d,
                        color: "#f0ad4e",
                        marker: {
                            enabled: true,
                            radius: 1,
                            symbol: "circle"
                        }
                    }],
                    plotOptions: {
                        line: {
                            cursor: 'pointer',
                            point: {
                                events: {
                                    click: function() {
                                        if (drilldown_level < max_drilldown_level) { // drill down
                                            drillDown(vg_chart, drilldown_level+1, this.x);
                                        }
                                    }
                                }
                            }
                        }
                    },
                    exporting: {
                        enabled: true,
                        buttons: {
                            'customDrillUpButton': {
                                _id: 'customDrillUpButton',
                                x: 0,
                                y: 30,
                                align: 'left',
                                text: '◁ <?php _e('Back', LIKEBTN_I18N_DOMAIN) ?>',
                                onclick: function() {
                                    drillUp(vg_chart);
                                }
                            }
                        }
                    },
                    tooltip: {
                        useHTML: true,
                        footerFormat: '<sub><?php _e('Click to view details', LIKEBTN_I18N_DOMAIN) ?></sub>'
                    }
                };
                vg_chart = new Highcharts.StockChart(chart_options);
            });
        }

        // Load chart data from server
        function drillDown(chart, level, timestamp)
        {
            chart.showLoading();

            // Load data from server
            jQuery.getJSON('<?php echo admin_url('admin-ajax.php') ?>?action=likebtn_vgaph&level='+level+'&timestamp='+timestamp+'&<?php echo $_SERVER['QUERY_STRING'] ?>', function(response) {

                if (response.error_message) {
                    jQuery(".likebtn-vgraph:visible:first").hide().next().html(response.error_message).removeClass('hidden');
                    return false;
                }

                // Remember extrimes
                data_by_level[drilldown_level].extremes = chart.xAxis[0].getExtremes();

                drilldown_level++;

                data_by_level[drilldown_level] = {
                    data: cloneObject(response.data),
                    extremes: null
                };

                setChart(chart, response.data);
                chart.hideLoading();
            });
        }

        // Drill up
        function drillUp(chart) {
            chart.showLoading();
            // Load stored data
            drilldown_level--;

            setChart(chart, data_by_level[drilldown_level].data, data_by_level[drilldown_level].extremes);
            chart.hideLoading();
        }

        // Set chart data and redraw
        function setChart(chart, data_list, extremes) {
            var data_exists = false;

            // Back button
            if (drilldown_level > 0) {
                // Show back button
                jQuery('.highcharts-container:visible:first .highcharts-button:eq(1), .highcharts-button:eq(3)').show();
            } else {
                // Hide back button
                jQuery('.highcharts-container:visible:first .highcharts-button:eq(1), .highcharts-button:eq(3)').hide();
            }

            if (drilldown_level < max_drilldown_level) {
                chart.options.tooltip.footerFormat = '<sub><?php _e('Click to view details', LIKEBTN_I18N_DOMAIN) ?></sub>';
                chart.options.plotOptions.line.cursor = 'pointer';
            } else {
                chart.options.tooltip.footerFormat = '';
                chart.options.plotOptions.line.cursor = 'normal';
            }

            var types = ['t', 'l', 'd'];
            for (i in types) {
                chart.series[i].setData(data_list[types[i]], true);
                chart.series[i].update({
                    marker: {
                        enabled: (drilldown_level > 0)
                    }
                });
                if (data_list[types[i]].length) {
                    data_exists = true;
                }
            }
            // Zoom - show all
            if (extremes) {
                chart.xAxis[0].setExtremes(
                    extremes.min,
                    extremes.max
                );
            } else if (data_exists) {
                chart.xAxis[0].setExtremes(null, null);
            }
        }

        function hideChartElements() {
            jQuery('.highcharts-container:visible:first .highcharts-button:eq(1)').hide();
            jQuery('.highcharts-container:visible:first .highcharts-button:eq(3)').hide();
        };
    </script>

    <?php

    _likebtn_admin_footer();
}

// get SQL query for retrieving votes
function _likebtn_get_votes_sql($prefix, $query_where, $query_orderby, $query_limit, $query_select, $query_join = '')
{
    $query = "
         SELECT {$query_select}
         FROM {$prefix}".LIKEBTN_TABLE_VOTE." v
         LEFT JOIN {$prefix}".LIKEBTN_TABLE_ITEM." i on i.identifier = v.identifier 
         {$query_join}
         WHERE
            1 = 1
            {$query_where}
         {$query_orderby}
         {$query_limit}";

    return $query;
}
