=== Plugin Name ===
Contributors: matrixagent
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=paypal%40matrixagents%2eorg&item_name=Dodge%20this%21%20bbSigPress&no_shipping=1&no_note=1&tax=0&currency_code=EUR&lc=DE&bn=PP%2dDonationsBF&charset=UTF%2d8
Tags: vblletin, wbb, vbb, siganture, bulletin board, web forum, forum, phpBB
Requires at least: 2.5
Tested up to: 2.7.1
Stable Tag: 0.4.8

Automatically updates your <a href="http://en.wikipedia.org/wiki/Internet_forum">bb</a>-signature(s) when publishing a new post.


== Description ==

Remember those web forums?
The big thing before everybody became a blogger..
Well, personally I'm still using them a lot. Of course, my signature includes a link to my blog. I bet that most of you also have their blog linked in a forum signature - "Visit my great blog".
Yeah, great catch phrase.. but no matter how great your link-text is, people will get used to it. As soon as they are used to your link they won't click it anymore - so what to do? Try out a new catch phrase every week?
What about this: Apart from the default link to your blog you could also give them a link to your most recent entry - together with it's title, category, publishing date, comment count..
And you don't have to do anything, even with multiple forums.
bbSigPress automatically updates your signatures when you publish a new post.

<b>Warning: This plugin is still in a very early beta state! It mostly works, but problems may occur! Use with caution!</b>

= Multiple Forums =

You can define as many forums to be updated in the settings as you want!
Please be aware that bbSigPress does basically the same as you would do - so it may take some seconds per forum.
You simply need to enter your login details, the URL to the forum and its' type - and of course the signature.

= Templates =

bbSigPress not only put's a boring link in your signature - you still have full control over the layout!
Just design and style your signature in the forum of your choice and afterwards copy the "BB-Code" to the template field of that forum.
Then put in the placeholders you want to use and you're done! You can use the following placeholders:

* %title% - obviously, the post's title.
* %author% - the display-name of the author.
* %permalink% - explanation needed?!
* %category% - also works if post is in multiple categories!
* %date% - is formatted using your WP settings.
* %time% - likewise.
* %commentcount% - the bigger, the better, right? ;)

= Supported forum software =

Although you may have thought so: bSigPress ain't a magician. There are probably hundreds of different bulletin board systems (BBS) out there.
And for each one of them I need to manually define how to update the signature in this particular system. If you wan't to learn more about it, read <a href="http://en.wikipedia.org/wiki/Internet_forum">here</a> and <a href="http://www.forummatrix.org/">there</a>.
Two big players are "vBulletin" and "Burning Board" and most of you should have seen a phpBB, as it is free. Currently the following BBS are supported:

* vBulletin 3
* Burning Board 2
* phpBB 3

More to come soon! Please visit the <a href="http://matrixagents.org/phpBB/">support forum</a> if you need help with a specific BBS!


== Installation ==

1. Unzip and upload the "bbSigPress" directory into "wp-content/plugins/"
2. Activate it on your plugin management page.
3. Setup your accounts under the new "bbSigPress" option of the settings menu
4. That's it, you're done!

= Update =

* Override the "bbSigPress" directory in "wp-content/plugins/"

Update from within Wordpress Admin Panel of course also works.



== Frequently Asked Questions ==

= Where can I get help? =

Please visit the <a href="http://matrixagents.org/phpBB/">discussion board</a>.


== Screenshots ==
&middot; <a href="http://blog.matrixagents.org/wp-content/uploads/2008/12/bbs-settings.jpg">Settings</a><br>
&middot; <a href="http://blog.matrixagents.org/wp-content/uploads/2008/12/bbs-e1.jpg">Example 1</a><br>
&middot; <a href="http://blog.matrixagents.org/wp-content/uploads/2008/12/bbs-e2.jpg">Example 2</a><br>


== Changelog ==


*Version 0.1*

* Basic functionality, not released to public, only vBulletin 3 supported

*Version 0.2*

* Added debug mode, added Burning Board 2 support

*Version 0.3*

* Charset problem fixed, added phpBB 3 support, first public release

*Version 0.4*

* Minor compatibility fix, added lots of "supress warning"-signs for better error dealing.

*Version 0.4.1*

* Shiny new help icon, thanks to <a href="http://blog.kwoth.net/">Henning</a>!
* Fixed a problem when special html characters occured in a username or password.

*Version 0.4.2*

* bbSigPress now remembers the last posting you have "submitted" to your signatures and won't do the update more than once. So if you just fix some typos in your most recent posting, the plugin won't do any useless operations. If you update the title, it will of course be updated in your signatures as well.

*Version 0.4.3*

* Various little bugfixes for problems in rare cases. (Same title but different posting, e.g.)
* Moved the settings to the "Plugins"-Category in dashboard. Feels better there since 2.7, i think.
* Added a direct link to the settings from the plugins page in dashboard.

*Version 0.4.4*

* You can now choose whether you want bbSigPress only to keep your LATEST post in your signatures or if it should put an older post back into the signatures when you save it. (Reason: When I corrected some typos in an older article, bbSigPress would always have put them into my signatures, which I didn't want to happen.)

*Version 0.4.5*

* Fixes a problem with a recent phpBB update.

*Version 0.4.6*

* Just some typos and higher vBulletin compability.

*Version 0.4.7*

* Discovered a rarely occuring bug while working on the engine for IPB, which will be added in 0.5

*Version 0.4.8*

* Updated to WP 2.7.1 and fixed a WPMU bug