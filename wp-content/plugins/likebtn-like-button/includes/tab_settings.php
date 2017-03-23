<?php

// Settings tab
function likebtn_admin_settings() {

    //global $likebtn_plans;
    global $likebtn_sync_intervals;

    wp_enqueue_script('likebtn-jquery-ui', _likebtn_get_public_url().'js/jquery/jquery-ui/jquery-ui.js', array('jquery'), LIKEBTN_VERSION);
    wp_enqueue_style('likebtn-jquery-ui-css', _likebtn_get_public_url().'css/jquery/jquery-ui/jquery-ui.css', false, LIKEBTN_VERSION, 'all');
    wp_enqueue_script('likebtn-durationpicker', _likebtn_get_public_url().'js/jquery/jquery.ui.durationPicker.js', array('jquery', 'likebtn-jquery-ui'), LIKEBTN_VERSION);
    wp_enqueue_style('likebtn-durationpicker', _likebtn_get_public_url().'css/jquery/jquery.ui.durationPicker.css', array(), LIKEBTN_VERSION, 'all');

    // reset sync interval
    if (!get_option('likebtn_account_email') || !get_option('likebtn_account_api_key') || !get_option('likebtn_site_id')) {
        update_option('likebtn_sync_inerval', '');
    }

    // If account data has changed, refresh the plan
    $account_data_hash = md5(get_option('likebtn_account_email').get_option('likebtn_account_api_key').get_option('likebtn_site_id'));

    if (!get_option('likebtn_account_data_hash') || $account_data_hash != get_option('likebtn_account_data_hash')) {
        update_option('likebtn_account_data_hash', $account_data_hash);
        // run plan sunchronization
        require_once(dirname(__FILE__) . '/../likebtn_like_button.class.php');
        $likebtn = new LikeBtnLikeButton();
        $likebtn->syncPlan();
        // run synchronization
        $likebtn->syncVotes();
    }

    // Save IP vote interval if changed
    $ipvi = get_option('likebtn_ipvi');
    if ($ipvi === '' || $ipvi === NULL) {
        // Not initicalized yet
        $ipvi = LIKEBTN_IP_VOTE_INTERVAL;
    }
    if (_likebtn_is_stat_enabled() && $ipvi != get_option('likebtn_ipvi_hash')) {
        require_once(dirname(__FILE__) . '/../likebtn_like_button.class.php');
        $likebtn = new LikeBtnLikeButton();
        $ipvi_result = $likebtn->setIpvi($ipvi);

        if ($ipvi_result['result'] == 'success') {
            update_option('likebtn_ipvi_hash', $ipvi);
        } else {
            _likebtn_add_notice(array(
                'msg' => __('Error occured saving IP vote interval: '.$ipvi_result['message'], LIKEBTN_I18N_DOMAIN),
                'class' => 'error'
            ));
        }
    }

    $export_config = likebtn_get_config();

    likebtn_admin_header();
    ?>
    <?php /*<script type="text/javascript">
        jQuery(document).ready(function() {
            planChange(jQuery(":input[name='likebtn_plan']").val());
        });
    </script>*/ ?>
    <script type="text/javascript">
        var likebtn_msg_error = '<?php _e("Error occured:", LIKEBTN_I18N_DOMAIN); ?><br/>● <?php _e('Make sure that <a href="https://wordpress.org/support/topic/howto-disable-php-errors-using-htaccess-on-a-shared-hosting" target="_blank">displaying errors is disabled</a>', LIKEBTN_I18N_DOMAIN); ?><br/>● <?php _e("Disable WP HTTP Compression plugin if you have it enabled", LIKEBTN_I18N_DOMAIN); ?>';
        scriptSettings();
    </script>
    <div class="likebtn_subpage">
        <form method="post" action="options.php" novalidate="novalidate" autocomplete="off">
            <?php settings_fields('likebtn_settings'); ?>
            <?php /*
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><label><?php _e('Current plan', LIKEBTN_I18N_DOMAIN); ?></label></th>
                    <td>
                        <select name="likebtn_plan" onChange="planChange(this.value)">
                            <?php foreach ($likebtn_plans as $plan_id => $plan_name): ?>
                                <option value="<?php echo $plan_id; ?>" <?php if (get_option('likebtn_plan') == $plan_id): ?>selected="selected"<?php endif ?> ><?php echo $plan_name; ?></option>
                            <?php endforeach ?>
                        </select>
                        <p class="description">
                            <?php _e('Premium features are available only if your website is upgraded to the corresponding tariff plan (PLUS, PRO, VIP, ULTRA). Keep in mind that only websites upgraded to <a href="http://likebtn.com/en/#plans_pricing" target="_blank">PLUS</a> plan or higher are allowed to display more than 1 Like Button per page.', LIKEBTN_I18N_DOMAIN) ?><br/>
                            <a href="javascript:toggleToUpgrade();void(0);"><?php _e('To upgrade your website...', LIKEBTN_I18N_DOMAIN) ?></a>
                            <ol id="likebtn_to_upgrade" class="hidden">
                                <li><?php _e('Register on <a href="http://likebtn.com/en/customer.php/register/" target="_blank">LikeBtn.com</a>', LIKEBTN_I18N_DOMAIN) ?></li>
                                <li><?php _e('Add your website to your account and activate it on <a href="http://likebtn.com/en/customer.php/websites" target="_blank">Websites page</a>', LIKEBTN_I18N_DOMAIN) ?></li>
                                <li><?php _e('Upgrade your website to the desired plan.', LIKEBTN_I18N_DOMAIN) ?></li>
                            </ol>
                        </p>
                    </td>
                </tr>
            </table>
            */ ?>
            <div class="postbox likebtn_postbox">
                <h3><?php _e('Account Details', LIKEBTN_I18N_DOMAIN); ?></h3>
                <div class="inside">
                    <p>
                        <?php /*_e('To get your account data:', LIKEBTN_I18N_DOMAIN); */ ?>
                        <ol>
                            <li>
                                <?php echo strtr(
                                    __('Register on <a href="%url_register%">LikeBtn.com</a>', LIKEBTN_I18N_DOMAIN), 
                                    array('%url_register%'=>"javascript:likebtnPopup('".__('http://likebtn.com/en/customer.php/register/', LIKEBTN_I18N_DOMAIN)."');void(0)")); 
                                ?>
                            </li>
                            <li>
                                <?php if (!is_multisite()): ?>
                                    <?php echo strtr(
                                        __('Add your website to your account on <a href="%url_websites%">Websites</a> page.', LIKEBTN_I18N_DOMAIN), 
                                        array('%url_websites%'=>"javascript:likebtnPopup('".__('http://likebtn.com/en/customer.php/websites', LIKEBTN_I18N_DOMAIN)."');void(0)")); 
                                    ?>
                                <?php else: ?>
                                    <?php echo strtr(
                                        __('Add each website of the network as a separate website to your account on <a href="%url_websites%">Websites</a> page. If using path-based (sub-directories) multisite network please make sure to specify the "Subdirectory" when adding each website. Also see <a href="%bulk_discount%">bulk discount pricing</a>.', LIKEBTN_I18N_DOMAIN), 
                                        array('%url_websites%'=>"javascript:likebtnPopup('".__('http://likebtn.com/en/customer.php/websites', LIKEBTN_I18N_DOMAIN)."');void(0)", '%bulk_discount%'=>"javascript:likebtnPopup('".__('https://likebtn.com/en/', LIKEBTN_I18N_DOMAIN)."pricing#bulk_discount_pricing');void(0)")); 
                                    ?>
                                    
                                <?php endif ?>
                            </li>
                            <li>
                                <?php echo __('Check and save data.', LIKEBTN_I18N_DOMAIN); ?>
                            </li>
                        </ol>
                    </p>
                    <input class="button-primary likebtn_button_green" type="button" value="<?php _e('Get Account Data', LIKEBTN_I18N_DOMAIN); ?>" onclick="likebtnGetAccountData('<?php _e('http://likebtn.com/en/customer.php/register/', LIKEBTN_I18N_DOMAIN) ?>')" />
                    <?php /* For add_domain */ ?>
                    <?php if (get_option('likebtn_acc_data_correct') != '1'): ?>
                        <div style="display:none">
                            <?php echo _likebtn_get_markup(LIKEBTN_ENTITY_POST, 'demo', array(), '', true, true, true) ?>
                        </div>
                    <?php endif ?>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><label><?php _e('E-mail', LIKEBTN_I18N_DOMAIN); ?></label></th>
                            <td>
                                <input type="text" name="likebtn_account_email" value="<?php echo htmlspecialchars(get_option('likebtn_account_email')) ?>" onkeyup="accountChange(this)" class="likebtn_account likebtn_input" id="likebtn_account_email_input"/><br/>
                                <p class="description"><?php _e('Your LikeBtn.com account email. Can be found on <a href="http://likebtn.com/en/customer.php/profile/edit" target="_blank">Profile</a> page', LIKEBTN_I18N_DOMAIN) ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><label><?php _e('API key', LIKEBTN_I18N_DOMAIN); ?></label></th>
                            <td>
                                <input type="text" name="likebtn_account_api_key" value="<?php echo htmlspecialchars(get_option('likebtn_account_api_key')) ?>" onkeyup="accountChange(this)" class="likebtn_account likebtn_input" id="likebtn_account_api_key_input" maxlength="32" /><br/>
                                <p class="description"><?php _e('Your website API key on LikeBtn.com. Can be obtained on <a href="http://likebtn.com/en/customer.php/websites" target="_blank">Websites</a> page', LIKEBTN_I18N_DOMAIN) ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><label><?php _e('Site ID', LIKEBTN_I18N_DOMAIN); ?></label></th>
                            <td>
                                <input type="text" name="likebtn_site_id" value="<?php echo htmlspecialchars(get_option('likebtn_site_id')) ?>" class="likebtn_input" id="likebtn_site_id_input" maxlength="24" /><br/>
                                <?php /*<span class="description"><?php _e('Enter Site ID in following cases:', LIKEBTN_I18N_DOMAIN) ?><br/>
                                ● <?php _e('Your site is local – located on a local server and is available from your local network only and NOT available from the Internet.', LIKEBTN_I18N_DOMAIN) ?><br/>
                                ● <?php _e('Your site is path-based – one of sites located in different subdirectories of one domain and you want to have statistics separate from other sites.', LIKEBTN_I18N_DOMAIN) ?><br/><br/>*/ ?>
                                <p class="description">
                                    <?php _e('Your Site ID on LikeBtn.com. Can be obtained on <a href="http://likebtn.com/en/customer.php/websites" target="_blank">Websites</a> page.', LIKEBTN_I18N_DOMAIN) ?> <?php _e('If your website has multiple addresses or you are developing a website on a local server and planning to move it to a live domain, you can add domains to the website <a href="http://likebtn.com/en/customer.php/websites" target="_blank">here</a>.', LIKEBTN_I18N_DOMAIN) ?>
                                </p>
                            </td>
                        </tr>
                        <tr valign="middle">
                            <th scope="row">&nbsp;</th>
                            <td class="likebtn_mid_row">
                                <input class="button-primary likebtn_s_btn" type="button" value="<?php _e('Check Account Data', LIKEBTN_I18N_DOMAIN); ?>" onclick="checkAccount('<?php echo _likebtn_get_public_url() ?>img/ajax_loader.gif')" /> &nbsp;<strong class="likebtn_check_account_container"></strong>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>
                <input class="button-primary" type="submit" name="Save" value="<?php _e('Save All Changes', LIKEBTN_I18N_DOMAIN); ?>" />
            </p>
            <br/>

            <?php if (get_option('likebtn_plan') < LIKEBTN_PLAN_PRO): ?>
                <strong class="likebtn_error">
                    <?php echo strtr(
                        __('Website tariff plan does not allow to synchronize likes into your database – <a href="%url_upgrade%">upgrade</a> your website to PRO or higher plan.', LIKEBTN_I18N_DOMAIN), 
                        array('%url_upgrade%'=>"javascript:likebtnPopup('".__('https://likebtn.com/en/pricing', LIKEBTN_I18N_DOMAIN)."');void(0)")); 
                    ?>
                </strong>
                <br/><br/>
            <?php endif ?>

            <div class="postbox likebtn_postbox" id="synchronization">
                <h3><?php _e('Synchronization', LIKEBTN_I18N_DOMAIN); ?> <i class="premium_feature" title="PRO / VIP / ULTRA"></i></h3>
                <div class="inside">
                    <p class="description">
                        <?php _e('Enable synchronization of likes from LikeBtn.com into your database to:', LIKEBTN_I18N_DOMAIN); ?><br/>
                        &nbsp;&nbsp;● <?php _e('View statistics on Statistics tab.', LIKEBTN_I18N_DOMAIN); ?><br/>
                        &nbsp;&nbsp;● <?php _e('Sort content by likes.', LIKEBTN_I18N_DOMAIN); ?><br/>
                        &nbsp;&nbsp;● <?php _e('Use most liked content widget and shortcode.', LIKEBTN_I18N_DOMAIN); ?><br/>
                    </p>
                    <table class="form-table">
                        <tr valign="middle">
                            <th scope="row"><label><?php _e('Synchronization status', LIKEBTN_I18N_DOMAIN); ?></label>
                                <?php /*<i class="likebtn_help" title="​<?php _e('Votes are stored in LikeBtn.com system.', LIKEBTN_I18N_DOMAIN); ?> <?php _e('Choose time interval of fetching votes from LikeBtn.com into your database.', LIKEBTN_I18N_DOMAIN); ?>">&nbsp;</i>*/ ?>
                            </th>
                            <td class="likebtn_sync_cntr <?php if (_likebtn_is_stat_enabled()): ?>likebtn_sync_ena_flag<?php else: ?>likebtn_sync_dis_flag<?php endif ?> likebtn_mid_row">
                                <?php /*<select name="likebtn_sync_inerval" <?php disabled((!get_option('likebtn_account_email') || !get_option('likebtn_account_api_key'))); ?> id="likebtn_sync_inerval_input">
                                    <option value="" <?php selected('', get_option('likebtn_sync_inerval')); ?> ><?php _e('Synchronization of votes into local database is disabled', LIKEBTN_I18N_DOMAIN) ?></option>
                                    <?php foreach ($likebtn_sync_intervals as $sync_interval): ?>
                                        <option value="<?php echo $sync_interval; ?>" <?php selected($sync_interval, get_option('likebtn_sync_inerval')); ?> ><?php echo $sync_interval; ?> <?php _e('min', LIKEBTN_I18N_DOMAIN); ?></option>
                                    <?php endforeach ?>
                                </select>
                                <br/><br/>*/?>
                                <input type="hidden" name="likebtn_sync_inerval" value="<?php echo get_option('likebtn_sync_inerval'); ?>" />
                                <p>
                                    <strong class="likebtn_success likebtn_sync_ena"><?php _e('Enabled', LIKEBTN_I18N_DOMAIN); ?></strong>
                                    <strong class="likebtn_error likebtn_sync_dis"><?php _e('Disabled', LIKEBTN_I18N_DOMAIN); ?></strong> 
                                </p>
                                <?php if (!_likebtn_is_stat_enabled()): ?>
                                    <p class="description likebtn_sync_dis"><?php _e('Please ugrade at least to PRO, enter, check and save account data above', LIKEBTN_I18N_DOMAIN); ?></p>
                                <?php endif ?>
                                <div <?php if (!get_option('likebtn_account_email') || !get_option('likebtn_account_api_key') || !get_option('likebtn_site_id')): ?>style="display:none"<?php endif ?>>
                                    <br/>
                                    <input class="button-primary likebtn_button_green likebtn_sync_dis" type="button" value="<?php _e('Enable Sync', LIKEBTN_I18N_DOMAIN); ?>" onclick="testSync('<?php echo _likebtn_get_public_url() ?>img/ajax_loader.gif')" />

                                    <input class="button-primary likebtn_sync_ena likebtn_s_btn" type="button" value="<?php _e('Test Sync', LIKEBTN_I18N_DOMAIN); ?>" onclick="testSync('<?php echo _likebtn_get_public_url() ?>img/ajax_loader.gif')" /> 
                                    &nbsp;<strong class="likebtn_test_sync_container"></strong>
                                    
                                    <div class="liketbtn_mansync_wr" style="display:none">
                                        <br/><br/>
                                        <input class="button-secondary likebtn_ttip" type="button" value="<?php _e('Run Full Sync Manually', LIKEBTN_I18N_DOMAIN); ?>" onclick="manualSync('<?php echo _likebtn_get_public_url() ?>img/ajax_loader.gif')" title="<?php _e("ATTENTION: Use this feature carefully since full synchronization may affect your website performance. If you don't experience any problems with likes synchronization better to avoid using this feature.", LIKEBTN_I18N_DOMAIN) ?>" /> &nbsp;<strong class="likebtn_manual_sync_container"><img src="<?php echo _likebtn_get_public_url() ?>img/ajax_loader.gif" class="hidden"/></strong>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><label><?php _e('Diagnostics', LIKEBTN_I18N_DOMAIN); ?></label>
                                <i class="likebtn_help" title="​<?php _e('Check if your server configuration satisfies the Like button plugin requirements.', LIKEBTN_I18N_DOMAIN); ?>">&nbsp;</i>
                            </th>
                            <td class="likebtn_mid_row">
                                <input class="button-secondary likebtn_ttip" type="button" value="<?php _e('Check the System', LIKEBTN_I18N_DOMAIN); ?>" onclick="systemCheck('<?php echo _likebtn_get_public_url() ?>img/ajax_loader.gif')" /> &nbsp;<strong class="likebtn_sc_container"></strong>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>
                <input class="button-primary" type="submit" name="Save" value="<?php _e('Save All Changes', LIKEBTN_I18N_DOMAIN); ?>" />
            </p>
            <br/>

            <div class="postbox likebtn_postbox" id="ip_vote_interval">
                <h3><?php _e('IP Address Vote Interval', LIKEBTN_I18N_DOMAIN); ?> <i class="premium_feature" title="ULTRA"></i></h3>
                <div class="inside">
                    <p class="description">
                        <?php _e('If you\'ve set up voters identification by Username for a particular post type on Buttons tab, this IP address vote interval option is ignored for that post type.' , LIKEBTN_I18N_DOMAIN); ?><br/>
                    </p>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><label><?php _e('Interval', LIKEBTN_I18N_DOMAIN); ?></label>
                            </th>
                            <td>
                                <?php
                                    $ipvi_custom = false;
                                    if ((int)$ipvi != 0 && (int)$ipvi != LIKEBTN_MAX_IP_VOTE_INTERVAL) {
                                        $ipvi_custom = true;   
                                    }
                                ?>
                                <select class="disabled" onchange="ipviSelect(this)" onclick="ipviSelect(this)" disabled="disabled" id="ipvi_select">
                                    <option value="<?php echo LIKEBTN_MAX_IP_VOTE_INTERVAL; ?>" <?php selected(LIKEBTN_MAX_IP_VOTE_INTERVAL, $ipvi); ?> ><?php _e('Allow ONE vote per IP address', LIKEBTN_I18N_DOMAIN) ?></option>
                                    <option value="0" <?php selected(0, (int)$ipvi); ?> ><?php _e('Allow UNLIMITED votes from the same IP address', LIKEBTN_I18N_DOMAIN) ?></option>
                                    <option value="-1" <?php if ($ipvi_custom): ?>selected="selected"<?php endif ?> ><?php _e('Custom IP address vote interval', LIKEBTN_I18N_DOMAIN) ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr valign="top" class="<?php if (!$ipvi_custom): ?>hidden<?php endif ?> ipvi_custom">
                            <th scope="row">&nbsp;</label>
                            </th>
                            <td>
                                <div id="ipvi_duration"></div>
                            </td>
                        </tr>
                        <tr valign="top" class="<?php if (!$ipvi_custom): ?>hidden<?php endif ?> ipvi_custom">
                            <th scope="row">&nbsp;</label>
                            </th>
                            <td>
                                <input type="number" min="1" max="31557599" value="<?php echo (int)$ipvi ?>" onkeyup="ipviChanged(this)" class="disabled" id="ipvi_secs" readonly="readonly" /> <strong><?php _e('total seconds', LIKEBTN_I18N_DOMAIN); ?></strong>
                                <input type="hidden" name="likebtn_ipvi" id="ipvi_secs_hidden" value="<?php echo (int)$ipvi ?>" />
                            </td>
                        </tr>
                        <tr valign="top" id="likebtn_ipvi_change">
                            <th scope="row"></th>
                            <td class="likebtn_mid_row">
                                <input class="button-secondary" type="button" value="<?php _e('Change', LIKEBTN_I18N_DOMAIN); ?>" onclick="ipviChange('<?php echo _likebtn_get_public_url() ?>img/ajax_loader.gif')" /> &nbsp;<strong class="likebtn_ipvi_change_container"></strong>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>
                <input class="button-primary" type="submit" name="Save" value="<?php _e('Save All Changes', LIKEBTN_I18N_DOMAIN); ?>" />
            </p>
            <br/>

            <div class="postbox likebtn_postbox">
                <h3><?php _e('Sorting', LIKEBTN_I18N_DOMAIN); ?></h3>
                <div class="inside">
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><label><?php _e('bbPress replies sorting', LIKEBTN_I18N_DOMAIN); ?></label>
                            </th>
                            <td>
                                <select name="likebtn_bbp_replies_sort">
                                    <option value="" <?php selected('', get_option('likebtn_bbp_replies_sort')); ?> ><?php _e('Default', LIKEBTN_I18N_DOMAIN) ?></option>
                                    <option value="<?php echo LIKEBTN_META_KEY_LIKES; ?>" <?php selected(LIKEBTN_META_KEY_LIKES, get_option('likebtn_bbp_replies_sort')); ?> ><?php _e('Likes', LIKEBTN_I18N_DOMAIN) ?></option>
                                    <option value="<?php echo LIKEBTN_META_KEY_DISLIKES; ?>" <?php selected(LIKEBTN_META_KEY_DISLIKES, get_option('likebtn_bbp_replies_sort')); ?> ><?php _e('Dislikes', LIKEBTN_I18N_DOMAIN) ?></option>
                                    <option value="<?php echo LIKEBTN_META_KEY_LIKES_MINUS_DISLIKES; ?>" <?php selected(LIKEBTN_META_KEY_LIKES_MINUS_DISLIKES, get_option('likebtn_bbp_replies_sort')); ?> ><?php _e('Likes minus dislikes', LIKEBTN_I18N_DOMAIN) ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><label><?php _e('BuddyPress activities sorting', LIKEBTN_I18N_DOMAIN); ?></label>
                            </th>
                            <td>
                                <select name="likebtn_bp_act_sort">
                                    <option value="" <?php selected('', get_option('likebtn_bbp_replies_sort')); ?> ><?php _e('Default', LIKEBTN_I18N_DOMAIN) ?></option>
                                    <option value="<?php echo LIKEBTN_META_KEY_LIKES; ?>" <?php selected(LIKEBTN_META_KEY_LIKES, get_option('likebtn_bp_act_sort')); ?> ><?php _e('Likes', LIKEBTN_I18N_DOMAIN) ?></option>
                                    <option value="<?php echo LIKEBTN_META_KEY_DISLIKES; ?>" <?php selected(LIKEBTN_META_KEY_DISLIKES, get_option('likebtn_bp_act_sort')); ?> ><?php _e('Dislikes', LIKEBTN_I18N_DOMAIN) ?></option>
                                    <option value="<?php echo LIKEBTN_META_KEY_LIKES_MINUS_DISLIKES; ?>" <?php selected(LIKEBTN_META_KEY_LIKES_MINUS_DISLIKES, get_option('likebtn_bp_act_sort')); ?> ><?php _e('Likes minus dislikes', LIKEBTN_I18N_DOMAIN) ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><a href="<?php _e('http://likebtn.com/en/', LIKEBTN_I18N_DOMAIN) ?>wordpress-like-button-plugin#sort_posts_by_likes" target="_blank"><?php _e('How to sort other types of content?', LIKEBTN_I18N_DOMAIN) ?></a></td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>
                <input class="button-primary" type="submit" name="Save" value="<?php _e('Save All Changes', LIKEBTN_I18N_DOMAIN); ?>" />
            </p>
            <br/>

            <div class="postbox likebtn_postbox">
                <h3><?php _e('Miscellaneous', LIKEBTN_I18N_DOMAIN); ?></h3>
                <div class="inside">
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><label><?php _e('CloudFlare enabled', LIKEBTN_I18N_DOMAIN); ?></label>
                            </th>
                            <td>
                                <p class="description">
                                    <input type="checkbox" name="likebtn_cf" value="1" <?php checked('1', get_option('likebtn_cf')); ?> /> 
                                    <?php _e('Check if your website is powered by CloudFlare.com for proper visitors\' IP detection.', LIKEBTN_I18N_DOMAIN); ?>
                                </p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><label><?php _e('Custom CSS', LIKEBTN_I18N_DOMAIN); ?></label>
                            </th>
                            <td>
                                <textarea name="likebtn_css" class="likebtn_input" rows="4"><?php echo htmlspecialchars(get_option('likebtn_css')); ?></textarea>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><label><?php _e('Custom JavaScript', LIKEBTN_I18N_DOMAIN); ?></label>
                            </th>
                            <td>
                                <textarea name="likebtn_js" class="likebtn_input" rows="4"><?php echo htmlspecialchars(get_option('likebtn_js')); ?></textarea>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <?php /*<div class="postbox likebtn_account">
                <h3><?php _e('Local domain', LIKEBTN_I18N_DOMAIN); ?></h3>
                <div class="inside">
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">&nbsp;</th>
                            <td> ?>
                                <input type="hidden" name="likebtn_local_domain" value="<?php echo get_option('likebtn_local_domain') ?>" size="60" />
                                <?php
                                <br/>
                                <strong class="description"><?php _e('Example:', LIKEBTN_I18N_DOMAIN) ?> localdomain!50f358d30acf358d30ac000001</strong>
                                <br/><br/>
                                <span class="description"><?php _e('Specify it if your website is located on a local server and is available from your local network only and NOT available from the Internet. You can find the domain on your <a href="http://likebtn.com/en/customer.php/websites" target="_blank">Websites</a> page after adding your local website to the panel. See <a href="http://likebtn.com/en/faq#local_domain" target="_blank">FAQ</a> for more details.', LIKEBTN_I18N_DOMAIN) ?></span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>*/ ?>
            <?php /*<div class="postbox likebtn_account">
                <h3><?php _e('Website subdirectory', LIKEBTN_I18N_DOMAIN); ?></h3>
                <div class="inside">
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">&nbsp;</th>
                            <td> ?>
                                <input type="hidden" name="likebtn_subdirectory" value="<?php echo get_option('likebtn_subdirectory') ?>" size="60" />
                                <?php
                                <br/>
                                <strong class="description"><?php _e('Example:', LIKEBTN_I18N_DOMAIN) ?> /subdirectory/</strong>
                                <br/><br/>
                                <span class="description"><?php _e('If your website is one of websites located in different subdirectories of one domain and you want to have a separate from other websites on this domain statistics, enter subdirectory (for example /subdirectory/). Required for path-based <a href="http://codex.wordpress.org/Create_A_Network" target="_blank">multisite networks</a> in which on-demand sites use paths.', LIKEBTN_I18N_DOMAIN) ?></span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>*/ ?>
            <p>
                <input class="button-primary" type="submit" name="Save" value="<?php _e('Save All Changes', LIKEBTN_I18N_DOMAIN); ?>" />
            </p>

        </form>
        <br/>

        <div class="postbox likebtn_postbox">
            <h3><?php _e('Configuration Import & Export', LIKEBTN_I18N_DOMAIN); ?></h3>
            <div class="inside">
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><label><?php _e('Export configuration', LIKEBTN_I18N_DOMAIN); ?></label>
                        </th>
                        <td>
                            <p class="description">
                                <?php _e('This box contains all the Like Button configuration encoded as a string so you can easily copy it.'); ?>
                            </p>
                            <textarea class="likebtn_input" rows="4"><?php echo htmlspecialchars($export_config); ?></textarea>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label><?php _e('Import configuration', LIKEBTN_I18N_DOMAIN); ?></label>
                        </th>
                        <td>
                            <form method="post" action="" onsubmit="return importSubmit('<?php _e('Current settings and buttons configuration will be overwritten. Please type IMPORT in order to import configuration:', LIKEBTN_I18N_DOMAIN); ?>')">
                                <p class="description">
                                    <?php _e('Paste your Like Button configuration here and the plugin will load it into the database.'); ?>
                                </p>
                                <textarea class="likebtn_input" rows="4" name="likebtn_import_config"></textarea>
                                <p class="notice update-nag"> 
                                    <?php _e('On importing current settings and buttons configuration will be overwritten. Votes and statistics will be preserved.', LIKEBTN_I18N_DOMAIN); ?>
                                </p>
                                <input class="button-primary" type="submit" value="<?php _e('Import', LIKEBTN_I18N_DOMAIN); ?>"/>
                            </form>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <br/>
        <div class="postbox likebtn_postbox">
            <h3><?php _e('Resetting', LIKEBTN_I18N_DOMAIN); ?></h3>
            <div class="inside">
                <table class="form-table">
                    <tr valign="top">
                        <td>
                            <form method="post" action="" id="likebtn_fr_form">
                                <input type="hidden" name="likebtn_full_reset" value="" />
                                <input type="button" class="button-secondary likebtn_ttip" onclick="likebtnFullReset('<?php _e("Votes and stats will be removed permanently and can not be restored. If you want to continue please enter RESET:", LIKEBTN_I18N_DOMAIN) ?>')" value="<?php _e('Reset Votes & Stats', LIKEBTN_I18N_DOMAIN); ?>" title="<?php _e('Remove ALL votes and stats', LIKEBTN_I18N_DOMAIN); ?>">
                            </form>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

    </div>
    <?php

    _likebtn_admin_footer();
}
