Blog demonstration for DoctrineODMMongoDBServiceProvider
========================================================

This is blog is a demo that shows how to use Mparaiso\Provider\DoctrineODMMongoDBServiceProvider
service provider.

### REQUIREMENTS

+ PHP > 5.3
+ a mongo db database
+ php mongo extension
+ a webserver like Apache on Nginx

### INSTALLATION

The web root for your server is the __web__ folder.

The __temp__ directory must be writable 

Define the following server variables : 

+ ODM_BLOG_DEMO_SERVER  : the mongo connection string
+ ODM_BLOG_DEMO_ENV : either development or production

You need to create a default role : 

+ in the root folder launch the console with a cli to create a new user role : 

        php console  mp:user:role:create user ROLE_USER

### USAGE