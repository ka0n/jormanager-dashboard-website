JORMANAGER DASHBOARD & WEBSITE
======

This is a template for a stake pool website that doubles as a dashboard for [Jormanager](https://bitbucket.org/muamw10/jormanager/src/develop/).

### PREREQUISITS:

* [Jormanager](https://bitbucket.org/muamw10/jormanager/src/develop/)
* PHP w/cURL extension

### NOTES:

You'll need to configure Jormanager to write its JSON files to whatever directory your website lives in. 

### FILES:

* index.php -- The main site.
* css/*.css -- Additional styles and colors. 

### DESCRIPTION:

This is a dashboard / website for a Cardano ADA Stake Pool that uses Jormanager to manage the Jormungandr node. The dashboard gets its data from a variety of sources, your local nodes API, [PoolTool](http://pooltool.io), and Jormanager. I try to present a good overview of the state of the node, pool and network by including as many relevant statistics as possible about each.

I've done my best to comment the code so that its accessable to anybody. If you're running Jormanager already this should be pretty much plug and play. If you're not using Jormanager then this is not going to work for you. Some of the data will still be available, but some wont. 

The CSS framework used is [Materialize CSS](https://materializecss.com/), I also use jQuery to initialize Materialize javascript functions (though you could just use regular javascript if you wanted, thats on you). I also include some Google fonts so feel free to change those to your liking as well. 

The design is mobile friendly and will give all the relevant information that the desktop version does to mobile browsers. 

One last thing, under the contact menu there is no contact form included. You will have to provide that yourself since everyone is different in how they like their forms to be configured, I've left that up to you.  

### EXAMPLE: 

You can see it in action on my site: [Coconut Pool](https://coconutpool.com)

### COPYRIGHT:

* MIT License
* Please fork, fix & update as you feel necessary. 
* Please leave the link to JON_MADE_THIS in the footer.

### DONATIONS:

If you like it, put some ₳ in it: 

DdzFFzCqrhsimosXs37Lz1aGs3pbYjD3CzLuQYUagnTzT3GjK9vvrdxsArDBgHT1BNnGqCht4aMRangofWtr7zD5PA9JvEvr3wz6Jbxs

### CHANGE LOG:

* 022820: Layout: Support for CSS themes introduced. 
* 022720: Layout: Fixed formatting for when no data was available during bootstrap.
* 022020: Bug fix: Epoch was showing wrong number.
