<?php

use Sil\DevPortal\components\ApiAxle\Client as ApiAxleClient;
use Sil\DevPortal\components\Http\ClientG5 as HttpClient;
use Sil\DevPortal\models\Api;
use Sil\DevPortal\models\Key;
use Sil\DevPortal\models\User;

/**
 * @group ApiAxle
 * @method Api apis(string $fixtureName)
 * @method User users(string $fixtureName)
 * @method Key keys(string $fixtureName)
 */
class AxleTest extends DeveloperPortalTestCase
{
    public $fixtures = array(
        'apis' => Api::class,
        'users' => User::class,
        'keys' => Key::class,
    );  
    
    public function setUp()
    {
        global $ENABLE_AXLE;
        if(!isset($ENABLE_AXLE) || !$ENABLE_AXLE){
            $ENABLE_AXLE = true;
        }
        Yii::app()->user->id = 1;
        parent::setUp();
    }
    
    protected static function calculateKeyringNameForKey($keyValue)
    {
        if ( ! empty($keyValue)) {
            $key = Key::model()->findByAttributes(['value' => $keyValue]);
            if ($key !== null) {
                return $key->calculateKeyringName();
            }
        }
        return null;
    }
    
    protected static function deleteTestApisFromApiAxle(ApiAxleClient $apiAxle)
    {
        // Delete all the APIs that start with the string we use to identify
        // test APIs.
        $apiCodeNames = $apiAxle->listApis(0, 1000);
        foreach ($apiCodeNames as $apiCodeName) {
            if (strpos($apiCodeName, 'test-') !== false) {
                $apiAxle->deleteApi($apiCodeName);
            }
        }
    }
    
    protected static function deleteTestKeysFromApiAxle(ApiAxleClient $apiAxle)
    {
        // Delete all the keys that start with the string we use to identify
        // test keys.
        $keyValues = $apiAxle->listKeys(0, 1000);
        foreach ($keyValues as $keyValue) {
            if ( ! self::isValueOfTestKey($keyValue)) {
                continue;
            }
        }
    }
    
    protected static function getApiAxleClient()
    {
        return new ApiAxleClient(\Yii::app()->params['apiaxle']);
    }
    
    protected static function isValueOfTestKey($keyValue)
    {
        return (strpos($keyValue, 'test-') === 0);
    }
    
    public static function tearDownAfterClass()
    {
        try {
            $apiAxle = self::getApiAxleClient();
            self::deleteTestApisFromApiAxle($apiAxle);
            self::deleteTestKeysFromApiAxle($apiAxle);
        } catch(\Exception $e){
            echo PHP_EOL, $e, PHP_EOL;
        }
    }
    
    /**
     * As a programmer, I want to be able to recreate an API in ApiAxle so that
     * I can recover from a data loss in ApiAxle.
     */
    public function testApiCreateOrUpdateInApiAxle()
    {
        // Arrange:
        $apiAxle = self::getApiAxleClient();
        $api = new Api();
        $apiCode = 'test-' . uniqid();
        $api->setAttributes([
            'code' => $apiCode,
            'endpoint' => 'local',
            'default_path' => '/' . $apiCode,
            'display_name' => __FUNCTION__,
            'queries_second' => 100,
            'queries_day' => 1000,
            'endpoint_timeout' => 10,
        ]);
        $this->assertTrue($api->save(), $api->getErrorsForConsole());
        $this->assertTrue($apiAxle->apiExists($apiCode));
        $this->assertTrue($apiAxle->deleteApi($apiCode));
        $this->assertFalse($apiAxle->apiExists($apiCode));
        
        // Act:
        $result = $api->createOrUpdateInApiAxle();
        
        // Assert:
        $this->assertTrue($result, $api->getErrorsForConsole());
        $this->assertTrue($apiAxle->apiExists($apiCode));
    }
    
    /**
     * As a programmer, I want to be able to recreate a key in ApiAxle so that
     * I can recover from a data loss in ApiAxle.
     */
    public function testKeyCreateOrUpdateInApiAxle()
    {
        // Arrange:
        $apiAxle = self::getApiAxleClient();
        $key = $this->keys('approvedKey');
        $key->generateNewValueAndSecret();
        $this->assertTrue(
            $key->api->save(), // Make sure the API exists in ApiAxle.
            $key->api->getErrorsForConsole()
        );
        $this->assertTrue(
            $key->save(), // Make sure the key exists in ApiAxle.
            $key->getErrorsForConsole()
        );
        
        // Pre-assert:
        $this->assertTrue($apiAxle->keyExists($key->value));
        $apiAxle->deleteKey($key->value);
        $this->assertFalse($apiAxle->keyExists($key->value));
        
        // Act:
        $result = $key->createOrUpdateInApiAxle();
        
        // Assert:
        $this->assertTrue($result, $key->getErrorsForConsole());
        $this->assertTrue($apiAxle->keyExists($key->value));
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
        
        $apiAxle = self::getApiAxleClient();
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
        $apiAxle = self::getApiAxleClient();
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
        $apiAxle = self::getApiAxleClient();
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
    
    public function testEffectsOfCustomSignatureWindow()
    {
        // Arrange:
        $key = $this->keys('keyToCallableTestApi');
        $this->assertTrue(
            $key->api->save(), // Make sure the API exists in ApiAxle.
            $key->api->getErrorsForConsole()
        );
        $key->generateNewValueAndSecret();
        $this->assertTrue(
            $key->save(), // Make sure the key exists in ApiAxle.
            $key->getErrorsForConsole()
        );
        $this->assertTrue(
            $key->api->requiresSignature(),
            'This test requires a key to an API that requires a signature.'
        );
        $proxyProtocol = parse_url(\Yii::app()->params['apiaxle']['endpoint'], PHP_URL_SCHEME);
        $apiAxleEndpointDomain = parse_url(\Yii::app()->params['apiaxle']['endpoint'], PHP_URL_HOST);
        $proxyDomain = str_replace('apiaxle.', '', $apiAxleEndpointDomain);
        $urlMinusSignature = sprintf(
            '%s://%s.%s/?api_key=%s&api_sig=',
            $proxyProtocol,
            $key->api->code,
            $proxyDomain,
            $key->value
        );
        $client = new HttpClient();
        foreach ([3, Api::SIGNATURE_WINDOW_MAX] as $signatureWindow) {
            $key->api->signature_window = $signatureWindow;
            $this->assertTrue(
                $key->api->save(),
                $key->api->getErrorsForConsole()
            );
            $lastValidSignatureOffset = 0;
            $foundInvalidSignatureOffset = false;
            for ($i = 0; $i <= Api::SIGNATURE_WINDOW_MAX + 1; $i++) {
                $signature = \CalcApiSig\HmacSigner::CalcApiSig(
                    $key->value,
                    $key->secret,
                    time() + $i
                );

                // Act:
                $response = $client->request('GET', $urlMinusSignature . $signature);
                $responseData = json_decode($response->getBody());
                if ($responseData->meta->status_code == 403) {
                    $foundInvalidSignatureOffset = true;
                } else {
                    $lastValidSignatureOffset = $i;
                }
            }

            // Assert:
            $this->assertEquals(
                $key->api->signature_window,
                $lastValidSignatureOffset
            );
            $this->assertTrue($foundInvalidSignatureOffset);
        }
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
        $apiAxle = self::getApiAxleClient();
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
        
        $apiAxle = self::getApiAxleClient();
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
        $apiAxle = self::getApiAxleClient();
        $apiCodeNames = $apiAxle->listApis(0, 1000);
        foreach ($apiCodeNames as $apiCodeName) {
            if (preg_match('/test\-' . $uniqId . '-[0-9]{1,3}/', $apiCodeName)) {
                $inList++;
            }
        }
        
        $this->assertEquals($howMany, $inList);
    }
}
