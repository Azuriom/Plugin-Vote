<?php

namespace Azuriom\Plugin\Vote\Verification;

use Azuriom\Models\User;
use Closure;
use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class VoteVerifier
{
    /**
     * The domain (without www) of this site.
     *
     * @var string
     */
    private $siteDomain;

    /**
     * The api url of this site.
     *
     * @var string
     */
    private $apiUrl;

    /**
     * The method to verify is a user voted on this site.
     *
     * @var string|callable
     */
    private $verificationMethod;

    /**
     * The method to handle websites pingback
     * @var callable|null
     */
    private $pingbackCallback;

    /**
     * The method to retrieve the server id from the vote url.
     *
     * @var string|callable|null
     */
    private $retrieveKeyMethod;

    private function __construct(string $siteDomain)
    {
        $this->siteDomain = $siteDomain;
    }

    /**
     * Create a new VoteVerifier instance for the following domain (without http(s) or www).
     *
     * @param  string  $siteDomain
     * @return \Azuriom\Plugin\Vote\Verification\VoteVerifier
     */
    public static function for(string $siteDomain)
    {
        return new self($siteDomain);
    }

    /**
     * Set the API verification url for this vote site.
     *
     * @param  string  $apiUrl
     * @return \Azuriom\Plugin\Vote\Verification\VoteVerifier
     */
    public function setApiUrl(string $apiUrl)
    {
        $this->apiUrl = $apiUrl;

        return $this;
    }

    public function retrieveKeyByRegex(string $regex, int $index = 1)
    {
        $this->retrieveKeyMethod = function ($url) use ($regex, $index) {
            $url = str_replace(['http://', 'https://'], '', $url);

            if (Str::startsWith($url, 'www.')) {
                $url = substr($url, 4);
            }

            if (! preg_match($regex, $url, $matches)) {
                return null;
            }

            return $matches[$index] ?? null;
        };

        return $this;
    }

    public function retrieveKeyByDynamically(callable $method)
    {
        $this->retrieveKeyMethod = $method;

        return $this;
    }

    public function requireKey(string $type)
    {
        $this->retrieveKeyMethod = $type;

        return $this;
    }

    public function verifyByJson(string $key, $exceptedValue)
    {
        $this->verificationMethod = function (Response $response) use ($key, $exceptedValue) {
            $json = $response->json();

            $value = Arr::get($json, $key);

            if ($value === null) {
                return false;
            }

            return $value == ($exceptedValue instanceof Closure ? $exceptedValue($value, $json) : $exceptedValue);
        };

        return $this;
    }

    public function verifyByValue(string $value)
    {
        $this->verificationMethod = function (Response $response) use ($value) {
            return $response->body() == $value;
        };

        return $this;
    }

    public function verifyByDifferentValue(string $value)
    {
        $this->verificationMethod = function (Response $response) use ($value) {
            return $response->body() != $value;
        };

        return $this;
    }

    public function verifyByPingback()
    {
        $this->verificationMethod = function () {
            $user = auth()->user();
            if ($user->last_login_ip === '127.0.0.1') {
                $ping = cache(["{$this->siteDomain}-127.0.0.1"]);;
            } else {
                $ping = cache(["{$this->siteDomain}-{$user->last_login_ip}"]);
            }

            return $ping ?? false;
        };

        return $this;
    }

    public function setPingbackCallback(callable $callback)
    {
        $this->pingbackCallback = $callback;

        return $this;
    }

    public function getPingbackCallback()
    {
        return $this->pingbackCallback;
    }

    public function verifyVote(string $voteUrl, User $user, string $ip = '', string $voteKey = null)
    {
        $retrieveKeyMethod = $this->retrieveKeyMethod;
        $verificationMethod = $this->verificationMethod;

        $key = $this->requireVerificationKey() ? $voteKey : $retrieveKeyMethod($voteUrl);

        if ($key === null) {
            return true;
        }

        $url = str_replace([
            '{server}', '{ip}', '{id}', '{name}',
        ], [
            $key, $ip, $user->game_id, $user->name,
        ], $this->apiUrl);

        try {
            if ($this->apiUrl) {
                $response = Http::get($url);

                return $verificationMethod($response);
            }

            return $verificationMethod();
        } catch (Exception $e) {
            return true;
        }
    }

    public function requireVerificationKey()
    {
        return is_string($this->retrieveKeyMethod);
    }

    public function verificationTypeKey()
    {
        return $this->retrieveKeyMethod;
    }

    public function getSiteDomain()
    {
        return $this->siteDomain;
    }
}
