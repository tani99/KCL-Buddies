# Student Buddy System Management

This is the repository for the team UnintelliJ Major Project. 
A live demonstration version of this project is available at http://kclbuddies.com (root account email: root@kclbuddies.com pass: password123)

## Setup Instructions

Anyone wishing to use this project will need to have a Google Maps API Key, a Microsoft Azure AD Application, and access to an email automation service such as mailgun (if email options are chosen)

The administrator will need to perform the following steps to set up the system:

1. Run command: composer install
1. Copy the .env.example file to a new file .env
1. Run command: php artisan key:generate
1. Run command: php artisan config:clear
1. Run command: php artisan storage:link
1. Open the .env file and change the following:
    1. APP_ENV to production
    1. APP_URL to the URL of the site
    1. The DB fields to connect to the BuddySystem database
    1. The OAUTH_APP_ID, OAUTH_APP_PASSWORD and OAUTH_REDIRECT_URI to the OAuth details for the Microsoft Login system
    1. Fill in the Google Maps Key field for map-based questions
    1. Optionally, for the email system, fill in the MAIL fields. If you would like to disable the email system, please clear the MAIL_HOST field.
1. Run command: php artisan migrate (this creates a default root account with email: no.reply@kcl.ac.uk and password: password123)
1. Create a cron job that runs every 5 minutes and executes the script in the root site directory: php artisan schedule:run. If the server does not have a cron scheduler, install cron or find an alternative program that can schedule executing scripts in a loop.

## APIs/Libraries used in this project

[Microsoft Azure AD v2.0](https://docs.microsoft.com/en-us/azure/active-directory/develop/v2-overview)

[thephpleague OAuth2 client](https://github.com/thephpleague/oauth2-client)

[Microsoft Graph API](https://developer.microsoft.com/en-us/graph)

[Google Maps API](https://developers.google.com/maps/documentation/javascript/get-api-key)

[Owl Carousel](https://owlcarousel2.github.io/OwlCarousel2/)

[SVG Balloon Slider](https://codepen.io/chrisgannon/pen/xweVNM)

[Tweenmax](https://greensock.com/tweenmax)

[Draggable](https://shopify.github.io/draggable/)
