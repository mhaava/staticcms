#Static CMS
Simple CMS for your static html templates.
##Run example
###Generate output files
```bash
php Generator.php
```
###Run php local development server
```bash
php -S localhost:80 -t output/
```
###Open in browser
Open http://localhost

Open admin panel http://localhost/admin (credentials in `config.json`)

##Include your website
###Include your files
Place your html files in `/input` folder.

Changeable images must be placed in  `/input/assets/images`

###Tag editable content
Tag your editable text with `cms-editable-text` class.
```html
<div class="cms-editable-text">
  <!-- Text inside is editable -->
</div>
```
And your images with `cms-editable-image` class.
```html
<img class="cms-editable-image" src="" alt="">
<!--Image is replaceable-->
```
###Configure
Modify `config.json` to fit your needs.
```json
{
    "localization": [
        {
            "code": "en",
            "name": "english",
            "default": true
        },
        {
            "code": "et",
            "name": "eesti",
            "default": false
        },
        {
            "code": "ru",
            "name": "pусский",
            "default": false
        }
    ],
    "administrators": [
        {
            "username": "admin",
            "password": "admin"
        }
    ]
}
```
###Generate output files
```bash
php Generator.php
```
###Run PHP local development server
Run local server in `output` folder.
```bash
php -S localhost:80 -t output/
```
###Open in browser
Open http://localhost

Open admin panel http://localhost/admin (credentials in `config.json`)