<?php
/**
 * File containing the Tweet FieldType Test class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\TweetFieldTypeBundle\Tests\eZ\Publish\FieldType;

use eZ\Publish\Core\FieldType\Tests\FieldTypeTest;
use EzSystems\TweetFieldTypeBundle\eZ\Publish\FieldType\Tweet\Type as TweetType;
use EzSystems\TweetFieldTypeBundle\eZ\Publish\FieldType\Tweet\Value as TweetValue;
use eZ\Publish\Core\FieldType\ValidationError;

class TweetTest extends FieldTypeTest
{
    protected function createFieldTypeUnderTest()
    {
        return new TweetType($this->getMock('EzSystems\TweetFieldTypeBundle\Twitter\TwitterClientInterface'));
    }

    protected function getValidatorConfigurationSchemaExpectation()
    {
        return array(
            'TweetUrlValidator' => array(),
            'TweetAuthorValidator' => array(
                'AuthorList' => array(
                    'type' => 'array',
                    'default' => array()
                )
            )
        );
    }

    protected function getSettingsSchemaExpectation()
    {
        return array();
    }

    protected function getEmptyValueExpectation()
    {
        return new TweetValue;
    }

    public function provideInvalidInputForAcceptValue()
    {
        return array(
            array(
                1,
                'eZ\\Publish\\Core\\Base\\Exceptions\\InvalidArgumentException',
            ),
            array(
                new \stdClass,
                'eZ\\Publish\\Core\\Base\\Exceptions\\InvalidArgumentException'
            ),
        );
    }

    public function provideValidInputForAcceptValue()
    {
        return array(
            array(
                'https://twitter.com/user/status/123456789',
                new TweetValue( array( 'url' => 'https://twitter.com/user/status/123456789' ) ),
            ),
            array(
                new TweetValue(
                    array(
                        'url' => 'https://twitter.com/user/status/123456789'
                    )
                ),
                new TweetValue(
                    array(
                        'url' => 'https://twitter.com/user/status/123456789'
                    )
                ),
            ),
            array(
                new TweetValue(
                    array(
                        'url' => 'https://twitter.com/user/status/123456789',
                        'authorUrl' => 'https://twitter.com/user',
                        'contents' => '<blockquote />'
                    )
                ),
                new TweetValue(
                    array(
                        'url' => 'https://twitter.com/user/status/123456789',
                        'authorUrl' => 'https://twitter.com/user',
                        'contents' => '<blockquote />'
                    )
                )
            )
        );
    }

    public function provideInputForToHash()
    {
        return array(
            array(
                new TweetValue,
                null
            ),
            array(
                new TweetValue( array( 'url' => 'https://twitter.com/user/status/123456789' ) ),
                array(
                    'url' => 'https://twitter.com/user/status/123456789',
                    'authorUrl' => '',
                    'contents' => ''
                )
            ),
            array(
                new TweetValue(
                    array(
                        'url' => 'https://twitter.com/user/status/123456789',
                        'authorUrl' => 'https://twitter.com/user',
                        'contents' => '<blockquote />'
                    )
                ),
                array(
                    'url' => 'https://twitter.com/user/status/123456789',
                    'authorUrl' => 'https://twitter.com/user',
                    'contents' => '<blockquote />'
                )
            )
        );
    }

    public function provideInputForFromHash()
    {
        return array(
            array(
                array(), new TweetValue
            ),
            array(
                array( 'url' => 'https://twitter.com/user/status/123456789' ),
                new TweetValue( array( 'url' => 'https://twitter.com/user/status/123456789' ) ),
            ),
            array(
                array(
                    'url' => 'https://twitter.com/user/status/123456789',
                    'authorUrl' => 'https://twitter.com/user',
                    'contents' => '<blockquote />'
                ),
                new TweetValue(
                    array(
                        'url' => 'https://twitter.com/user/status/123456789',
                        'authorUrl' => 'https://twitter.com/user',
                        'contents' => '<blockquote />'
                    )
                ),
            )
        );
    }

    protected function provideFieldTypeIdentifier()
    {
        return 'eztweet';
    }

    public function provideDataForGetName()
    {
        return array(
            array( $this->getEmptyValueExpectation(), '' ),
            array( new TweetValue( 'https://twitter.com/ymc_ch/status/572364727990558720' ), 'ymc_ch-status-572364727990558720' )
        );
    }

    public function provideValidDataForValidate()
    {
        return array(
            array(
                array(
                    "validatorConfiguration" => array(
                        "TweetAuthorValidator" => array(
                            "AuthorList" => ['ymc_ch']
                        ),
                    ),
                ),
                new TweetValue('https://twitter.com/ymc_ch/status/572364727990558720'),
            )
        );
    }
    public function provideInvalidDataForValidate()
    {
        return array(
            array(
                array(
                    "validatorConfiguration" => array(
                        "TweetAuthorValidator" => array(
                            "AuthorList" => ['ymc']
                        ),
                    ),
                ),
                new TweetValue('https://twitter.com/ymc_ch/status/572364727990558720'),
                array(
                    new ValidationError(
                        "Twitter user %user% is not in the approved author list",
                        null,
                        array('ymc_ch')
                    ),
           ),
            )
        );
    }

}
