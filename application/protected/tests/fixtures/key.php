<?php

use Sil\DevPortal\models\Key;

return array(
    'key1' => array(
        'value' => 'K1_value',
        'secret' => 'K1_secret',
        'user_id' => 1,
        'api_id' => 2,
        'queries_second' => 1,
        'queries_day' => 111,
        'created' => '2016-06-08 10:06:18',
        'updated' => '2016-06-08 10:06:18',
        'requested_on' => '2016-06-08 10:06:18',
        'processed_on' => null,
        'processed_by' => null,
        'status' => Key::STATUS_PENDING,
        'purpose' => 'Test purpose.',
        'domain' => 'developer-portal.local',
    ),
    'approvedKey' => array(
        'value' => 'a',
        'secret' => 'b',
        'user_id' => 4,
        'api_id' => 2,
        'queries_second' => 10,
        'queries_day' => 1000,
        'created' => '2016-06-08 10:06:18',
        'updated' => '2016-06-08 10:06:18',
        'requested_on' => '2016-06-08 10:06:18',
        'processed_on' => '2016-06-08 10:06:18',
        'processed_by' => null,
        'status' => Key::STATUS_APPROVED,
        'purpose' => 'Unit testing',
        'domain' => 'developer-portal.local',
    ),
    'firstKeyForApiWithTwoKeys' => array(
        'value' => 'firstKeyForApiWithTwoKeys_value',
        'secret' => 'firstKeyForApiWithTwoKeys_secret',
        'user_id' => 10,
        'api_id' => 9,
        'queries_second' => 10,
        'queries_day' => 1000,
        'created' => '2016-06-08 10:06:18',
        'updated' => '2016-06-08 10:06:18',
        'requested_on' => '2016-06-08 10:06:18',
        'processed_on' => '2016-06-08 10:06:18',
        'processed_by' => null,
        'status' => Key::STATUS_APPROVED,
        'purpose' => 'Unit testing',
        'domain' => 'developer-portal.local',
    ),
    'secondKeyForApiWithTwoKeys' => array(
        'value' => 'secondKeyForApiWithTwoKeys_value',
        'secret' => 'secondKeyForApiWithTwoKeys_secret',
        'user_id' => 11,
        'api_id' => 9,
        'queries_second' => 10,
        'queries_day' => 1000,
        'created' => '2016-06-08 10:06:18',
        'updated' => '2016-06-08 10:06:18',
        'requested_on' => '2016-06-08 10:06:18',
        'processed_on' => '2016-06-08 10:06:18',
        'processed_by' => null,
        'status' => Key::STATUS_APPROVED,
        'purpose' => 'Unit testing',
        'domain' => 'developer-portal.local',
    ),
    'keyToApiOwnedByUser18' => array(
        'value' => 'keyToApiOwnedByUser18_value',
        'secret' => 'keyToApiOwnedByUser18_secret',
        'user_id' => 19,
        'api_id' => 12,
        'queries_second' => 10,
        'queries_day' => 1000,
        'created' => '2016-06-08 10:06:18',
        'updated' => '2016-06-08 10:06:18',
        'requested_on' => '2016-06-08 10:06:18',
        'processed_on' => '2016-06-08 10:06:18',
        'processed_by' => 18,
        'status' => Key::STATUS_APPROVED,
        'purpose' => 'Unit testing',
        'domain' => 'developer-portal.local',
    ),
    'keyToApiWithoutOwner' => array(
        'value' => 'keyToApiWithoutOwner_value',
        'secret' => 'keyToApiWithoutOwner_secret',
        'user_id' => 19,
        'api_id' => 11,
        'queries_second' => 10,
        'queries_day' => 1000,
        'created' => '2016-06-08 10:06:18',
        'updated' => '2016-06-08 10:06:18',
        'requested_on' => '2016-06-08 10:06:18',
        'processed_on' => '2016-06-08 10:06:18',
        'processed_by' => null,
        'status' => Key::STATUS_APPROVED,
        'purpose' => 'Unit testing',
        'domain' => 'developer-portal.local',
    ),
    'deniedKeyUser5' => array(
        'user_id' => 5,
        'api_id' => 2,
        'queries_second' => 10,
        'queries_day' => 1000,
        'created' => '2016-06-08 10:06:18',
        'updated' => '2016-06-08 10:06:18',
        'requested_on' => '2016-06-08 10:06:18',
        'processed_on' => '2016-06-08 10:06:18',
        'status' => Key::STATUS_DENIED,
        'purpose' => 'Unit testing',
        'domain' => 'developer-portal.local',
    ),
    'pendingKeyUser6' => array(
        'user_id' => 6,
        'api_id' => 2,
        'queries_second' => 10,
        'queries_day' => 1000,
        'created' => '2016-06-08 10:06:18',
        'updated' => '2016-06-08 10:06:18',
        'requested_on' => '2016-06-08 10:06:18',
        'processed_on' => null,
        'status' => Key::STATUS_PENDING,
        'purpose' => 'Unit testing',
        'domain' => 'developer-portal.local',
    ),
    'revokedKeyUser7' => array(
        'user_id' => 7,
        'api_id' => 2,
        'queries_second' => 10,
        'queries_day' => 1000,
        'created' => '2016-06-08 10:06:18',
        'updated' => '2016-06-08 10:06:18',
        'requested_on' => '2016-06-08 10:06:18',
        'processed_on' => '2016-06-08 10:06:18',
        'status' => Key::STATUS_REVOKED,
        'purpose' => 'Unit testing',
        'domain' => 'developer-portal.local',
    ),
    'pendingKey1_apiWithTwoPendingKeys' => array(
        'user_id' => 12,
        'api_id' => 10,
        'queries_second' => 10,
        'queries_day' => 1000,
        'created' => '2016-06-08 10:06:18',
        'updated' => '2016-06-08 10:06:18',
        'requested_on' => '2016-06-08 10:06:18',
        'processed_on' => null,
        'status' => Key::STATUS_PENDING,
        'purpose' => 'Unit testing',
        'domain' => 'developer-portal.local',
    ),
    'pendingKey2_apiWithTwoPendingKeys' => array(
        'user_id' => 13,
        'api_id' => 10,
        'queries_second' => 10,
        'queries_day' => 1000,
        'created' => '2016-06-08 10:06:18',
        'updated' => '2016-06-08 10:06:18',
        'requested_on' => '2016-06-08 10:06:18',
        'processed_on' => null,
        'status' => Key::STATUS_PENDING,
        'purpose' => 'Unit testing',
        'domain' => 'developer-portal.local',
    ),
    'pendingKeyForApiOwnedByUser18' => array(
        'user_id'   => 22,
        'api_id'    => 12,
        'queries_second' => 10,
        'queries_day' => 1000,
        'created' => '2016-06-08 10:06:18',
        'updated' => '2016-06-08 10:06:18',
        'requested_on' => '2016-06-08 10:06:18',
        'processed_on' => null,
        'status' => Key::STATUS_PENDING,
        'purpose' => 'Unit testing',
        'domain' => 'developer-portal.local',
    ),
    'pendingKeyToPublicApiThatRequiresApproval' => array(
        'user_id' => 9,
        'api_id' => 19,
        'queries_second' => 10,
        'queries_day' => 1000,
        'created' => '2016-06-08 10:06:18',
        'updated' => '2016-06-08 10:06:18',
        'requested_on' => '2016-06-08 10:06:18',
        'processed_on' => null,
        'status' => Key::STATUS_PENDING,
        'purpose' => 'Unit testing',
        'domain' => 'developer-portal.local',
    ),
    /** @todo Isn't the following an impossible situation? */
    'pendingKeyToPublicApiThatAutoApprovesKeys' => array(
        'user_id' => 9,
        'api_id' => 20,
        'queries_second' => 10,
        'queries_day' => 1000,
        'created' => '2016-06-08 10:06:18',
        'updated' => '2016-06-08 10:06:18',
        'requested_on' => '2016-06-08 10:06:18',
        'processed_on' => null,
        'status' => Key::STATUS_PENDING,
        'purpose' => 'Unit testing',
        'domain' => 'developer-portal.local',
    ),
    'pendingKey1ForUserWithOneApprovedKeyAndTwoPendingKeys' => array(
        'user_id' => 31,
        'api_id' => 19,
        'queries_second' => 10,
        'queries_day' => 1000,
        'created' => '2016-06-08 10:06:18',
        'updated' => '2016-06-08 10:06:18',
        'requested_on' => '2016-06-08 10:06:18',
        'processed_on' => null,
        'status' => Key::STATUS_PENDING,
        'purpose' => 'Unit testing',
        'domain' => 'developer-portal.local',
    ),
    'pendingKey2ForUserWithOneApprovedKeyAndTwoPendingKeys' => array(
        'user_id' => 31,
        'api_id' => 3,
        'queries_second' => 10,
        'queries_day' => 1000,
        'created' => '2016-06-08 10:06:18',
        'updated' => '2016-06-08 10:06:18',
        'requested_on' => '2016-06-08 10:06:18',
        'processed_on' => null,
        'status' => Key::STATUS_PENDING,
        'purpose' => 'Unit testing',
        'domain' => 'developer-portal.local',
    ),
    'approvedKeyForUserWithOneApprovedKeyAndTwoPendingKeys' => array(
        'value' => \Utils::getRandStr(32),
        'user_id' => 31,
        'api_id' => 20,
        'queries_second' => 10,
        'queries_day' => 1000,
        'created' => '2016-06-08 10:06:18',
        'updated' => '2016-06-08 10:06:18',
        'requested_on' => '2016-06-08 10:06:18',
        'processed_on' => null, /** @todo Fix this? */
        'status' => Key::STATUS_APPROVED,
        'purpose' => 'Unit testing',
        'domain' => 'developer-portal.local',
    ),
    'deniedKeyForApiOwnedByUser18' => array(
        'user_id' => 32,
        'api_id' => 12,
        'queries_second' => 10,
        'queries_day' => 1000,
        'created' => '2016-06-08 10:06:18',
        'updated' => '2016-06-08 10:06:18',
        'requested_on' => '2016-06-08 10:06:18',
        'processed_on' => null,
        'status' => Key::STATUS_DENIED,
        'purpose' => 'Unit testing',
        'domain' => 'developer-portal.local',
    ),
    'revokedKeyForApiOwnedByUser18' => array(
        'user_id' => 33,
        'api_id' => 12,
        'queries_second' => 10,
        'queries_day' => 1000,
        'created' => '2016-06-08 10:06:18',
        'updated' => '2016-06-08 10:06:18',
        'requested_on' => '2016-06-08 10:06:18',
        'processed_on' => null,
        'status' => Key::STATUS_REVOKED,
        'purpose' => 'Unit testing',
        'domain' => 'developer-portal.local',
    ),
    'firstUserKeyDependentOnAvd3' => array(
        'value' => \Utils::getRandStr(32),
        'user_id' => 34,
        'api_id' => 17,
        'queries_second' => 10,
        'queries_day' => 1000,
        'created' => '2016-07-26 09:55:41',
        'updated' => '2016-07-26 09:55:41',
        'requested_on' => '2016-07-26 09:55:41',
        'processed_on' => '2016-07-26 09:55:41',
        'status' => Key::STATUS_APPROVED,
        'purpose' => 'Unit testing',
        'domain' => 'developer-portal.local',
    ),
    'secondUserKeyDependentOnAvd3' => array(
        'user_id' => 35,
        'api_id' => 17,
        'queries_second' => 10,
        'queries_day' => 1000,
        'created' => '2016-07-26 09:55:42',
        'updated' => '2016-07-26 09:55:42',
        'requested_on' => '2016-07-26 09:55:42',
        'processed_on' => '2016-07-26 09:55:42',
        'status' => Key::STATUS_PENDING,
        'purpose' => 'Unit testing, too',
        'domain' => 'developer-portal.local',
    ),
    'keyNotDependentOnAvd3' => array(
        'value' => \Utils::getRandStr(32),
        'user_id' => 36,
        'api_id' => 17,
        'queries_second' => 10,
        'queries_day' => 1000,
        'created' => '2016-07-26 09:55:43',
        'updated' => '2016-07-26 09:55:43',
        'requested_on' => '2016-07-26 09:55:43',
        'processed_on' => '2016-07-26 09:55:43',
        'status' => Key::STATUS_APPROVED,
        'purpose' => 'Unit testing, as well',
        'domain' => 'developer-portal.local',
    ),
    'deniedKeyThusNotDependentOnAvd3AnyMore' => array(
        'user_id' => 37,
        'api_id' => 17,
        'queries_second' => 10,
        'queries_day' => 1000,
        'created' => '2016-07-26 09:55:44',
        'updated' => '2016-07-26 09:55:44',
        'requested_on' => '2016-07-26 09:55:44',
        'processed_on' => '2016-07-26 09:55:44',
        'status' => Key::STATUS_DENIED,
        'purpose' => 'Some frivolous purpose',
        'domain' => 'developer-portal.local',
    ),
    'allowedByTwoApiVisibilityDomains' => array(
        'value' => \Utils::getRandStr(32),
        'user_id' => 38,
        'api_id' => 17,
        'queries_second' => 10,
        'queries_day' => 1000,
        'created' => '2016-07-27 15:07:11',
        'updated' => '2016-07-27 15:07:11',
        'requested_on' => '2016-07-27 15:07:11',
        'processed_on' => '2016-07-27 15:07:11',
        'status' => Key::STATUS_APPROVED,
        'purpose' => 'Testing Multiple ApiVisibilityDomains',
        'domain' => 'developer-portal.local',
    ),
    'keyToCallableTestApi' => array(
        'value' => \Utils::getRandStr(32),
        'user_id' => 40,
        'api_id' => 22,
        'queries_second' => 100,
        'queries_day' => 1000,
        'created' => '2016-11-01 15:56:37',
        'updated' => '2016-11-01 15:56:37',
        'requested_on' => '2016-11-01 15:56:37',
        'processed_on' => '2016-11-01 15:56:37',
        'status' => Key::STATUS_APPROVED,
        'purpose' => 'Unit testing',
        'domain' => 'developer-portal.local',
    ),
);
