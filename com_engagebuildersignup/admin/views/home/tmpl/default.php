<?php
/**
 */

// No direct access
defined('_JEXEC') or die;
//die('display default;');
//jimport('joomla.html.pane');
//$pane = JPane::getInstance('Sliders');

// lets see if you have
$db =& JFactory::getDBO();

if (count($_POST)){ // means save form
    $query = sprintf(<<<EOF
UPDATE #__eb SET
api_key = '%s',
ask_for_first_name = '%s',
first_name_label = '%s',
ask_for_last_name = '%s',
last_name_label = '%s',
email_label = '%s',
ask_for_phone = '%s',
phone_label = '%s',
welcome_message = '%s',
thanks_message = '%s',
connect_with_campaign_id = %s,
add_to_group_id = %s

EOF
        ,
        $_POST['apikey'],
        $_POST['askFirstName']=='on'?1:0,
        addslashes($_POST['labelFirstName']),
        $_POST['askLastName']=='on'?1:0,
        addslashes($_POST['labelLastName']),
        addslashes($_POST['labelEmail']),
        $_POST['askPhone']=='on'?1:0,
        addslashes($_POST['labelPhone']),
        addslashes($_POST['welcome']),
        addslashes($_POST['thanks']),
        $_POST['connectCampaignId']?$_POST['connectCampaignId']:0,
        $_POST['addToGroupId']?$_POST['addToGroupId']:0
    );
    $db->setQuery($query);
    $db->execute();
}


$query = "SELECT * FROM #__eb";
$db->setQuery($query);
$api = $db->loadObject();
//if (count($_POST)){ // means you are saving data
if (!$api){
    $query = 'insert into #__eb (first_name_label) values \'Your First Name\' ';
    $db->setQuery($query);
    $db->execute();

    $query = "SELECT *  FROM #__eb";
    $db->setQuery($query);
    $api = $db->loadObject();
}
JHTML::_('behavior.modal');
$editor =& JFactory::getEditor();
$params = array( 'smilies'=> '0' ,
    'style'  => '0' ,
    'layer'  => '0' ,
    'table'  => '0' ,
    'clear_entities'=>'0'
);

$ebSDK = null;
if (!is_null($api->api_key) && $api->api_key!==''){
//    JLoader::import(JURI::root(true).'administrator/components/com_engagebuildersignup/ebSdk/EbSDK.class.php');
//    echo JURI::root(true).'administrator/components/com_engagebuildersignup/ebSdk/EbSDK.class.php'; die;
    $ebSDK = new EbSDK($api->api_key);
}
?>

<table class="adminForm" style="width: 100%">
	<tbody>
		<tr>
			<td valign="top" style="width: 50%">
                <a href="http://www.engagebuilder.com">
                    <?php echo JHTML::image(JURI::root(false).'administrator/components/com_engagebuildersignup/images/cross_channel_circle.png', 'Cross Channel marketing'); ?>
                </a>
                <h2> Engagebuilder LLC can help your business with: </h2>
                <ul class="eb_admin_features">
                    <li>Send Newsletters/Trigger emails/Email marketing</li>
                    <li>Facebook & social media marketing</li>
                    <li>Facebook tabs</li>
                    <li>SMS engagement</li>
                    <li>Loyalty program</li>
                    <li>Mobile website & Apps</li>
                    <li>Videos & slide shows</li>
                    <li>In store experience</li>
                </ul>
                <div  id="eb-signup">
                    <h2> Do you have NOT an EngageBuilder account? </h2>
                    <a href="http://www.engagebuilder.com/pricing/engage-builder/pricing" target="_blank">
                        <?php echo JHTML::image(JURI::root(false).'administrator/components/com_engagebuildersignup/images/start_button.jpg', 'Signup Now!'); ?>
                    </a>
                </div>
			</td>
			<td valign="top"  style="width: 50%">

                <h2> Configure and Customize subscribers signup form? </h2>
                    <form action="" method="post" onsubmit="return validateApiKey();">
                        <table class="eb_admin_fields">
                            <tr>
                                <td>
                                    <p> <a  class="modal" rel="{handler: 'iframe', size: {x: 940, y: 640}}" href="http://i.engagebuilder.com/users/api-key">Request</a> a new api key using IP of this server (<b><?php echo $_SERVER['SERVER_ADDR'];?></b>) and enter the given key in the box to the right. Then review all parameters and enjoy. </p>
                                </td>
                                <td>
                                    <input type="text" value="<?php echo ($api->api_key)?$api->api_key:'';?>" placeholder="Your api key here" id="apikey" name="apikey" />
                                    <?php if ($invalidKey = (!is_null($ebSDK) && !($ebSDK->validateKey()))): ?>
                                        <br/><small class="eb-admin-error">Invalid API Key. If you want to ask for help with this issue, please <a class="modal" rel="{handler: 'iframe', size: {x: 940, y: 640}}" href="http://i.engagebuilder.com/support-ticket?dm_embed=1&topic=11" >open a Support Ticket</a></small>
                                    <?php endif;?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <label for="welcome"> Welcome Message</label></br>
                                    <?php echo $editor->display('welcome',$api->welcome_message,'100%','10px', false, null, null, null, array() );?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="checkbox" <?php echo ($api->ask_for_first_name)?'checked="checked"':'';?> id="askFirstName" name="askFirstName" />
                                    <label for="askFirstName"> Display first name field?</label>
                                </td>
                                <td>
                                    <label for="labelFirstName">Label for field First name </label><br/>
                                    <input type="text" placeholder="Your First Name" id="labelFirstName" name="labelFirstName" value="<?php echo $api->first_name_label;?>"/>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input type="checkbox"  <?php echo ($api->ask_for_last_name)?'checked="checked"':'';?> id="askFirstName" name="askLastName" />
                                    <label for="askLastName"> Display last name field?</label>
                                </td>
                                <td>
                                    <label for="labelLastName"> Label for field Last name </label><br/>
                                    <input type="text" placeholder="Your Last Name" id="labelFirstName" name="labelLastName" value="<?php echo $api->last_name_label;?>"/>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input type="checkbox"  <?php echo ($api->ask_for_phone)?'checked="checked"':'';?>  id="askPhone" name="askPhone" />
                                    <label for="askPhone"> Display phone field?</label>
                                </td>
                                <td>
                                    <label for="labelPhone">Label for field Phone? </label><br/>
                                    <input type="text" placeholder="Your Phone" id="labelPhone" name="labelPhone" value="<?php echo $api->phone_label;?>" />
                                </td>
                            </tr>

                            <tr>
                                <td>
                                </td>
                                <td>
                                    <label for="labelPhone"> Label for field email</label>
                                    <br/>
                                    <input type="text" placeholder="Your Email" id="labelEmail" name="labelEmail" value="<?php echo $api->email_label;?>" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="addToGroupId"> Segment Subscriber into Group (optional)</label><br/>
                                    <small>To get your current group list a valid key should be saved first.</small>
                                    <small>if you leave blank subscribers will be added to your main list.</small>
                                </td>
                                <td>
                                    <?php
                                        if (!is_null($ebSDK)){
                                            $groupList = $ebSDK->getGroupList();
                                            if ($groupList===false){
                                                echo '<small class="eb-admin-error"> Error getting list</small>';
                                                $groupList = array();
                                            }
                                        }
                                    ?>
                                    <select id="addToGroupId" name="addToGroupId">
                                        <option value="0" <?php echo ($api->add_to_group_id == 0)?'selected="selected"':'';?>> Select a group </option>
                                        <?php foreach ($groupList as $key=>$name):?>
                                            <option value="<?php echo $key;?>" <?php echo ($api->add_to_group_id == $key)?'selected="selected"':'';?>> <?php echo $name;?></option>
                                        <?php endforeach;?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="connectCampaignId"> On Demand Campaign (optional)</label><br/>
                                    <small>To get your campaigns list a valid key should be saved first.</small>
                                </td>
                                <td>
                                    <?php
                                    if (!is_null($ebSDK)){
                                        $campaignList = $ebSDK->getOnDemandCampaignList();
                                        if ($campaignList===false){
                                            echo '<small class="eb-admin-error"> Error getting list</small>';
                                            $campaignList = array();
                                        }
                                    }
                                    ?>
                                    <select id="connectCampaignId" name="connectCampaignId">
                                        <option value="0" <?php echo ($api->connect_with_campaign_id == 0)?'selected="selected"':'';?>> Select a campaign </option>
                                        <?php foreach ($campaignList as $key=>$name):?>
                                            <option value="<?php echo $key;?>" <?php echo ($api->connect_with_campaign_id == $key)?'selected="selected"':'';?>> <?php echo $name;?></option>
                                        <?php endforeach;?>
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2">
                                    <label for="thanks"> Thanks Message</label></br>
                                    <?php echo $editor->display('thanks',$api->thanks_message,'100%','10px', false, null, null, null, array() );?>
                                </td>
                            </tr>

                            <tr>
                                <td  colspan="2">
                                    <input type="submit" value="Save" id="eb-save" />
                                    <p id="css-route"> You can add your own css rules to fit your frontend design in auto-loaded file <small><?php echo JURI::root(true).'modules/mod_engagebuildersignup/css/engagebuilder.css';?></small></p>
                                </td>
                            </tr>
                        </table>
                    </form>
            <?php
			?></td>
		</tr>

	</tbody>
</table>