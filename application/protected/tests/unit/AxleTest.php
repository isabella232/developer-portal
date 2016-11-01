<?php

use ApiAxle\Shared\ApiException;
use Sil\DevPortal\components\ApiAxle\Client as ApiAxleClient;
use Sil\DevPortal\models\Api;
use Sil\DevPortal\models\Key;

/**
 * @group ApiAxle
 */
class AxleTest extends DeveloperPortalTestCase
{
    protected $config;
    
    public $fixtures = array(
        'users' => '\Sil\DevPortal\models\User',
        'keys' => '\Sil\DevPortal\models\Key',
    );  
    
    public function setUp()
    {
        global $ENABLE_AXLE;
        if(!isset($ENABLE_AXLE) || !$ENABLE_AXLE){
            $ENABLE_AXLE = true;
        }
        Yii::app()->user->id = 1;
        parent::setUp();
        $this->config = $this->getConfig();
    }
    
    public static function getConfig()
    {
        return Yii::app()->params['apiaxle'];
    }
    
    public static function tearDownAfterClass()
    {
        try {
            $apiAxle = new ApiAxleClient(self::getConfig());
            
            // Delete all the APIs that start with the string we use to identify
            // test APIs.
            $apiCodeNames = $apiAxle->listApis(0, 1000);
            foreach ($apiCodeNames as $apiCodeName) {
                if (strpos($apiCodeName, 'test-') !== false) {
                    $apiAxle->deleteApi($apiCodeName);
                }
            }
            
            // Get the list of keys from ApiAxle.
            $keyValues = $apiAxle->listKeys(0, 1000);
            
            // For each key that ApiAxle returned...
            foreach ($keyValues as $keyValue) {
                
                // If it starts with the string we use to identify test keys,
                // delete it.
                if (strpos($keyValue, 'test-') !== false) {
                    $apiAxle->deleteKey($keyValue);
                }
            }
        } catch(ApiException $ae){
            echo $ae;
        } catch(\Exception $e){
            echo $e;
        }
    }
    
    public function testAxleCreateApi()
    {
        $apiData = array(
            'code' => 'test-'.str_replace(array(' ','.'),'',microtime()),
            'display_name' => __FUNCTION__,
            'endpoint' => 'localhost',
            'default_path' => '/path/' . __FUNCTION__,
            'queries_second' => 3,
            'queries_day' => 1000,
            'visibility' => Api::VISIBILITY_PUBLIC,
            'protocol' => 'http',
            'strict_ssl' => 1,
            'approval_type' => 'auto',
            'endpoint_timeout' => 2,
        );
        
        $api = new Api();
        $api->setAttributes($apiData);
        $result = $api->save();
        $this->assertTrue($result, 'Failed to create API: ' . PHP_EOL .
            self::getModelErrorsForConsole($api->getErrors()));
        
        $apiAxle = new ApiAxleClient($this->config);
        $apiCodeNames = $apiAxle->listApis(0, 1000);
        $inList = false;
        foreach ($apiCodeNames as $apiCodeName) {
            if ($apiCodeName == $apiData['code']) {
                $inList = true;
                break;
            }
        }
        $this->assertTrue($inList, 'Api was created locally but not found on ApiAxle');
    }
    
    public function testAxleCreateApiWithAdditionalHeaders()
    {
        // Arrange:
        $apiAxle = new ApiAxleClient($this->config);
        $apiData = array(
            'code' => 'test-' . uniqid(),
            'display_name' => __FUNCTION__,
            'endpoint' => 'localhost',
            'default_path' => '/path/' . __FUNCTION__,
            'queries_second' => 3,
            'queries_day' => 1000,
            'visibility' => Api::VISIBILITY_PUBLIC,
            'protocol' => 'http',
            'strict_ssl' => 1,
            'approval_type' => 'auto',
            'endpoint_timeout' => 2,
            'additional_headers' => 'One=1&Two=2',
        );
        $api = new Api();
        $api->setAttributes($apiData);
        
        // Act:
        $result = $api->save();
        
        // Assert:
        $this->assertTrue($result, sprintf(
            "Failed to create API: \n%s",
            $api->getErrorsForConsole()
        ));
        $apiInfo = $apiAxle->getApiInfo($api->code);
        $dataFromAxle = $apiInfo->getData();
        $this->assertArrayHasKey(
            'additionalHeaders',
            $dataFromAxle,
            'No additional headers found in data returned by ApiAxle.'
        );
        $this->assertEquals(
            $apiData['additional_headers'],
            $dataFromAxle['additionalHeaders']
        );
    }
    
    public function testAxleCreateApiWithCustomSignatureWindow()
    {
        // Arrange:
        $apiAxle = new ApiAxleClient($this->config);
        $apiData = array(
            'code' => 'test-' . uniqid(),
            'display_name' => __FUNCTION__,
            'endpoint' => 'localhost',
            'default_path' => '/path/' . __FUNCTION__,
            'queries_second' => 3,
            'queries_day' => 1000,
            'visibility' => Api::VISIBILITY_PUBLIC,
            'protocol' => 'http',
            'strict_ssl' => 1,
            'approval_type' => 'auto',
            'endpoint_timeout' => 2,
            'signature_window' => 5,
        );
        $api = new Api();
        $api->setAttributes($apiData);
        
        // Act:
        $result = $api->save();
        
        // Assert:
        $this->assertTrue($result, sprintf(
            "Failed to create API: \n%s",
            $api->getErrorsForConsole()
        ));
        $apiInfo = $apiAxle->getApiInfo($api->code);
        $dataFromAxle = $apiInfo->getData();
        $this->assertArrayHasKey(
            'tokenSkewProtectionCount',
            $dataFromAxle,
            'No custom signature window found in data returned by ApiAxle.'
        );
        $this->assertEquals(
            $apiData['signature_window'],
            $dataFromAxle['tokenSkewProtectionCount'],
            'Did not correctly save the signature_window value.'
        );
    }
    
    public function testAxleCreateResetAndRevokeKey()
    {
        // Arrange:
        $normalUser = $this->users('userWithRoleOfUser');
        $adminUser = $this->users('userWithRoleOfAdmin');
        $api = new Api();
        $api->setAttributes(array(
            'code' => 'test-' . str_replace('.', '', microtime(true)),
            'display_name' => __FUNCTION__,
            'endpoint' => 'localhost',
            'default_path' => '/path/' . __FUNCTION__,
            'queries_second' => 3,
            'queries_day' => 1000,
            'visibility' => Api::VISIBILITY_PUBLIC,
            'protocol' => Api::PROTOCOL_HTTP,
            'strict_ssl' => Api::STRICT_SSL_TRUE,
            'approval_type' => Api::APPROVAL_TYPE_AUTO,
            'endpoint_timeout' => 2,
        ));
        $this->assertTrue(
            $api->save(),
            'Failed to create API: ' . print_r($api->getErrors(), true)
        );
        $key = new Key();
        $key->setAttributes(array(
            'user_id' => $normalUser->user_id,
            'api_id' => $api->api_id,
            'queries_second' => $api->queries_second,
            'queries_day' => $api->queries_day,
            'created' => 1465414526,
            'updated' => 1465414526,
            'requested_on' => 1465414526,
            'status' => Key::STATUS_PENDING,
            'purpose' => 'Unit testing',
            'domain' => 'developer-portal.local',
        ));
        
        // Act (create):
        $approveKeyResult = $key->approve($normalUser);
        
        // Assert (create):
        $this->assertTrue(
            $approveKeyResult,
            'Failed to create/approve Key: ' . print_r($key->getErrors(), true)
        );
        $apiAxle = new ApiAxleClient($this->config);
        $keyValuesAfterCreate = $apiAxle->listKeysForApi($api->code);
        $hasKeyAfterCreate = false;
        foreach ($keyValuesAfterCreate as $keyValue) {
            if ($keyValue == $key->value) {
                $hasKeyAfterCreate = true;
                break;
            }
        }
        $this->assertTrue(
            $hasKeyAfterCreate,
            'New key is not linked to API in ApiAxle. Key errors (if any): '
            . print_r($key, true)
        );
        $initialKeyValue = $key->value;
        $initialKeySecret = $key->secret;
        
        // Act (reset):
        $resetKeyResult = Key::resetKey($key->key_id);
        
        // Assert (reset):
        $this->assertTrue(
            $resetKeyResult[0],
            'Unable to reset key: ' . print_r($resetKeyResult[1], true)
        );
        $keyValuesAfterReset = $apiAxle->listKeysForApi($api->code);
        $hasKeyAfterReset = false;
        foreach ($keyValuesAfterReset as $keyValue) {
            if ($keyValue == $resetKeyResult[1]->value) {
                $hasKeyAfterReset = true;
                break;
            }
        }
        $this->assertTrue(
            $hasKeyAfterReset,
            'Reset key is not linked to API in ApiAxle. Key errors (if any): '
            . print_r($resetKeyResult[1], true)
        );
        $changedKeyValue = $resetKeyResult[1]->value;
        $changedKeySecret = $resetKeyResult[1]->secret;
        $this->assertNotEquals(
            $initialKeyValue,
            $changedKeyValue,
            'Resetting the key did not change its value.'
        );
        $this->assertNotEquals(
            $initialKeySecret,
            $changedKeySecret,
            'Resetting the key did not change its secret.'
        );
        
        // Act (revoke):
        $revokeKeyResult = Key::revokeKey($key->key_id, $adminUser);
        
        // Assert (revoke):
        $key->refresh();
        $this->assertTrue(
            $revokeKeyResult[0],
            'Unable to revoke key: ' . print_r($revokeKeyResult[1], true)
        );
        $keyValuesAfterRevoke = $apiAxle->listKeysForApi($api->code);
        $hasKeyAfterRevoke = false;
        foreach ($keyValuesAfterRevoke as $keyValue) {
            if ($keyValue == $key->value) {
                $hasKeyAfterRevoke = true;
                break;
            }
        }
        $this->assertFalse(
            $hasKeyAfterRevoke,
            'Revoked key was not deleted from API in ApiAxle. Key errors (if any): '
            . print_r($revokeKeyResult[1], true)
        );
    }
    
    public function testDeleteApi()
    {
        $apiData = array(
            'code' => 'test-'.str_replace(array(' ','.'),'',microtime()),
            'display_name' => __FUNCTION__,
            'endpoint' => 'localhost',
            'default_path' => '/path/' . __FUNCTION__,
            'queries_second' => 3,
            'queries_day' => 1000,
            'visibility' => Api::VISIBILITY_PUBLIC,
            'protocol' => 'http',
            'strict_ssl' => 1,
            'approval_type' => 'auto',
            'endpoint_timeout' => 2,
        );
        $api = new Api();
        $api->setAttributes($apiData);
        $result = $api->save();
        $this->assertTrue($result,'Failed to create API: '.print_r($api->getErrors(),true));
        
        $apiAxle = new ApiAxleClient($this->config);
        $apiCodeNames = $apiAxle->listApis(0, 1000);
        $hasApi = false;
        foreach ($apiCodeNames as $apiCodeName) {
            if ($apiCodeName == $api->code) {
                $hasApi = true;
                break;
            }
        }
        $this->assertTrue($hasApi,'New API not found on server.');
        
        $api->delete();
        $apiCodeNamesAfterDelete = $apiAxle->listApis(0, 1000);
        $hasApiAfterDelete = false;
        foreach ($apiCodeNamesAfterDelete as $apiCodeName) {
            if ($apiCodeName == $api->code) {
                $hasApiAfterDelete = true;
                break;
            }
        }
        $this->assertFalse($hasApiAfterDelete, 'New API still found after delete.');
    }
    
    public function testAxleCreate100Apis()
    {
        $count = 0;
        $howMany = 100;
        $apiData = array(
            'endpoint' => 'localhost',
            'queries_second' => 3,
            'queries_day' => 1000,
            'visibility' => Api::VISIBILITY_PUBLIC,
            'protocol' => 'http',
            'strict_ssl' => 1,
            'approval_type' => 'auto',
            'endpoint_timeout' => 2,
        );
        
        $uniqId = uniqid();
        while ($count < $howMany) {
            $apiData['code'] = 'test-' . $uniqId . '-' . $count;
            $apiData['display_name'] = __FUNCTION__ . $uniqId . '-' . $count;
            $apiData['default_path'] = '/path/' . $apiData['code'];
            $api = new Api();
            $api->setAttributes($apiData);
            if ( ! $api->save()) {
                $this->fail(sprintf(
                    'Failed to create API "%s": %s',
                    $apiData['code'],
                    $api->getErrorsForConsole()
                ));
            }
            $count++;
        }
        
        $inList = 0;
        $apiAxle = new ApiAxleClient($this->config);
        $apiCodeNames = $apiAxle->listApis(0, 1000);
        foreach ($apiCodeNames as $apiCodeName) {
            if (preg_match('/test\-' . $uniqId . '-[0-9]{1,3}/', $apiCodeName)) {
                $inList++;
            }
        }
        
        $this->assertEquals($howMany, $inList);
    }
}
