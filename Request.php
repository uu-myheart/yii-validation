<?php

namespace Curia\YiiValidation;

use Curia\Collect\Arr;
use UnexpectedValueException;
use Zend\Diactoros\ServerRequest;
use function Zend\Diactoros\parseCookieHeader;
use function Zend\Diactoros\normalizeServer;
use function Zend\Diactoros\marshalUriFromSapi;
use function Zend\Diactoros\marshalMethodFromSapi;
use function Zend\Diactoros\marshalHeadersFromSapi;
use function Zend\Diactoros\normalizeUploadedFiles;
use function Zend\Diactoros\marshalProtocolVersionFromSapi;

class Request extends ServerRequest
{
    protected $rules = [];

    protected $messages = [];

    /**
     * Function to use to get apache request headers; present only to simplify mocking.
     *
     * @var callable
     */
    private static $apacheRequestHeaders = 'apache_request_headers';

    public function __construct( 
        array $server = null,
        array $query = null,
        array $body = null,
        array $cookies = null,
        array $files = null 
    ) {
        $server = normalizeServer(
            $server ?: $_SERVER,
            is_callable(self::$apacheRequestHeaders) ? self::$apacheRequestHeaders : null
        );
        $files   = normalizeUploadedFiles($files ?: $_FILES);
        $headers = marshalHeadersFromSapi($server);

        if (null === $cookies && array_key_exists('cookie', $headers)) {
            $cookies = parseCookieHeader($headers['cookie']);
        }

        parent::__construct(
            $server,
            $files,
            marshalUriFromSapi($server, $headers),
            marshalMethodFromSapi($server),
            'php://input',
            $headers,
            $cookies ?: $_COOKIE,
            $query ?: $_GET,
            $body ?: $_POST,
            marshalProtocolVersionFromSapi($server)
        );
    }

    /**
     * Validate the given request with the given rules.
     *
     * @param  \Curia\YiiValidation\Request  $request
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $customAttributes
     * @return array
     */
    public function validate(array $rules = [], array $messages = [])
    {
        $rules = $rules ?: $this->rules;
        $messages = $messages ?: $this->messages;

        $this->getValidationFactory()
             ->make($this->all(), $rules, $messages)
             ->validate();

        return $this->extractInputFromRules($this, $rules);
    }

    /**
     * Get a validation factory instance.
     *
     * @return \Illuminate\Contracts\Validation\Factory
     */
    protected function getValidationFactory()
    {
        return \Yii::$app->validator;
    }

    /**
     * Get the request input based on the given validation rules.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $rules
     * @return array
     */
    protected function extractInputFromRules(Request $request, array $rules)
    {
        return $this->only(collect($rules)->keys()->map(function ($rule) {
            return explode('.', $rule)[0];
        })->unique()->toArray());
    }

    /**
     * Get request value from specific key.
     * @param $key
     * @param null $default
     * @return null
     */
    public function get($key, $default = null)
    {
        $array = $this->all();
        
        return array_key_exists($key, $array) ? $array[$key] : $default;
    }

    /**
     * Get all request params.
     * @return array
     */
    public function all()
    {
        return array_merge(
            $this->getQueryParams(),
            $this->getParsedBody(),
            $this->getUploadedFiles()
        );
    }

    /**
     * Get request values from specific keys.
     * @param $keys
     * @return array
     */
    public function only($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();

        return array_intersect_key($this->all(), array_flip($keys));
    }

    /**
     * Get request values except specific keys.
     * @param $keys
     * @return array
     */
    public function except($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();

        $array = $this->all();
        
        Arr::forget($array, $keys);

        return $array;
    }

    /**
     * Check if an item or items exist in an array using "dot" notation.
     * @param $keys
     * @return bool
     */
    public function has($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();

        return Arr::has($this->all(), $keys);
    }

    /**
     * Determine the current the request is ajax or not
     * 
     * @return boolean
     */
    public function isAjax()
    {
        return 'XMLHttpRequest' == ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '');
    }
}