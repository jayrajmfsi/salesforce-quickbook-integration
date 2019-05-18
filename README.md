SalesForce Quickbooks API Integration
========================

The system contains a way for syncing your salesforce data with quickbooks. The user of the application can register and have to provide details needed for connectiing with salesforce and quicbooks, The system is deployed using
heroku having clearDB as an add-on for mysql database.

https://sf-qb-data-integration.herokuapp.com/

First Use Instructions
-----------------------

* Clone the GitHub repo to your computer.
* Add the necessary parameters value in parameters.yml
* Create an account at salesforce to fetch consumer key, secret and redirect_uri.
* Create an account at quickbooks to fetch consumer key, secret and redirect_uri..
* Register using the register route and provide the details for both quickbooks and salesforce.
* Account id has to fetched using a third party app like postman.
* Login and now you can connect to your account and sync their data.
