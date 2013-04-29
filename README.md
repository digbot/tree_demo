Tree Demo 
========================

Welcome to Tree Demo. This solution use Symfony 2 and DoctrineExtensions - Tree.
The Thee extension (Doctrine 2) use a nested set implementation in order to store 
tree date in relational database.


1) Installing 
----------------------------------

1.1) First use composer to all vendor libs.
php composer.phar install

1.2) Setup your database settings in /app/config/parameters.yml

1.3) Generate your database with this command:
php app/console doctrine:schema:create --dump-sql

1.4) Optional: Setup you virtual host on apache.


2) Current implementation
----------------------------------
At this time ( version 0.1.0) the following operations are supported:
Add node.
Edit node.
Delete branch (this will remove all sub nodes).
Remove node from tree (only this node).

Important Note: The both reset button will truncate your data from database. 

What's inside?
---------------

Symonfy 2 - standar edition  

DoctrineExtensions library

JQuery 

JsTree 

Twitter Bootstrap
