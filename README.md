# LegoCalc

**LegoCalc** is an web based calculator application for multiple browser based **MMORPG** games. Currently supporting only [StarGateWars](http://www.stargatewars.com/) (where I was once an active player), but it’s popularity and handfulness were always praised amongst players. First version was developed using **Seijuro Hiko**‘s calculations and I added a few of my own. It was called version 2.0, because it was appropriate to credit Seijuro with version 1.0, although it was not officially LegoCalc.

*Later I sketched version 3.0, which was quickly scrapped, because of bad architecture. But the next version (4.0) remained for years, it supported modular-based system for calculations, templates and multi-language system.*

# Details
Platform: **Cross-platform** (*web application*)
Technologies: **(X)HTML**, **CSS**, **PHP**

# Screenshots



# What is this now?

After few moments of reading my old E-mail account and searching through [StarGateWars Forums](http://stargatewars.herebegames.com/), I realised that my calculator is badly missed and people have been looking for alternatives. Well, look no more! This repository contains full source code to the calculator as it was once running at demo.arvystate.net. You are able to use and modify source code and pull requests are very welcome! I am very pleased to finally find time to release this and I hope someone will find time and help with implementing changes for newest game updates, since I do not play anymore.

# Documentation

Below are short instructions on how to use or implement your own modules and translate them. Translations can be done by anyone who can understand the desired language, but module implementations will require basic knowledge of **HTML** and **PHP**.

## Module system

The module system is quite complex. One module is represented by a single directory. Modules cannot interact with each other, common code must be copied (maybe a flaw of the system, but it is what it is). The architecture is loosely based on Model – View – Controller, but a very primitive one. Module is divided into 2 parts – user interaction and code.

The code is in file `functions.php`. It is a simple PHP class, with few basic functions. This is the main module functionality, it is user language independent and contains all the calculations. This file is dynamically loaded by LegoCalc system, when your module is called. That is why it is important, to keep the class name the same to public functions. You can create as many additional functions and class variables as you like. Take a look at few modules to get the idea, the code is commented and easy to understand.

User interaction is stored in language directories. In module pack, only English language is included, meaning all user interaction is stored in en directory. The same file structure is used for all modules, you can change the user interaction structure as you please. But if you want to add module to main application, please use the structure provided, because it is easier for translators to work with consistent structure. HTML source is in file `construct.lpa`, module name and global variables into file `system.php` and module error messages into file `messages.php`. Open each file and it should be pretty self-explanatory.

The code dynamically loads the correct language directory and displays (echoes) the user interface based on user-selected language. After sending the HTML form, the system again redirects the correct input into your module. Based on user input you can then regenerate user interface and display the results.

If you still cannot understand the mechanics of LegoCalc, just open an issue and tag it with question.

## Translations

The translation is fairly simple. All you need is a basic text editor and you can start. You need to translate two parts:
- modules
- main system

The main system is very small, but it is a MUST. Modules you can translate one by one, but you must translate all files (`construct.lpa`, `messages.php` and `system.php`). Be sure to choose correct language identificator – such as en for English, which is used to name all language directories, including the ones in modules.

Be careful, do NOT change any HTML tags or PHP code, only English based text!

# License

**LegoCalc** is available under the **GPL** license. See [LICENSE](https://github.com/arvystate/LegoCalc/blob/master/LICENSE) file for more information.
