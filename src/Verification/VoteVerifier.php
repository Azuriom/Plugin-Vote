<?php

namespace Azuriom\Plugin\Vote\Verification;

use Azuriom\Models\User;
use Closure;
use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class VoteVerifier
{
    /**
     * The domain (without www) of this site.
     */
    public string $siteDomain;

    /**
     * The api url of this site.
     */
    private ?string $apiUrl = null;

    /**
     * The method to verify is a user voted on this site.
     */
    private Closure|string|null $verificationMethod = null;

    /**
     * The method to handle websites pingback.
     */
    private Closure|null $pingbackCallback = null;

    /**
     * The method to retrieve the server id from the vote url.
     */
    private Closure|string|null $retrieveKeyMethod = null;

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

    public function retrieveKeyByDynamically(Closure $method)
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

            return $value == value($exceptedValue, $value, $json);
        };

        return $this;
    }

    public function verifyByValue(string $value)
    {
        $this->verificationMethod = function (Response $response) use ($value) {
            return $response->body() === $value;
        };

        return $this;
    }

    public function verifyByDifferentValue(string $value)
    {
        $this->verificationMethod = function (Response $response) use ($value) {
            return $response->body() !== $value;
        };

        return $this;
    }

    public function verifyByPingback(Closure $callback)
    {
        $this->pingbackCallback = $callback;

        $this->verificationMethod = function (string $ip) {
            $ping = Cache::pull("vote.sites.{$this->siteDomain}.{$ip}");

            return $ping === true;
        };

        return $this;
    }

    public function executePingbackCallback(Request $request)
    {
        return ($this->pingbackCallback)($request);
    }

    public function hasPingback()
    {
        return $this->pingbackCallback !== null;
    }

    public function verifyVote(string $voteUrl, User $user, string $requestIp = '', string $voteKey = null)
    {
        $retrieveKeyMethod = $this->retrieveKeyMethod;
        $verificationMethod = $this->verificationMethod;

        $key = $this->requireVerificationKey() ? $voteKey : $retrieveKeyMethod($voteUrl);

        if ($key === null) {
            return true;
        }

        try {
            $ips = $this->getRequestIps($requestIp);

            if (empty($ips)) {
                $ips = [$requestIp];
            }

            foreach ($ips as $ip) {
                if ($this->apiUrl === null) {
                    if ($verificationMethod($ip)) {
                        return true;
                    }
                    continue;
                }

                $url = str_replace([
                    '{server}', '{ip}', '{id}', '{name}',
                ], [
                    $key, $ip, $user->game_id, $user->name,
                ], $this->apiUrl);

                $response = Http::get($url);

                if ($verificationMethod($response)) {
                    return true;
                }
            }

            return false;
        } catch (Exception) {
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

    protected function getRequestIps(string $requestIp)
    {
        if (! setting('vote.ipv4-v6-compatibility', true)) {
            return null;
        }

        try {
            return Http::get('https://ipv6-adapter.com/api/v1/fetch?ip='.$requestIp)->json('ips');
        } catch (Exception) {
            return null;
        }
    }
}
