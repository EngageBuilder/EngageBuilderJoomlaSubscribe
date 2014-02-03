<?php
class EbSDK {
    protected $config = array(
        'url'=>'http://cbuilder.lo/+/api',
//        'url'=>'http://i.engagebuilder.com/+/api',
        'apiKey'=>''
    );

    public function __construct($apiKey){
        #pre-checks
        function_exists('curl_version') or die('CURL support required');
        function_exists('json_encode') or die('JSON support required');

        $this->config['apiKey'] = $apiKey;
    }

    protected function execute($cmd, $data = array()){
        #set timeout
        set_time_limit(30);

        $ch = curl_init();
        #curl post
        curl_setopt($ch, CURLOPT_URL, $this->config['url'].'/'.$cmd);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_USERAGENT, 'EngageBuilder LLC API Client v1.0');
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:', 'X-API-Key: '.$this->config['apiKey']));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $result=curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return array('result'=>$result, 'code'=>$code);
    }

    public function validateKey(){
        $cmd = 'validate';
        $data = array(
          'apiKey'=>$this->config['apiKey']
        );

        $validation = $this->execute($cmd, $data);
        if ($validation['code'] != 200 )
            return false;

        return true;
    }

    public function getOnDemandCampaignList(){
        $cmd = 'getOnDemandCampaignList';

        $campaignList = $this->execute($cmd);
        if ($campaignList['code'] != 200){
            return false;
        }
        
        return json_decode($campaignList['result'], true); // return array of campaigns id=>name
    }

    public function triggerCampaign($campaignId, $subscriberId){
        $data = array(
            'campaign_id' => $campaignId,
            'subscriber_id' => $subscriberId
        );
        $cmd = 'triggerCampaign';
        $trigger = $this->execute($cmd, $data);

        if ($trigger['code']!=200)
            return false;
        else
            return (int) $trigger['result']; // returns the campaign sent code id
    }

    public function createSubscriber($firstName = null, $lastName = null, $phone = null, $email = null, $zipCode = null, $dob = null, $extraFields=array()){
        $data = array(
            'first_name'=>$firstName,
            'last_name'=>$lastName,
            'email'=>$email,
            'phone'=>$phone,
            'zip_code'=>$zipCode,
            'dob'=>$dob,
            'extra_fields'=>$extraFields
        );
        $subscriber = $this->execute('createSubscriber', $data);
        if ($subscriber['code']!=200)
            return false;
        else
            return (int) $subscriber['result']; // returns the Subscriber id
    }

    public function searchSubscriberByEmailOrPhone($emailOrPhone){
        $data = array('email_or_phone', $emailOrPhone);

        $fullSubscriber = $this->execute('searchSubscriberByEmailOrPhone', $data);
        if ($fullSubscriber['code']!=200)
            return false;
        else
            return $fullSubscriber['result']; // returns the Subscriber id
    }

    public function searchSubscriberById($subscriberId){
        $data = array('subscriber_id', $subscriberId);

        $fullSubscriber = $this->execute('searchSubscriberById', $data);
        if ($fullSubscriber['code']!=200)
            return false;
        else
            return $fullSubscriber['result']; // returns the Subscriber id
    }

    public function getLoyaltyAppList(){
        $data = array();
        $loyaltyList = $this->execute('getLoyaltyAppList', $data);

        if ($loyaltyList['code']!=200)
            return false;
        else
            return json_decode($loyaltyList['result']); // returns the Subscriber id
    }

    public function getDefaultLoyaltyApp(){
        $data = array();
        $defaultLoyalty = $this->execute('getDefaultLoyaltyApp', $data);

        if ($defaultLoyalty['code']!=200)
            return false;
        else
            return json_decode($defaultLoyalty['result'], true); // returns the Subscriber id
    }

    public function searchLoyaltyById($loyaltyId){
        $data = array('loyalty_id', $loyaltyId);

        $fullLoyalty = $this->execute('searchLoyaltyById', $data);
        if ($fullLoyalty['code']!=200)
            return false;
        else
            return json_decode($fullLoyalty['result']); // returns the Subscriber id
    }

    // fill parameter subscriber email or phone or id to search for it
    public function addSubscriberRewardPoints($pointsToAdd, $subscriberEmailOrPhone = null, $subscriberId = null){
        $data = array(
            'add_reward_points'=>$pointsToAdd
        );
        if ($subscriberId)
            $data['subscriber_id'] = $subscriberId;
        elseif($subscriberEmailOrPhone)
            $data['email_or_phone'] = $subscriberEmailOrPhone;
        else
            throw new Exception('You should fill at least one parameter to search subscriber by. Can be used subscriber email, phone or id.');

        $response = $this->execute('addSubscriberRewardPoints', $data);
        if ($response['code']!=200)
            return false;
        else
            return $response['result']; // returns the Subscriber id
    }

    public function getGroupList(){
        $data = array();

        $response = $this->execute('getGroupList', $data);
        if ($response['code']!=200)
            return false;
        else
            return json_decode($response['result'], true); // returns the Subscriber id
    }

    public function createNewGroup($newGroupName){
        if  (!$newGroupName)
            throw new Exception('You should provide a name for your new group name.');

        $data = array('name'=>$newGroupName);

        $response = $this->execute('createNewGroup', $data);
        if ($response['code']!=200)
            return false;
        else
            return $response['result']; // returns the Subscriber id
    }

    public function searchGroupById($groupNameId){
        if  (!$groupNameId)
            throw new Exception('You should provide a name for your new group name.');

        $data = array('group_name_id'=>$groupNameId);

        $response = $this->execute('searchGroupById', $data);
        if ($response['code']!=200)
            return false;
        else
            return json_decode($response['result']); // returns the Subscriber id
    }

    public function addSubscriberToGroup($groupNameId, $subscriberEmailOrPhone = null, $subscriberId = null){
        if (!$groupNameId) throw new Exception('You should provide a group name id.');

        $data = array('group_name_id'=>$groupNameId);

        if ($subscriberId)
            $data['subscriber_id'] = $subscriberId;
        elseif($subscriberEmailOrPhone)
            $data['email_or_phone'] = $subscriberEmailOrPhone;
        else
            throw new Exception('You should fill at least one parameter to search subscriber by. Can be used subscriber email, phone or id.');


        $response = $this->execute('addSubscriberToGroup', $data);
        if ($response['code']!=200)
            return false;
        else
            return $response['result']; // returns the Subscriber id
    }


}
