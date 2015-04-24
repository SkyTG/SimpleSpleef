SimpleSpleef
============

A simple Spleef Plugin for your PocketMine-MP Server

#Features

* Create infinite arenas
* Automatically handle them. No need for Admins to be online
* Customisable Settings
* Rewards for a winning Player

#Admin Commands

| Description          	| Command                             	| Important                                                                                                         	|
|----------------------	|-------------------------------------	|-------------------------------------------------------------------------------------------------------------------	|
| Create an arena      	| /ss arena create <name>             	| You can only create an arena which does not exist yet. The spawn point of the arena will be your current position 	|
| Set arena floor      	| /ss arena edit floor <arena>        	| You must stand on the floor layer to set the floor correctly                                                      	|
| Enable/Disable arena 	| /ss arena edit state <arena>        	| You must enable an arena after it is created and set up                                                           	|
| Change arena spawn   	| /ss arena edit spawn <arena>        	| Your current location will be used as the arena spawn                                                             	|
| Start/Stop a game    	| /ss arena edit <start/stop> <arena> 	| Useful for testing                                                                                                    |
       
#Player Commands
       
| Description    	| Command              	|
|----------------	|----------------------	|
| Join an arena  	| /spleef join <arena> 	|
| Leave an arena 	| /spleef leave        	|

#Join Signs

Anyone can create a join-sign for an arena. It must have the following format to work
* [Spleef]
* <arenaName>
       