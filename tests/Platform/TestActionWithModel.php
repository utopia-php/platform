<?php

namespace Utopia\Tests;

use Utopia\Platform\Action;
use Utopia\Validator\Text;

/**
 * Test action demonstrating the use of the model parameter
 * for documenting request body schema references
 */
class TestActionWithModel extends Action
{
    public function __construct()
    {
        $this->httpPath = '/model-test';
        $this->httpMethod = 'POST';
        $this->groups(['test']);

        $this
            ->desc('Test action with model parameter')
            ->param(
                key: 'user',
                default: null,
                validator: new Text(5000),
                description: 'User object in JSON format',
                optional: false,
                injections: [],
                skipValidation: false,
                deprecated: false,
                example: '{"name": "John Doe", "email": "john@example.com"}',
                model: 'user'
            )
            ->param(
                key: 'settings',
                default: null,
                validator: new Text(2000),
                description: 'User settings object',
                optional: true,
                injections: [],
                skipValidation: false,
                deprecated: false,
                example: '{"theme": "dark", "notifications": true}',
                model: 'userSettings'
            )
            ->param(
                key: 'legacyData',
                default: null,
                validator: new Text(1000),
                description: 'Legacy data format (deprecated)',
                optional: true,
                injections: [],
                skipValidation: true,
                deprecated: true,
                example: '{"old_format": true}',
                model: 'legacyData'
            )
            ->param(
                key: 'simpleField',
                default: '',
                validator: new Text(100),
                description: 'A simple text field without model'
            )
            ->inject('response')
            ->callback(function ($user, $settings, $legacyData, $simpleField, $response) {
                $this->action($user, $settings, $legacyData, $simpleField, $response);
            });
    }

    /**
     * @param mixed $user
     * @param mixed $settings
     * @param mixed $legacyData
     * @param mixed $simpleField
     * @param mixed $response
     */
    public function action($user, $settings, $legacyData, $simpleField, $response): void
    {
        $response->json([
            'success' => true,
            'user' => $user,
            'settings' => $settings,
            'legacyData' => $legacyData,
            'simpleField' => $simpleField,
        ]);
    }
}
