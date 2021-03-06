# SimpleReservation

![Docs](https://img.shields.io/badge/version-1.0.0-blue.svg?style=flat-square)

SimpleReservation is a WordPress that makes it easy to manage rooms and their reservations.

## Installation

You can either install the plugin using FTP or clone the repository using git. Later at some point, the plugin will be also available in the official WordPress Plugin Repository.

### FTP

1. Download [the zip file](https://github.com/Lanseuo/simple-reservation/archive/master.zip) and extract it
2. Put the extracted folder in the plugin directory of your WordPress installation (`wp-content/plugins`)
3. Rename the folder from `simple-reservation-master` to `simple-reservation`
4. Go to the plugin area of your WordPress admin space and activate the plugin

### Git Clone

```
cd .../wp-content/plugins
git clone https://github.com/Lanseuo/simple-reservation.git
cd simple-reservation
composer install
npm install
gulp
```

Go to the plugin area of your WordPress admin space and activate the plugin

## Usage

There are two parts of the plugin

- the admin area
- and the frontend

### Admin

In the admin space you will find a menu item called _SimpleResevation_. Here you can find the settings of the plugin and you can manage the rooms that can be reserved.

### Frontend

To show the reservation site to the user, you have to add the following shortcode to a post or a page:

```
[simplereservation]
```

## Development

```
composer install
npm install 
```

during development

```
gulp watch
```

## Meta

Lucas Hild - [https://lucas-hild.de](https://lucas-hild.de)  
This project is licensed under the MIT License - see the LICENSE file for details
