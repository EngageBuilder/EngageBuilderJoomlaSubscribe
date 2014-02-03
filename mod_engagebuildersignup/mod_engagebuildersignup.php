<?php
defined('_JEXEC') or die;
// load helper from component and css/js
require_once JPATH_BASE . '/administrator/components/com_engagebuildersignup/ebSdk/EbSDK.class.php';

$document =& JFactory::getDocument();
$document->addStyleSheet(JURI::root(false).'modules/mod_engagebuildersignup/css/engagebuildersignup.css');
$document->addScript(JURI::root(false).'modules/mod_engagebuildersignup/js/engagebuilder.js');

$db =& JFactory::getDBO();

$query = "SELECT * FROM #__eb";
$db->setQuery($query);
$api = $db->loadObject();
if (!$api){
    $query = 'insert into #__eb (first_name_label) values \'Your First Name\' ';
    $db->setQuery($query);
    $db->execute();

    $query = "SELECT *  FROM #__eb";
    $db->setQuery($query);
    $api = $db->loadObject();
}

$ebSDK = null;
if (!is_null($api->api_key) && $api->api_key!==''){
    $ebSDK = new EbSDK($api->api_key);
}

if (count($_POST)){ // means you are saving data
    if (is_null($ebSDK))
        echo '<label class="eb_error">This module its not well configured, please enter your API Key <a href="/administrator/index.php?option=com_engagebuildersignup">here</a>';
    // ok lets save the subscriber if possible
    // check for email validation.
    $first_name = $_POST['eb_first_name'];
    $last_name = $_POST['eb_last_name'];
    $phone = $_POST['eb_phone'];
    $email = $_POST['eb_email'];

    // create the subscriber
    $subscriberId = $ebSDK->createSubscriber($first_name, $last_name, $phone, $email);

    if ($subscriberId!==false && $api->connect_with_campaign_id != 0){ // means was added ok
        $sent = $ebSDK->triggerCampaign($api->connect_with_campaign_id, $subscriberId);
    }
    if ($subscriberId!==false && $api->add_to_group_id!= 0){ // means was added ok
        $ebSDK->addSubscriberToGroup($api->add_to_group_id, null, $subscriberId);
    }
}
if (count($_POST) && $result!==false):
    ?>
    <div class="eb_signup">
        <?php echo $api->thanks_message;?>
    </div>
<?php
else:
    ?>
    <div class="eb_signup">
        <?php echo $api->welcome_message;?>
        <?php if (isset($result) && $result === false): ?>
            <label class="eb_error"> Sorry, an error occur subscribing your email, please try again later. </label>
        <?php endif;?>
        <form action="" method="post" onsubmit="return validateEmail();">
            <?php if ($api->ask_for_first_name):?>
                <div class="eb_signup_item">
                    <label for="eb_first_name"><?php echo $api->first_name_label; ?> </label>
                    <input id="eb_first_name" name="eb_first_name" value="<?php echo ($_POST['eb_first_name']?$_POST['eb_first_name']:'')?>" />
                </div>
            <?php endif; ?>
            <?php if ($api->ask_for_last_name):?>
                <div class="eb_signup_item">
                    <label for="eb_last_name"><?php echo $api->last_name_label; ?> </label>
                    <input id="eb_last_name" name="eb_last_name" value="<?php echo ($_POST['eb_last_name']?$_POST['eb_last_name']:'')?>" />
                </div>
            <?php endif; ?>
            <?php if ($api->ask_for_phone):?>
                <div class="eb_signup_item">
                    <label for="eb_phone"><?php echo $api->phone_label; ?> </label>
                    <input id="eb_phone" name="eb_phone" value="<?php echo ($_POST['eb_phone']?$_POST['eb_phone']:'')?>" />
                </div>
            <?php endif; ?>
            <div class="eb_signup_item">
                <label for="eb_email"><?php echo $api->email_label; ?> </label>
                <input id="eb_email" name="eb_email" value="<?php echo ($_POST['eb_email']?$_POST['eb_email']:'')?>" />
            </div>
            <input type="submit" value="Subscribe" />
        </form>
    </div>
<?php endif;?>

