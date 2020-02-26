
<div class="wrap">
    <h1>Gift cards settings </h1>


    <?php  $g_option = get_option("ws_settings"); ?>
    <form method="post" action="options.php">
        <?php settings_fields( 'ws-settings-group' ); ?>
        <?php do_settings_sections( 'ws-settings-group' ); ?>
        <h3>General</h3>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Enable / diable </th>
                <?php $enable = !empty($g_option) && array_key_exists("enable",$g_option)  ? $g_option["enable"] : '' ?>

                <td>
                    <div id="toggles">

                        <input  id="checkboxEnable"  type="checkbox" class="ios-toggle"  name="ws_settings[enable]" value="1" <?= $enable  ? "checked": "" ?> />
                        <label  for="checkboxEnable" class="checkbox-label" data-off="off" data-on="on" ></label>
                    </div>
                </td>
            </tr>



            <tr valign="top">
                <th scope="row">Email notification</th>
                <?php $email = !empty($g_option) && array_key_exists("email",$g_option)  ? $g_option["email"] : '' ?>

                <td> <label for="">Separate multiple emails by comma E.g: admin@ws.com, info@ws.com</label><br>
                    <input type="text" style="width: 500px;" name="ws_settings[email]" value="<?php echo esc_attr( $email ); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Price threshold for Wordlpay</th>
                <?php $price = !empty($g_option) && array_key_exists("price",$g_option) ? $g_option["price"] : '' ?>
                <td><input type="text" name="ws_settings[price]" value="<?php echo esc_attr( $price ); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">AVS value</th>
                <?php $avs = !empty($g_option) && array_key_exists("avs",$g_option) ? $g_option["avs"] : '' ?>
                <td><input type="text" name="ws_settings[avs]" value="<?php echo esc_attr( $avs ); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Envelope product id</th>
                <?php $envelope = !empty($g_option) && array_key_exists("envelope",$g_option) ? $g_option["envelope"] : '' ?>
                <td><input type="text" name="ws_settings[envelope]" value="<?php echo esc_attr( $envelope ); ?>" /></td>
            </tr>

        </table>

        <h3>Sftp</h3>

        <table class="form-table">
            <tr valign="top">
                <th scope="row">Ip address</th>
                <?php $ip = !empty($g_option) && array_key_exists("ip",$g_option)  ? $g_option["ip"] : '' ?>
                <td><input type="text" name="ws_settings[ip]" value="<?php echo esc_attr( $ip ); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Username</th>
                <?php $username = !empty($g_option) && array_key_exists("username",$g_option)  ? $g_option["username"] : '' ?>
                <td><input type="text" name="ws_settings[username]" value="<?php echo esc_attr( $username ); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Password</th>
                <?php $password = !empty($g_option) && array_key_exists("password",$g_option)  ? $g_option["password"] : '' ?>
                <td><input type="password" name="ws_settings[password]" value="<?php echo esc_attr( $password  ); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Running time</th>
                <?php $time = !empty($g_option) && array_key_exists("time",$g_option)  ? $g_option["time"] : '' ?>
                <td><input type="text" id="datetimepickerA" name="ws_settings[time]" value="<?php echo esc_attr( $time ); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Reset cron if time changed</th>
                <td><a href="<?=  site_url().'/wp-admin/admin.php?page=ws-gift-cards&cron=rest'  ?>" style="color:red;">Reset cron</a></td>
            </tr>



        </table>




        <?php submit_button(); ?>
    </form>
    <table class="info" >
        <tr valign="top">
            <h3>Show orders by date range:</h3>
        </tr>
        <tr>
            <td>
                <label for="datetimepicker">From</label>
                <input id="datetimepicker1" type="text" name="after"><br/>
            </td>
            <td>

                <label for="datetimepicker2">To</label>
                <input id="datetimepicker2" type="text" name="before"><br/>
            </td>
            <td>
                <button class="button button-primary" data-url="<?= site_url() ?>/wp-admin/admin-ajax.php" type="submit" id="showRange" >show orders</button>

            </td>
        </tr>

        <tr valign="top">
            <td>
                <div id="loadRange"></div>
            </td>
            <td>
                <button class="" data-url="<?= site_url() ?>/wp-admin/admin-ajax.php" type="submit" id="page" >next</button>
            </td>

        </tr>

    </table>
</div>
<?php

if(isset($_GET['cron']) && $_GET['cron'] == 'rest'){
    wp_clear_scheduled_hook( 'sftp_ws_report_settings' );
    wp_redirect( site_url().'/wp-admin/admin.php?page=ws-gift-cards'  );
    exit;

}
?>