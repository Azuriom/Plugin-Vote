<?php

namespace Azuriom\Plugin\Vote\Verification;

use Azuriom\Models\User;
use Azuriom\Plugin\Vote\Models\Site;
use Carbon\Carbon;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class VoteChecker
{
    /**
     * The votes sites supporting verification.
     *
     * @var array
     */
    private array $sites = [];

    public function __construct()
    {
        $this->register(VoteVerifier::for('serveur-minecraft-vote.fr')
            ->setApiUrl('https://serveur-minecraft-vote.fr/api/v1/servers/{server}/vote/{ip}')
            ->retrieveKeyByRegex('/^serveur-minecraft-vote\.fr\/serveurs\/[\w-]+\.(\d+).*/')
            ->verifyByJson('canVote', false));

        $this->register(VoteVerifier::for('liste-serv-minecraft.fr')
            ->setApiUrl('https://liste-serv-minecraft.fr/api/check?server={server}&ip={ip}')
            ->retrieveKeyByRegex('/^liste-serv-minecraft\.fr\/serveur\?id=(\d+)/')
            ->verifyByJson('status', '200'));

        $this->register(VoteVerifier::for('serveurs-minecraft.org')
            ->setApiUrl('https://www.serveurs-minecraft.org/api/is_valid_vote.php?id={server}&ip={ip}&duration=5&format=json')
            ->retrieveKeyByRegex('/^serveurs-minecraft\.org\/vote\.php\?id=(\d+)/')
            ->verifyByJson('votes', '1'));

        $this->register(VoteVerifier::for('serveurs-mc.net')
            ->setApiUrl('https://serveurs-mc.net/api/hasVote/{server}/{ip}/10')
            ->retrieveKeyByRegex('/^serveurs-mc\.net\/serveur\/(\d+)/')
            ->verifyByJson('hasVote', true));

        $this->register(VoteVerifier::for('serveur-minecraft.com')
            ->setApiUrl('https://serveur-minecraft.com/api/1/vote/{server}/{ip}/json')
            ->retrieveKeyByRegex('/^serveur-minecraft\.com\/(\d+)/')
            ->verifyByJson('vote', '1'));

        $this->register(VoteVerifier::for('serveur-minecraft.fr')
            ->setApiUrl('https://serveur-minecraft.fr/api-{server}_{ip}.json')
            ->retrieveKeyByRegex('/^serveur-minecraft\.fr\/[\w-]+\.(\d+)/')
            ->verifyByJson('status', 'Success'));

        $this->register(VoteVerifier::for('serveursminecraft.org')
            ->setApiUrl('https://www.serveursminecraft.org/sm_api/peutVoter.php?id={server}&ip={ip}')
            ->retrieveKeyByRegex('/^serveursminecraft\.org\/serveur\/(\d+)/')
            ->verifyByDifferentValue('true'));

        $this->register(VoteVerifier::for('serveurs-minecraft.com')
            ->setApiUrl('https://serveurs-minecraft.com/api.php?Classement={server}&ip={ip}')
            ->retrieveKeyByRegex('/^serveurs-minecraft\.com\/serveur-minecraft\.php\?Classement=([^\/]+)/')
            ->verifyByJson('lastVote.date', function ($lastVoteTime, $json) {
                if (! $lastVoteTime) {
                    return false;
                }

                $now = Carbon::parse(Arr::get($json, 'reqVote.date')) ?? now();

                return Carbon::parse($lastVoteTime)->addMinutes(5)->isAfter($now) ? $lastVoteTime : false;
            }));

        $this->register(VoteVerifier::for('serveur-multigames.net')
            ->setApiUrl('https://serveur-multigames.net/api/{server}?ip={ip}')
            ->retrieveKeyByRegex('/^serveur-multigames\.net\/\w+\/([\w-]+)/')
            ->verifyByValue('true'));

        $this->register(VoteVerifier::for('liste-serveurs.fr')
            ->setApiUrl('https://www.liste-serveurs.fr/api/checkVote/{server}/{ip}')
            ->retrieveKeyByRegex('/^liste-serveurs\.fr\/[\w-]+\.(\d+)/')
            ->verifyByJson('success', true));

        $this->register(VoteVerifier::for('serveur-top.fr')
            ->setApiUrl('https://serveur-top.fr/api/checkVote/{server}/{ip}')
            ->retrieveKeyByRegex('/^serveur-top\.fr\/[\w-]+\.(\d+)/')
            ->verifyByJson('success', true));

        $this->register(VoteVerifier::for('liste-minecraft-serveurs.com')
            ->setApiUrl('https://www.liste-minecraft-serveurs.com/Api/Worker/id_server/{server}/ip/{ip}')
            ->retrieveKeyByRegex('/^liste-minecraft-serveurs\.com\/Serveur\/(\d+)/')
            ->verifyByJson('result', 202));

        $this->register(VoteVerifier::for('topserveursminecraft.com')
            ->setApiUrl('https://topserveursminecraft.com/api/server={server}&ip={ip}')
            ->retrieveKeyByRegex('/^topserveursminecraft\.com\/[\w]+\.(\d+)/')
            ->verifyByJson('voted', 1));

        $this->register(VoteVerifier::for('topserv.fr')
            ->setApiUrl('https://topserv.fr/api/vote/check-ip?ip={ip}&serverId={server}')
            ->retrieveKeyByRegex('/^topserv\.fr\/[\w-]+\/server\/(\d+)/')
            ->verifyByJson('hasVoted', true));

        $this->register(VoteVerifier::for('yserveur.fr')
            ->setApiUrl('https://yserveur.fr/api/vote/{server}/{ip}')
            ->retrieveKeyByRegex('/^yserveur\.fr\/serveur\/(\d+)')
            ->verifyByJson('vote', true));

        $this->register(VoteVerifier::for('liste-serveur.fr')
            ->setApiUrl('https://www.liste-serveur.fr/api/hasVoted/{server}/{ip}')
            ->requireKey('secret')
            ->verifyByJson('hasVoted', true));

        $this->register(VoteVerifier::for('minecraft-italia.it/')
            ->setApiUrl('https://api.minecraft-italia.it/v6/server-info/{server_name}?key={server}')
            ->requireKey('api_key')
            ->verifyByCallback(function (Response $response, User $user) {
                $votes = collect($response->json('userVotes', []));

                if ($user->game_id === null) {
                    return $votes->contains('minecraftNickname', $user->name);
                }

                return $votes->contains('minecraftUUID', $user->game_id);
            }));

        $this->register(VoteVerifier::for('minecraft-server.eu')
            ->setApiUrl('https://minecraft-server.eu/api/v1/?object=votes&element=claim&key={server}&username={name}')
            ->requireKey('api_key')
            ->verifyByValue(1));

        $this->register(VoteVerifier::for('minecraft-mp.com')
            ->setApiUrl('https://minecraft-mp.com/api/?object=votes&element=claim&key={server}&username={name}')
            ->requireKey('api_key')
            ->verifyByValue(1));

        $this->register(VoteVerifier::for('minecraftpocket-servers.com')
            ->setApiUrl('https://minecraftpocket-servers.com/api/?object=votes&element=claim&key={server}&username={name}')
            ->requireKey('api_key')
            ->verifyByValue(1));

        $listForge = [
            'gmod-servers.com',
            'ark-servers.net',
            'rust-servers.net',
            'tf2-servers.com',
            'counter-strike-servers.net',
        ];

        foreach ($listForge as $domain) {
            $this->register(VoteVerifier::for($domain)
                ->setApiUrl("https://{$domain}/api/?object=votes&element=claim&key={server}&steamid={id}")
                ->requireKey('api_key')
                ->verifyByValue(1));
        }

        $this->register(VoteVerifier::for('trackyserver.com')
            ->setApiUrl('http://www.api.trackyserver.com/vote/?action=claim&key={server}&steamid={id}')
            ->requireKey('api_key')
            ->verifyByValue(1));

        $this->register(VoteVerifier::for('topminecraft.click')
            ->setApiUrl('https://topminecraft.click/api/vote/v1/{server}/{ip}')
            ->requireKey('api_key')
            ->verifyByJson('has_vote', true));

        $this->register(VoteVerifier::for('topminecraft.io')
            ->setApiUrl('https://topminecraft.io/api/vote/{server}/{ip}')
            ->requireKey('api_key')
            ->verifyByJson('status', 'success'));

        $this->register(VoteVerifier::for('serveur-prive.net')
            ->setApiUrl('https://serveur-prive.net/api/vote/json/{server}/{ip}')
            ->requireKey('api_key')
            ->verifyByJson('status', '1'));

        $this->register(VoteVerifier::for('top-serveurs.net')
            ->setApiUrl('https://api.top-serveurs.net/v1/votes/check-ip?server_token={server}&ip={ip}')
            ->requireKey('token')
            ->verifyByJson('code', 200));

        $this->register(VoteVerifier::for('minecraft-top.com')
            ->setApiUrl('https://api.minecraft-top.com/v1/vote/{ip}/{server}')
            ->requireKey('token')
            ->verifyByJson('vote', 1));

        $this->register(VoteVerifier::for('liste-serveurs-minecraft.org')
            ->setApiUrl('https://api.liste-serveurs-minecraft.org/vote/vote_verification.php?server_id={server}&ip={ip}&duration=5')
            ->requireKey('server_id')
            ->verifyByValue('1'));

        $this->register(VoteVerifier::for('gtop100.com')
            ->requireKey('api_key')
            ->verifyByPingback(function (Request $request) {
                $key = $request->input('pingbackkey');

                abort_if($key === null, 401);
                abort_if(! Site::where('verification_key', $key)->exists(), 403);

                if ($request->input('Successful') === '0') {
                    Cache::put("vote.sites.gtop100.com.{$request->input('VoterIP')}", true, now()->addMinutes(5));

                    return response()->noContent();
                }

                return response()->noContent(403);
            }));
    }

    public function hasVerificationForSite(string $domain)
    {
        return array_key_exists($domain, $this->sites);
    }

    public function getVerificationForSite(string $domain)
    {
        return $this->sites[$domain] ?? null;
    }

    /**
     * Try to verify if the user voted if the website is supported.
     * In case of failure or unsupported website true is returned.
     *
     * @param  Site  $site
     * @param  \Azuriom\Models\User  $user
     * @param  string  $requestIp
     * @return bool
     */
    public function verifyVote(Site $site, User $user, string $requestIp)
    {
        $host = $this->parseHostFromUrl($site->url);

        if ($host === null) {
            return true;
        }

        $verification = $this->getVerificationForSite($host);

        if ($verification === null) {
            return true;
        }

        return $verification->verifyVote($site->url, $user, $requestIp, $site->verification_key);
    }

    protected function register(VoteVerifier $verifier)
    {
        $this->sites[$verifier->getSiteDomain()] = $verifier;
    }

    public function parseHostFromUrl(string $rawUrl)
    {
        $url = parse_url($rawUrl);

        if ($url === false || ! array_key_exists('host', $url)) {
            return null;
        }

        $host = $url['host'];

        if (Str::startsWith($host, 'www.')) {
            $host = substr($host, 4);
        }

        return $host;
    }

    /**
     * Returns the list of registered sites.
     *
     * @return array
     */
    public function getSites()
    {
        return $this->sites;
    }
}
