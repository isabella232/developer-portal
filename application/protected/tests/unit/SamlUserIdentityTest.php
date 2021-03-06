<?php
namespace Sil\DevPortal\tests\unit;

use Sil\DevPortal\components\SamlUserIdentity;
use Sil\DevPortal\components\UserAuthenticationData;
use Sil\DevPortal\components\UserIdentity;
use Sil\DevPortal\models\User;
use Sil\DevPortal\tests\DbTestCase;

class SamlUserIdentityTest extends DbTestCase
{
    public $fixtures = array(
        'users' => '\Sil\DevPortal\models\User',
    );
    
    public function testExtractAccessGroups_exampleFromFunctionDocumentation()
    {
        // Arrange:
        $rawAccessGroups = array(
            'a=GROUP_name,b=stuff',
            'a=OTHER-GROUP,b=random',
        );
        $expectedResult = array(
            'GROUP_NAME',
            'OTHER-GROUP',
        );
        /* @var $samlUserIdentity SamlUserIdentity */
        $samlUserIdentity = \Phake::mock(
            '\Sil\DevPortal\components\SamlUserIdentity'
        );
        \Phake::when($samlUserIdentity)->extractAccessGroups->thenCallParent();
        
        // Act:
        $actualResults = $samlUserIdentity->extractAccessGroups($rawAccessGroups);
        
        // Assert:
        $this->assertEquals($expectedResult, $actualResults);
    }
    
    public function testFindUserRecord_fallbackToMatchByEmailForTrustedAuthProvider()
    {
        // Arrange:
        /* @var $expectedUser User */
        $expectedUser = $this->users('userFromTrustedAuthProviderLackingIdentifier');
        $userAuthData = new UserAuthenticationData(
            $expectedUser->auth_provider,
            'fake-identifier-1461943235',
            $expectedUser->email,
            $expectedUser->first_name,
            $expectedUser->last_name,
            $expectedUser->display_name
        );
        $samlUserIdentity = \Phake::mock(
            '\Sil\DevPortal\components\SamlUserIdentity'
        );
        \Phake::when($samlUserIdentity)->canTrustEmailAsFallbackIdFor->thenCallParent();
        \Phake::when($samlUserIdentity)->findUserRecord->thenCallParent();
        
        // Act:
        $actualUser = \Phake::makeVisible($samlUserIdentity)->findUserRecord(
            $userAuthData
        );
        
        // Assert:
        $this->assertEquals(
            $expectedUser,
            $actualUser,
            'Failed to find expected user (from trusted auth. provider) by '
            . 'email when record in database lacked auth. provider identifier.'
        );
    }
    
    public function testFindUserRecord_doNotFallbackToMatchByEmailForOtherAuthProvider()
    {
        // Arrange:
        /* @var $user User */
        $user = $this->users('userFromOtherAuthProviderLackingIdentifier');
        $userAuthData = new UserAuthenticationData(
            $user->auth_provider,
            'fake-identifier-1461943317',
            $user->email,
            $user->first_name,
            $user->last_name,
            $user->display_name
        );
        $samlUserIdentity = \Phake::mock(
            '\Sil\DevPortal\components\SamlUserIdentity'
        );
        \Phake::when($samlUserIdentity)->canTrustEmailAsFallbackIdFor->thenCallParent();
        \Phake::when($samlUserIdentity)->findUserRecord->thenCallParent();
        
        // Act:
        $result = \Phake::makeVisible($samlUserIdentity)->findUserRecord(
            $userAuthData
        );
        
        // Assert:
        $this->assertNull(
            $result,
            'Incorrectly found user (NOT from the trusted auth. provider) by '
            . 'email when record in database lacked auth. provider identifier.'
        );
    }
    
    public function testGetNameOfAuthProvider_knownValue()
    {
        // Arrange:
        $samlUserIdentity = \Phake::mock(
            '\Sil\DevPortal\components\SamlUserIdentity'
        );
        $idp = 'dummy.idp.entity.id'; // Match value in config/test.php file.
        $expectedResult = 'SAML';
        \Phake::when($samlUserIdentity)->getAuthSourceIdpEntityId->thenReturn($idp);
        \Phake::when($samlUserIdentity)->getNameOfAuthProvider->thenCallParent();
        
        // Act:
        $actualResult = \Phake::makeVisible($samlUserIdentity)->getNameOfAuthProvider();
        
        // Assert:
        $this->assertSame(
            $expectedResult,
            $actualResult,
            'Failed to return correct name for a known SAML auth provider (see '
            . 'config/test.php).'
        );
    }
    
    public function testGetNameOfAuthProvider_unknownValue()
    {
        // Arrange:
        $samlUserIdentity = \Phake::mock(
            '\Sil\DevPortal\components\SamlUserIdentity'
        );
        $idpEntityId = 'abc.123';
        $expectedResult = $idpEntityId;
        \Phake::when($samlUserIdentity)->getAuthSourceIdpEntityId->thenReturn($idpEntityId);
        \Phake::when($samlUserIdentity)->getNameOfAuthProvider->thenCallParent();
        
        // Act:
        $actualResult = \Phake::makeVisible($samlUserIdentity)->getNameOfAuthProvider();
        
        // Assert:
        $this->assertSame(
            $expectedResult,
            $actualResult,
            'Failed to return given string when it was not a known value.'
        );
    }
}
