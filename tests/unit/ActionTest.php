<?php

namespace Utopia\Unit;

use PHPUnit\Framework\TestCase;
use Utopia\Platform\Action;
use Utopia\Validator\Text;

class ActionTest extends TestCase
{
    /**
     * Test that the model parameter is stored correctly when provided
     */
    public function testParamWithModel(): void
    {
        $action = new class () extends Action {
            public function __construct()
            {
                $this
                    ->param(
                        key: 'userId',
                        default: null,
                        validator: new Text(100),
                        description: 'User ID',
                        optional: false,
                        injections: [],
                        skipValidation: false,
                        deprecated: false,
                        example: 'user_123',
                        model: 'user'
                    );
            }
        };

        $params = $action->getParams();

        $this->assertArrayHasKey('userId', $params);
        $this->assertEquals('user', $params['userId']['model']);
    }

    /**
     * Test that model parameter defaults to null when not provided
     */
    public function testParamModelDefaultsToNull(): void
    {
        $action = new class () extends Action {
            public function __construct()
            {
                $this
                    ->param(
                        key: 'name',
                        default: '',
                        validator: new Text(100),
                        description: 'Name field'
                    );
            }
        };

        $params = $action->getParams();

        $this->assertArrayHasKey('name', $params);
        $this->assertArrayHasKey('model', $params['name']);
        $this->assertNull($params['name']['model']);
    }

    /**
     * Test that model is included in options array
     */
    public function testParamModelInOptions(): void
    {
        $action = new class () extends Action {
            public function __construct()
            {
                $this
                    ->param(
                        key: 'document',
                        default: null,
                        validator: new Text(1000),
                        description: 'Document object',
                        optional: true,
                        injections: [],
                        skipValidation: false,
                        deprecated: false,
                        example: '{"key": "value"}',
                        model: 'document'
                    );
            }
        };

        $options = $action->getOptions();

        $this->assertArrayHasKey('param:document', $options);
        $this->assertEquals('document', $options['param:document']['model']);
        $this->assertEquals('param', $options['param:document']['type']);
    }

    /**
     * Test multiple params with different models
     */
    public function testMultipleParamsWithDifferentModels(): void
    {
        $action = new class () extends Action {
            public function __construct()
            {
                $this
                    ->param(
                        key: 'user',
                        default: null,
                        validator: new Text(100),
                        description: 'User object',
                        optional: false,
                        injections: [],
                        skipValidation: false,
                        deprecated: false,
                        example: '{}',
                        model: 'user'
                    )
                    ->param(
                        key: 'project',
                        default: null,
                        validator: new Text(100),
                        description: 'Project object',
                        optional: false,
                        injections: [],
                        skipValidation: false,
                        deprecated: false,
                        example: '{}',
                        model: 'project'
                    )
                    ->param(
                        key: 'simpleField',
                        default: '',
                        validator: new Text(50),
                        description: 'Simple text field'
                    );
            }
        };

        $params = $action->getParams();

        $this->assertEquals('user', $params['user']['model']);
        $this->assertEquals('project', $params['project']['model']);
        $this->assertNull($params['simpleField']['model']);
    }

    /**
     * Test skipValidation parameter is stored correctly
     */
    public function testParamSkipValidation(): void
    {
        $action = new class () extends Action {
            public function __construct()
            {
                $this
                    ->param(
                        key: 'skipField',
                        default: null,
                        validator: new Text(100),
                        description: 'Field with skipped validation',
                        optional: false,
                        injections: [],
                        skipValidation: true
                    )
                    ->param(
                        key: 'normalField',
                        default: null,
                        validator: new Text(100),
                        description: 'Field with normal validation',
                        optional: false,
                        injections: [],
                        skipValidation: false
                    );
            }
        };

        $params = $action->getParams();

        $this->assertTrue($params['skipField']['skipValidation']);
        $this->assertFalse($params['normalField']['skipValidation']);
    }

    /**
     * Test skipValidation defaults to false
     */
    public function testParamSkipValidationDefaultsFalse(): void
    {
        $action = new class () extends Action {
            public function __construct()
            {
                $this
                    ->param(
                        key: 'defaultField',
                        default: '',
                        validator: new Text(100),
                        description: 'Field with default skipValidation'
                    );
            }
        };

        $params = $action->getParams();

        $this->assertArrayHasKey('skipValidation', $params['defaultField']);
        $this->assertFalse($params['defaultField']['skipValidation']);
    }

    /**
     * Test deprecated parameter is stored correctly
     */
    public function testParamDeprecated(): void
    {
        $action = new class () extends Action {
            public function __construct()
            {
                $this
                    ->param(
                        key: 'oldField',
                        default: null,
                        validator: new Text(100),
                        description: 'Deprecated field',
                        optional: true,
                        injections: [],
                        skipValidation: false,
                        deprecated: true
                    )
                    ->param(
                        key: 'newField',
                        default: null,
                        validator: new Text(100),
                        description: 'New field',
                        optional: false,
                        injections: [],
                        skipValidation: false,
                        deprecated: false
                    );
            }
        };

        $params = $action->getParams();

        $this->assertTrue($params['oldField']['deprecated']);
        $this->assertFalse($params['newField']['deprecated']);
    }

    /**
     * Test deprecated defaults to false
     */
    public function testParamDeprecatedDefaultsFalse(): void
    {
        $action = new class () extends Action {
            public function __construct()
            {
                $this
                    ->param(
                        key: 'normalField',
                        default: '',
                        validator: new Text(100),
                        description: 'Normal field'
                    );
            }
        };

        $params = $action->getParams();

        $this->assertArrayHasKey('deprecated', $params['normalField']);
        $this->assertFalse($params['normalField']['deprecated']);
    }

    /**
     * Test example parameter is stored correctly
     */
    public function testParamExample(): void
    {
        $action = new class () extends Action {
            public function __construct()
            {
                $this
                    ->param(
                        key: 'email',
                        default: null,
                        validator: new Text(255),
                        description: 'User email address',
                        optional: false,
                        injections: [],
                        skipValidation: false,
                        deprecated: false,
                        example: 'user@example.com'
                    );
            }
        };

        $params = $action->getParams();

        $this->assertEquals('user@example.com', $params['email']['example']);
    }

    /**
     * Test example defaults to empty string
     */
    public function testParamExampleDefaultsToEmptyString(): void
    {
        $action = new class () extends Action {
            public function __construct()
            {
                $this
                    ->param(
                        key: 'field',
                        default: '',
                        validator: new Text(100),
                        description: 'Field without example'
                    );
            }
        };

        $params = $action->getParams();

        $this->assertArrayHasKey('example', $params['field']);
        $this->assertEquals('', $params['field']['example']);
    }

    /**
     * Test example with various types of content
     */
    public function testParamExampleWithVariousContent(): void
    {
        $action = new class () extends Action {
            public function __construct()
            {
                $this
                    ->param(
                        key: 'jsonField',
                        default: null,
                        validator: new Text(1000),
                        description: 'JSON field',
                        optional: false,
                        injections: [],
                        skipValidation: false,
                        deprecated: false,
                        example: '{"name": "John", "age": 30}'
                    )
                    ->param(
                        key: 'urlField',
                        default: null,
                        validator: new Text(500),
                        description: 'URL field',
                        optional: false,
                        injections: [],
                        skipValidation: false,
                        deprecated: false,
                        example: 'https://example.com/path?query=value'
                    )
                    ->param(
                        key: 'numericString',
                        default: null,
                        validator: new Text(20),
                        description: 'Numeric string field',
                        optional: false,
                        injections: [],
                        skipValidation: false,
                        deprecated: false,
                        example: '12345'
                    );
            }
        };

        $params = $action->getParams();

        $this->assertEquals('{"name": "John", "age": 30}', $params['jsonField']['example']);
        $this->assertEquals('https://example.com/path?query=value', $params['urlField']['example']);
        $this->assertEquals('12345', $params['numericString']['example']);
    }

    /**
     * Test all new parameters together
     */
    public function testAllNewParamsTogether(): void
    {
        $action = new class () extends Action {
            public function __construct()
            {
                $this
                    ->param(
                        key: 'complexParam',
                        default: null,
                        validator: new Text(1000),
                        description: 'A complex parameter with all options',
                        optional: true,
                        injections: [],
                        skipValidation: true,
                        deprecated: true,
                        example: '{"id": "123", "data": "test"}',
                        model: 'complexModel'
                    );
            }
        };

        $params = $action->getParams();
        $options = $action->getOptions();

        // Check params array
        $this->assertArrayHasKey('complexParam', $params);
        $this->assertEquals('complexModel', $params['complexParam']['model']);
        $this->assertTrue($params['complexParam']['skipValidation']);
        $this->assertTrue($params['complexParam']['deprecated']);
        $this->assertEquals('{"id": "123", "data": "test"}', $params['complexParam']['example']);
        $this->assertTrue($params['complexParam']['optional']);
        $this->assertEquals('A complex parameter with all options', $params['complexParam']['description']);

        // Check options array
        $this->assertArrayHasKey('param:complexParam', $options);
        $this->assertEquals('complexModel', $options['param:complexParam']['model']);
        $this->assertTrue($options['param:complexParam']['skipValidation']);
        $this->assertTrue($options['param:complexParam']['deprecated']);
        $this->assertEquals('{"id": "123", "data": "test"}', $options['param:complexParam']['example']);
        $this->assertEquals('param', $options['param:complexParam']['type']);
    }

    /**
     * Test param method returns self for chaining
     */
    public function testParamReturnsSelfForChaining(): void
    {
        $action = new class () extends Action {
            public function __construct()
            {
                $result = $this
                    ->param(
                        key: 'first',
                        default: null,
                        validator: new Text(100),
                        description: 'First param',
                        optional: false,
                        injections: [],
                        skipValidation: false,
                        deprecated: false,
                        example: 'example1',
                        model: 'model1'
                    );

                // Store reference for testing
                $this->chainResult = $result;
            }

            public $chainResult;
        };

        $this->assertInstanceOf(Action::class, $action->chainResult);
        $this->assertSame($action, $action->chainResult);
    }

    /**
     * Test model parameter with empty string
     */
    public function testParamModelWithEmptyString(): void
    {
        $action = new class () extends Action {
            public function __construct()
            {
                $this
                    ->param(
                        key: 'emptyModelField',
                        default: null,
                        validator: new Text(100),
                        description: 'Field with empty model string',
                        optional: false,
                        injections: [],
                        skipValidation: false,
                        deprecated: false,
                        example: 'test',
                        model: ''
                    );
            }
        };

        $params = $action->getParams();

        $this->assertEquals('', $params['emptyModelField']['model']);
    }

    /**
     * Test options contain all param properties
     */
    public function testOptionsContainAllParamProperties(): void
    {
        $action = new class () extends Action {
            public function __construct()
            {
                $this
                    ->param(
                        key: 'testParam',
                        default: 'defaultValue',
                        validator: new Text(100),
                        description: 'Test description',
                        optional: true,
                        injections: ['request'],
                        skipValidation: true,
                        deprecated: true,
                        example: 'exampleValue',
                        model: 'testModel'
                    );
            }
        };

        $options = $action->getOptions();
        $paramOptions = $options['param:testParam'];

        $this->assertEquals('defaultValue', $paramOptions['default']);
        $this->assertInstanceOf(Text::class, $paramOptions['validator']);
        $this->assertEquals('Test description', $paramOptions['description']);
        $this->assertTrue($paramOptions['optional']);
        $this->assertEquals(['request'], $paramOptions['injections']);
        $this->assertTrue($paramOptions['skipValidation']);
        $this->assertTrue($paramOptions['deprecated']);
        $this->assertEquals('exampleValue', $paramOptions['example']);
        $this->assertEquals('testModel', $paramOptions['model']);
        $this->assertEquals('param', $paramOptions['type']);
    }

    /**
     * Test params and options consistency
     */
    public function testParamsAndOptionsConsistency(): void
    {
        $action = new class () extends Action {
            public function __construct()
            {
                $this
                    ->param(
                        key: 'consistencyTest',
                        default: null,
                        validator: new Text(50),
                        description: 'Consistency test field',
                        optional: false,
                        injections: [],
                        skipValidation: false,
                        deprecated: false,
                        example: 'consistent',
                        model: 'consistentModel'
                    );
            }
        };

        $params = $action->getParams();
        $options = $action->getOptions();

        // All param values should match between params and options (except 'type' which is only in options)
        $this->assertEquals($params['consistencyTest']['model'], $options['param:consistencyTest']['model']);
        $this->assertEquals($params['consistencyTest']['skipValidation'], $options['param:consistencyTest']['skipValidation']);
        $this->assertEquals($params['consistencyTest']['deprecated'], $options['param:consistencyTest']['deprecated']);
        $this->assertEquals($params['consistencyTest']['example'], $options['param:consistencyTest']['example']);
        $this->assertEquals($params['consistencyTest']['default'], $options['param:consistencyTest']['default']);
        $this->assertEquals($params['consistencyTest']['description'], $options['param:consistencyTest']['description']);
        $this->assertEquals($params['consistencyTest']['optional'], $options['param:consistencyTest']['optional']);
        $this->assertEquals($params['consistencyTest']['injections'], $options['param:consistencyTest']['injections']);
    }

    /**
     * Test model with special characters in name
     */
    public function testModelWithSpecialCharacters(): void
    {
        $action = new class () extends Action {
            public function __construct()
            {
                $this
                    ->param(
                        key: 'specialModelField',
                        default: null,
                        validator: new Text(100),
                        description: 'Field with special model name',
                        optional: false,
                        injections: [],
                        skipValidation: false,
                        deprecated: false,
                        example: 'test',
                        model: 'model.with.dots'
                    )
                    ->param(
                        key: 'anotherSpecialField',
                        default: null,
                        validator: new Text(100),
                        description: 'Another field',
                        optional: false,
                        injections: [],
                        skipValidation: false,
                        deprecated: false,
                        example: 'test',
                        model: 'model-with-dashes'
                    );
            }
        };

        $params = $action->getParams();

        $this->assertEquals('model.with.dots', $params['specialModelField']['model']);
        $this->assertEquals('model-with-dashes', $params['anotherSpecialField']['model']);
    }

    /**
     * Test that overwriting a param with same key updates all values
     */
    public function testOverwritingParamUpdatesAllValues(): void
    {
        $action = new class () extends Action {
            public function __construct()
            {
                $this
                    ->param(
                        key: 'overwriteTest',
                        default: 'first',
                        validator: new Text(50),
                        description: 'First definition',
                        optional: false,
                        injections: [],
                        skipValidation: false,
                        deprecated: false,
                        example: 'first_example',
                        model: 'firstModel'
                    )
                    ->param(
                        key: 'overwriteTest',
                        default: 'second',
                        validator: new Text(100),
                        description: 'Second definition',
                        optional: true,
                        injections: ['response'],
                        skipValidation: true,
                        deprecated: true,
                        example: 'second_example',
                        model: 'secondModel'
                    );
            }
        };

        $params = $action->getParams();
        $options = $action->getOptions();

        // Should have the second definition's values
        $this->assertEquals('second', $params['overwriteTest']['default']);
        $this->assertEquals('Second definition', $params['overwriteTest']['description']);
        $this->assertTrue($params['overwriteTest']['optional']);
        $this->assertEquals(['response'], $params['overwriteTest']['injections']);
        $this->assertTrue($params['overwriteTest']['skipValidation']);
        $this->assertTrue($params['overwriteTest']['deprecated']);
        $this->assertEquals('second_example', $params['overwriteTest']['example']);
        $this->assertEquals('secondModel', $params['overwriteTest']['model']);
    }
}
