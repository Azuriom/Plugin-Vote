<?php

namespace Azuriom\Plugin\Vote\Verification;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class VoteChecker
{
    /**
     * The votes sites supporting verification.
     *
     * @var array
     */
    private $sites = [];

    public function __construct()
    {
        $this->register(VoteVerifier::for('liste-serv-minecraft.fr')
            ->setApiUrl('https://liste-serv-minecraft.fr/api/check?server={server}&ip={ip}')
            ->retrieveKeyByRegex('/^https:\/\/liste-serv-minecraft\.fr\/serveur\?id=(\d+)/')
            ->verifyByJson('status', '200'));

        $this->register(VoteVerifier::for('serveurs-minecraft.org')
            ->setApiUrl('https://www.serveurs-minecraft.org/api/is_valid_vote.php?id={server}&ip={ip}&duration=5&format=json')
            ->retrieveKeyByRegex('/^https:\/\/www\.serveurs-minecraft\.org\/vote\.php\?id=(\d+)/')
            ->verifyByJson('votes', '1'));

        $this->register(VoteVerifier::for('serveur-minecraft.fr')
            ->setApiUrl('https://serveur-minecraft.fr/api-{server}_{ip}.json')
            ->retrieveKeyByRegex('/^https:\/\/serveur-minecraft\.fr\/.+\.(\d+)/')
            ->verifyByJson('status', 'Success'));

        $this->register(VoteVerifier::for('serveursminecraft.org')
            ->setApiUrl('https://www.serveursminecraft.org/sm_api/peutVoter.php?id={server}&ip={ip}')
            ->retrieveKeyByRegex('/^https:\/\/www\.serveursminecraft\.org\/serveur\/(\d+)/')
            ->verifyByDifferentValue('true'));

        $this->register(VoteVerifier::for('serveurs-minecraft.com')
            ->setApiUrl('https://serveurs-minecraft.com/api.php?Classement={server}&ip={ip}')
            ->retrieveKeyByRegex('/^https:\/\/serveurs-minecraft\.com\/serveur-minecraft\.php\?Classement=([^\/]+)/')
            ->verifyByJson('lastVote.date', function ($lastVoteTime, $json) {
                if (! $lastVoteTime) {
                    return false;
                }

                $now = Carbon::parse(Arr::get($json, 'reqVote.date')) ?? now();

                return Carbon::parse($lastVoteTime)->addMinutes(5)->isAfter($now) ? $lastVoteTime : false;
            }));

        $this->register(VoteVerifier::for('serveur-multigames.net')
            ->setApiUrl('https://serveur-multigames.net/api/{server}?ip={ip}')
            ->retrieveKeyByRegex('/^https:\/\/serveur-multigames\.net\/(.*)\/(.*)/', 2)
            ->verifyByValue('true'));

        $this->register(VoteVerifier::for('liste-serveurs.fr')
            ->setApiUrl('https://www.liste-serveurs.fr/api/checkVote/{server}/{ip}')
            ->retrieveKeyByRegex('/^https:\/\/www\.liste-serveurs\.fr\/[\w\d-]+\.(\d+)/', 2)
            ->verifyByJson('success', true));

        $this->register(VoteVerifier::for('liste-serveur.fr')
            ->setApiUrl('https://www.liste-serveur.fr/api/hasVoted/{server}/{ip}')
            ->verifyByJson('hasVoted', 'true')); // TODO get key

        $this->register(VoteVerifier::for('liste-serveurs-minecraft.org')
            ->setApiUrl('https://api.liste-serveurs-minecraft.org/vote/vote_verification.php?server_id={server}&ip={ip}}&duration=5')
            ->verifyByValue('1')); // TODO get key

        $this->register(VoteVerifier::for('serveur-prive.net')
            ->setApiUrl('https://serveur-prive.net/api/vote/json/{server}/{ip}')
            ->verifyByJson('status', '1')); // TODO get key

        $this->register(VoteVerifier::for('top-serveurs.net')
            ->setApiUrl('https://api.top-serveurs.net/v1/votes/check-ip?server_token={server}&ip={ip}')
            ->verifyByJson('code', '200')); // TODO get key

        $this->register(VoteVerifier::for('kiosque-serveur.net')
            ->setApiUrl('https://api.kiosque-serveur.net/v1/vote/{ip}/{server}')
            ->verifyByJson('vote', '1')); // TODO get key
    }

    public function hasVerificationForSite(string $domain)
    {
        return array_key_exists($domain, $this->sites);
    }

    /**
     * Try to verify if the user voted if the website is supported.
     * In case of failure or unsupported website true is returned.
     *
     * @param  string  $voteSite
     * @param  string  $userIp
     * @param  string  $userName
     * @return bool
     */
    public function verifyVote(string $voteSite, string $userIp, string $userName)
    {
        $url = parse_url($voteSite);

        if ($url === false) {
            return true;
        }

        $host = $url['host'];

        if (Str::startsWith($host, 'www.')) {
            $host = substr($host, 4);
        }

        if (! $this->hasVerificationForSite($host)) {
            return true;
        }

        return $this->sites[$host]->verifyVote($voteSite, $userIp, $userName);
    }

    protected function register(VoteVerifier $verifier)
    {
        $this->sites[$verifier->getSiteDomain()] = $verifier;
    }
}
