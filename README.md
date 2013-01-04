gangstagram
===========

A PHP library for Instagram

Developed October 2012 for Chirpify.com

This library of functions was built with the PHP CodeIgniter framework in mind, but could be used in a variety of object oriented projects.
HTTP_request is not required, it's simply a CI wrapper for CURL requests.

Released under a BSD license.

A few useful points

    The Realtime Updates (using the pubsubhubbub protocol) is notoriously leaky. If your application requires reliable updates from users', 
    poll them using the API, using the user's access token.

    To be able to set comments on behalf of users you now have to email apidevelopers[at]instagram.com and ask for permission. 



Chirpify Engineering Team
-------------------------

Chris Teso

Evan Reeves

Todd Gruener

G. Xavier Robillard

Cameron Brown

Ian VanNess



`Boom goes the dynamite.` 
