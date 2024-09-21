<?php

namespace Azuriom\Plugin\Vote\Verification;

use Azuriom\Models\User;
use Azuriom\Plugin\Vote\Models\Site;
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
    private ?Closure $pingbackCallback = null;

    /**
     * The method to retrieve the server id from the vote url.
     */
    private Closure|string|null $retrieveKeyMethod = null;

    /**
     * The method to transform the pending request.
     */
    private ?Closure $transformRequest = null;

    private function __construct(string $siteDomain)
    {
        $this->siteDomain = $siteDomain;
    }

    /**
     * Create a new VoteVerifier instance for the following domain (without http(s) or www).
     */
    public static function for(string $siteDomain): self
    {
        return new self($siteDomain);
    }

    /**
     * Set the API verification url for this vote site.
     */
    public function setApiUrl(string $apiUrl): self
    {
        $this->apiUrl = $apiUrl;

        return $this;
    }

    public function retrieveKeyByRegex(string $regex, int $index = 1): self
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

    public function retrieveKeyByCallback(Closure $callback): self
    {
        $this->retrieveKeyMethod = $callback;

        return $this;
    }

    public function requireKey(string $type): self
    {
        $this->retrieveKeyMethod = $type;

        return $this;
    }

    public function transformRequest(Closure $callback): self
    {
        $this->transformRequest = $callback;

        return $this;
    }

    public function verifyByCallback(callable $verification): self
    {
        $this->verificationMethod = $verification;

        return $this;
    }

    public function verifyByJson(string $key, $exceptedValue): self
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

    public function verifyByValue(string $value): self
    {
        $this->verificationMethod = function (Response $response) use ($value) {
            return $response->body() === $value;
        };

        return $this;
    }

    public function verifyByDifferentValue(string $value): self
    {
        $this->verificationMethod = function (Response $response) use ($value) {
            return $response->body() !== $value;
        };

        return $this;
    }

    public function verifyByPingback(Closure $callback): self
    {
        $this->pingbackCallback = $callback;

        $this->verificationMethod = function (User $user, Site $site, array $ips) {
            $cacheKey = "vote.sites.{$this->siteDomain}.";

            $ip = Arr::first($ips, fn (string $ip) => Cache::pull($cacheKey.$ip));

            return $ip !== null || Cache::pull($cacheKey.$user->id);
        };

        return $this;
    }

    public function executePingbackCallback(Request $request)
    {
        $sites = Site::where('url', 'LIKE', '%'.$this->siteDomain.'/%')->get();

        $result = $sites->map(fn (Site $site) => ($this->pingbackCallback)($request, $site))
            ->first(fn ($result) => $result !== null);

        if ($result === null) {
            return response()->noContent(400);
        }

        Cache::put("vote.sites.{$this->siteDomain}.".$result, true, now()->addMinutes(5));

        return response()->json(['status' => 'ok']);
    }

    public function hasPingback(): bool
    {
        return $this->pingbackCallback !== null;
    }

    public function verifyVote(Site $site, User $user, string $ip = ''): bool
    {
        $retrieveKeyMethod = $this->retrieveKeyMethod;
        $verificationMethod = $this->verificationMethod;

        $key = $this->requireVerificationKey()
            ? $site->verification_key
            : $retrieveKeyMethod($site->url);

        if ($key === null) {
            return true;
        }

        try {
            $ips = $this->getRequestIps($ip);

            if (empty($ips)) {
                $ips = [$ip];
            }

            if ($this->apiUrl === null) {
                return $verificationMethod($user, $site, $ips);
            }

            foreach ($ips as $userIp) {
                $url = str_replace([
                    '{server}', '{ip}', '{id}', '{name}',
                ], [
                    $key, $userIp, $user->game_id, $user->name,
                ], $this->apiUrl);

                $request = Http::asJson();

                if ($this->transformRequest !== null) {
                    $request = ($this->transformRequest)($request, $user, $site);
                }

                $res = $request->get($url);

                if ($verificationMethod($res, $user)) {
                    return true;
                }
            }

            return false;
        } catch (Exception) {
            return true;
        }
    }

    public function requireVerificationKey(): bool
    {
        return is_string($this->retrieveKeyMethod);
    }

    public function verificationTypeKey(): Closure|string|null
    {
        return $this->retrieveKeyMethod;
    }

    public function getSiteDomain(): string
    {
        return $this->siteDomain;
    }

    protected function getRequestIps(string $requestIp): ?array
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
