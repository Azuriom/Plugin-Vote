(function () {
    'use strict';

    // Local IPv4/IPv6 compatibility shim.
    //
    // This used to be pulled from a remote third-party script, which is now
    // resolved on the server side instead. We keep an empty client hook so
    // existing templates and the admin toggle stay in place without loading
    // any external code on /vote.

    if (typeof window !== 'undefined') {
        window.VoteIpCompatibility = window.VoteIpCompatibility || { local: true };
    }
}());
