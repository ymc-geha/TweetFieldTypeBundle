<?php
/**
 * File contains: EzSystems\TweetFieldTypeBundle\Tests\eZ\Publish\FieldType\TweetSPIIntegrationTest class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\TweetFieldTypeBundle\Tests\eZ\Publish\FieldType;

use eZ\Publish\Core\Persistence\Legacy;
use eZ\Publish\Core\FieldType;
use eZ\Publish\SPI\Persistence\Content;
use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Tests\FieldType\BaseIntegrationTest;
use EzSystems\TweetFieldTypeBundle\eZ\Publish\FieldType\Tweet\LegacyConverter;
use EzSystems\TweetFieldTypeBundle\eZ\Publish\FieldType\Tweet\Type as TweetType;

/**
 * SPI Integration test for legacy storage field types
 *
 * This abstract base test case is supposed to be the base for field type
 * integration tests. It basically calls all involved methods in the field type
 * ``Converter`` and ``Storage`` implementations. Fo get it working implement
 * the abstract methods in a sensible way.
 *
 * The following actions are performed by this test using the custom field
 * type:
 *
 * - Create a new content type with the given field type
 * - Load create content type
 * - Create content object of new content type
 * - Load created content
 * - Copy created content
 * - Remove copied content
 *
 * @group integration
 */
class TweetSPIIntegrationTest extends BaseIntegrationTest
{
    /**
     * @var \EzSystems\TweetFieldTypeBundle\Twitter\ClientInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $twitterClientMock;

    protected function getCustomSqlSchemaDir()
    {
        return __DIR__ . '/_fixtures/';
    }

    /**
     * Get name of tested field type
     *
     * @return string
     */
    public function getTypeName()
    {
        return 'eztweet';
    }

    public function getCustomHandler()
    {
        $fieldType = new TweetType($this->getTwitterClientMock());
        return $this->getHandler(
            'eztweet',
            $fieldType,
            new LegacyConverter(),
            new FieldType\NullStorage()
        );
    }


    /**
     * Returns the FieldTypeConstraints to be used to create a field definition
     * of the FieldType under test.
     *
     * @return \eZ\Publish\SPI\Persistence\Content\FieldTypeConstraints
     */
    public function getTypeConstraints()
    {
        return new Content\FieldTypeConstraints();
    }

    /**
     * Get field definition data values
     *
     * This is a PHPUnit data provider
     *
     * @return array
     */
    public function getFieldDefinitionData()
    {
        return array(
            array( 'fieldType', 'eztweet' ),
            array( 'fieldTypeConstraints', new Content\FieldTypeConstraints() ),
        );
    }

    /**
     * Get initial field value
     *
     * @return \eZ\Publish\SPI\Persistence\Content\FieldValue
     */
    public function getInitialValue()
    {
        return new Content\FieldValue(
            array(
                'data' => 'http://twitter.com/xxx/status/123545',
                'externalData' => null,
                'sortKey' => 'http://twitter.com/xxx/status/123545',
            )
        );
    }

    /**
     * Asserts that the loaded field data is correct
     *
     * Performs assertions on the loaded field, mainly checking that the
     * $field->value->externalData is loaded correctly. If the loading of
     * external data manipulates other aspects of $field, their correctness
     * also needs to be asserted. Make sure you implement this method agnostic
     * to the used SPI\Persistence implementation!
     */
    public function assertLoadedFieldDataCorrect( Field $field )
    {
        $expected = $this->getInitialValue();

        $this->assertEquals(
            $expected->externalData,
            $field->value->externalData
        );

        $this->assertNotNull(
            $field->value->data
        );
    }

    /**
     * Get update field value.
     *
     * Use to update the field
     *
     * @return \eZ\Publish\SPI\Persistence\Content\FieldValue
     */
    public function getUpdatedValue()
    {
        return new Content\FieldValue(
            array(
                'data' => 'http://twitter.com/yyyyy/status/54321',
                'externalData' => null,
                'sortKey' => 'http://twitter.com/yyyyy/status/54321',
            )
        );
    }

    /**
     * Asserts that the updated field data is loaded correct
     *
     * Performs assertions on the loaded field after it has been updated,
     * mainly checking that the $field->value->externalData is loaded
     * correctly. If the loading of external data manipulates other aspects of
     * $field, their correctness also needs to be asserted. Make sure you
     * implement this method agnostic to the used SPI\Persistence
     * implementation!
     *
     * @return void
     */
    public function assertUpdatedFieldDataCorrect( Field $field )
    {
        $expected = $this->getUpdatedValue();

        $this->assertEquals(
            $expected->externalData,
            $field->value->externalData
        );

        $this->assertNotNull(
            $field->value->data
        );
    }

    /**
     * @return \EzSystems\TweetFieldTypeBundle\Twitter\ClientInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getTwitterClientMock()
    {
        if ( !isset( $this->twitterClientMock ) )
            $this->twitterClientMock = $this->getMock( 'EzSystems\\TweetFieldTypeBundle\\Twitter\\ClientInterface' );
        return $this->twitterClientMock;
    }
}
